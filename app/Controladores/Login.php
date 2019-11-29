<?php
/**
 * Controlador para el manejo de sesión
 * 
 * @version 0.2
 * @author alortiz
 */

namespace App\Controladores;

use App\Clases\BaseControlador;
use App\Clases\Authentication;

class Login extends BaseControlador {
    
    /**
     * Una vez logueado, se borran todos los intentos anteriores del usuario
     * @param \PDOConection $conexion
     * @param String $usuario
     */
    protected function logueado($conexion, $usuario){
        $cmds = 'DELETE FROM agenldap_intentos WHERE usuario=:usuario';
        $args = array('usuario' => $usuario);
        $conexion->exec($cmds, $args);
    }
    
    /**
     * El usuario es administrador o no
     * @param \PDOConection $conexion
     * @param string $usuario
     * @return Array
     */
    protected function obtenerBandera($conexion, $usuario){
        $default = Array('titulo' => 'Usuario', 'rol' => 'usuario');
        
        $cmds = 'SELECT titulo, usuario.rol, dominio FROM agenldap_usuarios as usuario JOIN agenldap_roles as rol ON usuario.rol=rol.rol WHERE usuario=:usuario';
        $args = array('usuario'=>$usuario);
        $resultado = $conexion->exec($cmds, $args);
        return $conexion->count() === 0 ? $default : $resultado[0];
    }

    /**
     * Inserta un intento de logueo una vez el usuario se ha equivocado
     * @param \PDOConection $conexion
     * @param String $usuario
     */
    protected function intentoUsuario($conexion, $usuario){
        $cmds = "INSERT INTO agenldap_intentos (usuario) VALUES(:usuario)";
        $args = array('usuario' => $usuario);
        $conexion->exec($cmds, $args);
    }

    /**
     * Si el usuario tiene más de 4 intentos intentos en menos de un día, le impide seguir intentando
     * @param \PDOConection $conexion
     * @param String $usuario
     * @return Boolean
     */
    protected function comprobarBloqueo($conexion, $usuario){
        $cmds = "SELECT usuario from agenldap_intentos WHERE DATE_PART('days', NOW() - estampa) <= 1 AND usuario=:usuario";
        $args = array('usuario' => $usuario);
        $resultado = $conexion->exec($cmds, $args);
        
        return $conexion->count() >= 4;
    }
    
    public function beforeRoute($index){
        $datos = json_decode($index['BODY']);
        if(json_last_error() > 0){
            $index->error(400); 
        }
    }
   
    /**
     * Controlador para el proceso de autenticacion
     * TODO: Quizá puedas meter después los datos ('uid', 'gecos', 'mail', 'o','ou', 'title','gidnumber')
     * si bien nunca los usaste
     */
    public function autenticar($index){
        $requerimientos = Array(
            'usuario' => Array('validaciones' => ['requerido']),
            'password' => Array('validaciones' => ['requerido']),
        );
        
        try {
            $resultado = $this->parsearDatosPeticion($requerimientos, $index['BODY']);
            $usuario = $resultado['usuario'];
            $password = $resultado['password'];
        } catch (\Exception $e){
            $index->error(400);
        }

        if($this->comprobarBloqueo($index->get('dbconexion'), $usuario)){ 
            $index->error(401);
        }

        $ldapParams = $index->get('ldap');
        list($parametros, $credenciales) = $this->obtenerParametros($index->get('ldap'));
        
        $login = new Authentication($parametros, $credenciales);
        if($login->login($usuario, $password)) {
            $this->logueado($index->get('dbconexion'), $usuario);
            print json_encode($this->obtenerBandera($index->get('dbconexion'), $usuario));
        } else {
            $this->intentoUsuario($index->get('dbconexion'), $usuario);
            $index->error(401);
        }
    }
    
}
