<?php

namespace App\Controladores\usuario;

use App\Acceso\ldapAccess;
use App\Modelos\userSamba;
use App\Modelos\userPosix;
use App\Modelos\grupoSamba;
use App\Modelos\mailbox;
use App\Modelos\DatosAdministrativos; 
use App\Clases\BaseControladorAuth;
use App\Clases\Cifrado;

class Actualizacion extends BaseControladorAuth { 
    
    /**
     * Lista los grupos a los que pertenece el usuario
     * @param string $base
     * @param string $memberUid
     * @return array
     */
    private function listarGruposUsuarios($base, $memberUid){
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $search = array('memberUid'=>$memberUid);   
        $resultado = $grupo->search($search, array('cn'), $base);
        $grupos = array();
        foreach ($resultado as $value) {
            if  (array_key_exists('cn', $value)){
                array_push($grupos, $value['cn']);
            }
        }
        return $grupos;
    }
    
    /**
     * Si $grupo (De la lista de grupos enviados desde el formulario) no existe 
     * en $gruposActuales (De la lista de actuales), entonces agregamos $uid a 
     * ese grupo
     * @param string $uid
     * @param string $grupo
     * @param array $gruposActuales
     * @param array $claves
     */
    private function anadirGruposAdicionales($uid, $grupo, $gruposActuales, $claves){
        if(!in_array($grupo, $gruposActuales)){
            $this->agregarEnGrupo($uid, $grupo, $claves);
        }
    }
    
    /**
     * Si $grupo (De la lista de grupos actuales) no existe en $usuarioGrupos 
     * (De la lista de grupos enviados desde el formulario), entonces removemos 
     * a $uid de ese grupo
     * @param string $uid
     * @param string $grupo
     * @param array $usuarioGrupos
     * @param array $claves
     */
    private function removerGruposAdicionales($uid, $grupo, $usuarioGrupos, $claves){
        if(!in_array($grupo, $usuarioGrupos)){
            $valores['memberuid'] = $uid;
            $grupu = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
            $grupu->setCn($grupo);

            if ($grupu->removerAtributos($grupu->getDNEntrada(), $valores)) {
                $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Eliminado $uid de ". $grupu->getDNEntrada());
            }else{
                $this->error[] = $grupu->getErrorLdap();
                $this->mensaje[] = array("codigo" => "danger", 'mensaje' => "Error eliminando $uid en ".  $grupu->getDNEntrada() . ". Revise los mensajes asociados ");
            }
                
        }
    }

    private function agregarEnGrupo($uid, $grupo, $claves){
            $valores['memberuid'] = $uid;
            $grupu = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
            $grupu->setCn($grupo);

            if ($grupu->agregarAtributos($grupu->getDNEntrada(), $valores)) {
                $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Agregado $uid de ". $grupu->getDNEntrada());
            }else{
                $this->error[] = $grupu->getErrorLdap();
                $this->mensaje[] = array("codigo" => "danger", 'mensaje' => "Error agregando $uid en ".  $grupu->getDNEntrada() . ". Revise los mensajes asociados ");
            }
    }

    /**
     * 
     * configurar como los nuevos grupos adicionales del usuario
     * @param array $usuarioGrupos Los grupos que el formulario nos envia
     * @param string $usuario
     * @param array $claves
     * @return type
     */
    private function modificarGruposAdicionales($usuarioGrupos, $usuario, $claves){
        
        $uid = $usuario->getUid();
        
        $gruposActuales = $this->listarGruposUsuarios($usuario->getDNBase(), $usuario->getUid());
        
        //Agrego el grupo principal a la lista
        $principal = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
        $principal->setGidNumber($usuario->getGidNumber());
        $grupoPrincipal = $principal->getCn();
        if (array_search($grupoPrincipal, $usuarioGrupos) === false || array_search($grupoPrincipal, $gruposActuales) === false) {
            array_push($usuarioGrupos, $grupoPrincipal);
        }
        
        foreach ($usuarioGrupos as $grupo) {
            $this->anadirGruposAdicionales($usuario->getUid(), $grupo, $gruposActuales, $claves);
        }
        foreach ($gruposActuales as $grupo) {
            $this->removerGruposAdicionales($uid, $grupo, $usuarioGrupos, $claves);
        }
    }
    
    protected function modificarAtributosAdministrativos($conexion, $userName, $datos){
        $dateador = new DatosAdministrativos($conexion);
        $dateador->dateador($userName, $datos);
    }

    /**
     * Configura los valores de $nombre (givenName) y $apellido ($sn) si alguno de ellos esta presenta en la peticion
     * @param Array $dn 
     * @param Array $dv
     * @return Array
     */
    private function obtenerParametrosNombre($dn, $dv){
        if (!array_key_exists('givenName', $dn) && !array_key_exists('sn', $dn)){
            return Array();
        } else {
            $nombre = (array_key_exists('givenName', $dn)) ? $dn['givenName'] : $dv['givenName']; 
            $apellido = (array_key_exists('sn', $dn)) ? $dn['sn'] : $dv['sn']; 
            
            return Array('nombre' => $nombre, 'apellido' => $apellido);
        }
    }

    /**
     * Actualizamos los atributos de un usuario con $userName con $datos
     * @param String $userName
     * @param stdClass $ldapParams
     * @param Array $datos
     * @return Boolean
     */
    protected function modificarUsuarioSamba($userName, $ldapParams, $datos){

        list($parametros, $credenciales) = $this->obtenerParametros($ldapParams);
        $cifrador = new Cifrado();
        $conexion = new ldapAccess($parametros, $credenciales);
        $usuario = new userSamba($parametros, $conexion, $cifrador);
        
        $usuario->setUid($userName);
        if (sizeof($usuario->getEntrada()) === 0){
            return 5;
        }
        
        $cn = $this->obtenerParametrosNombre($datos, $usuario->getEntrada());
        if (sizeof($cn) === 2){
            $usuario->configurarNombre($cn['nombre'], $cn['apellido']);
        }
       
        if (array_key_exists('userPassword', $datos)){
            $usuario->setUserPassword($datos['userPassword']);
        }

        $usuario->configurarDatos($datos);
        
        $usuario->actualizarEntrada();
        
        return $usuario->getMail();
       
    }
   
    protected function modificarUsuarioZimbra($correo, $zimbraParams, $conexion, $datos){
        list($parametros, $credenciales) = $this->obtenerClavesZimbra($zimbraParams, $conexion, $this->ipaddress, $this->tokens);
        $mailbox = new mailbox($parametros, $credenciales);
        
        $mailbox->cuenta($correo);
        if ($mailbox->getMail() === "{empty}") {
            return 5; 
        }
        
        $cn = $this->obtenerParametrosNombre($datos, $mailbox->getCuenta());
        if (sizeof($cn) === 2){
            $mailbox->configurarNombre($cn['nombre'], $cn['apellido']);
        } 
        
        $mailbox->configurarDatos($datos);
        
        if (array_key_exists('zimbraAccountStatus', $datos) and in_array($datos['zimbraAccountStatus'], Array('active', 'locked'))){
            $mailbox->setZimbraAccountStatus($datos['zimbraAccountStatus']);
        } 
        
        if (array_key_exists('zimbraMailStatus', $datos) and in_array($datos['zimbraMailStatus'], Array('enabled', 'disabled'))){
            $mailbox->setZimbraMailStatus($datos['zimbraMailStatus']);
        } 
        
        // TODO: Tenés que hacer que este metodo se parezca al de usuario
        // NOTA: Posiblemente no sea del todo necesario si te fijas en el otro de 
        //  allá abajo en modificarEstado Zimbra
        $mailbox->actualizarEntrada();
        $mensaje = $mailbox->getErrorSoap();
        
        return sizeof($mensaje) === 0;
      
    }
    
    private function requerir(){
        return Array(
            'fecha_nacimiento' => Array('validaciones' => []), 
            'gidNumber' => Array('validaciones' => []),
            'givenName' => Array('validaciones' => []),
            'jvs' => Array('validaciones' => []), 
            'nit' => Array('validaciones' => []),
            'o' => Array('validaciones' => []),
            'ou' => Array('validaciones' => []),
            'pregunta' => Array('validaciones' => []), 
            'respuesta' => Array('validaciones' => []), 
            'sn' => Array('validaciones' => []),
            'telephoneNumber' => Array('validaciones' => []),
            'title' => Array('validaciones' => []),
            'uid' => Array('validaciones' => []),
            'userPassword' => Array('validaciones' => []),
            'zimbraAccountStatus' => Array('validaciones' => []),
            'zimbraMailStatus' => Array('validaciones' => []),

        );

    }

    public function modificarUsuario($index, $params){
        try {
            $requerimientos = $this->requerir();
            $datos = $this->parsearDatosPeticion($requerimientos, $index['BODY']);
        } catch (\Exception $e){
            $index->error(400);
        }
        
        $datos['description'] = "USERMODWEBADMIN";
       
        $ldapParams = $index->get('ldap');
        
        $username = $params['usuario'];
        
        // Modificamos los atributos Samba/Posix del usuario
        $correo = $this->modificarUsuarioSamba($username, $ldapParams, $datos);
        if ($resultado === 5){
            $index->error(404);
        } else if ($resultado === false) {
            $index->error(500);
        }
        
        $correo = "alortiz@salud.gob.sv";
        $zimbraParams = $index->get('zimbra');
        $resultado = $this->modificarUsuarioZimbra($correo, $zimbraParams, $index->get('dbconexion'), $datos);
        
        $this->modificarAtributosAdministrativos($index->get('dbconexion'), $username, $datos);
    }
    
}
