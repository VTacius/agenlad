<?php
/**
 * Controlador para listado/detalles de usuarios
 * 
 * @version 0.2
 * @author alortiz
 */

namespace App\Controladores\usuario;

use App\Acceso\ldapAccess;
use App\Modelos\userSamba;
use App\Modelos\userPosix;
use App\Modelos\grupoSamba;
use App\Clases\BaseControladorAuth;
use App\Clases\Cifrado;

class Listado extends BaseControladorAuth {
    
    protected $error = array();
    protected $mensaje = array();
    
    /**
     * Busca que el atributo O del usuario corresponda con un establecimiento válido
     * TODO: Basáte en la búsqueda del helpers correspondiente
     */
    protected function comprobarOficina($oficina){
        $busqueda = "select id, nombre as label from ctl_establecimiento where activo is True and ";
        $busqueda .= is_numeric($establecimiento) ? "id=:establecimiento" : "nombre ilike ('%'|| :establecimiento || '%')"; 
        if (empty($establecimiento)){
            $busqueda = "estavlecimiento que no existe";
        }
        try{
            $base = $this->index->get('dbconexion');
            $entrada = $base->exec($busqueda, array(':establecimiento' => $establecimiento));
            return $entrada[0];
        }catch (\PDOException $e){
            // Lo pones en el mensaje de error a enviar al servidor
            $this->mensaje[] = array("codigo" => "danger", 'mensaje' => 'Error agregando datos administrativos. Revise los mensajes asociados');
            //$this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: " . $e->getMessage() );
            $this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: ");
        }
    }
    
    /**
     * Busca que el atributo O del usuario corresponda con un establecimiento válido
     * @param \PDOConection $conexion
     * @param String $establecimiento
     * @return Array
     */
    protected function comprobarEstablecimiento($conexion, $establecimiento){
        $busqueda = "select id, nombre as label from ctl_establecimiento where activo is True and ";
        $busqueda .= is_numeric($establecimiento) ? "id=:establecimiento" : "nombre ilike ('%'|| :establecimiento || '%')"; 
        try{
            $entrada = $conexion->exec($busqueda, array(':establecimiento' => $establecimiento));
            return $entrada[0];
        }catch (\PDOException $e){
            // Lo pones en el mensaje de error a enviar al servidor
            $this->mensaje[] = array("codigo" => "danger", 'mensaje' => 'Error agregando datos administrativos. Revise los mensajes asociados');
            //$this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: " . $e->getMessage() );
            $this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: ");
        }
    }
    
    /**
     * Devuelve los datos de usuario
     * @param Array $ldapParams
     * @param String $userName
     * @return Array
     */
    protected function usuario($ldapParams, $userName){
        
        list($parametros, $credenciales) = $this->obtenerParametros($ldapParams);
        $cifrador = new Cifrado();
        $conexion = new ldapAccess($parametros, $credenciales);
        $usuario = new userSamba($conexion, $cifrador);
        $usuario->setUid($userName);
        
        // TODO: Una copia descarada en usermodControl
       
        $mensaje = "";
        $datos = $usuario->getEntrada();
        if (empty($datos['dn'])) {
            $mensaje = "Usuario $userName no existe";
            return Array('mensaje' => $mensaje);
        }         
        
        $datos = $usuario->getEntrada();
        $datos['userPassword'] = $usuario->getuserPassword();

        return $datos;
    }
    
    /**
     * Obtiene el grupo al cual pertenece el usuario
     * @param Array $ldapParams
     * @param String $group
     */
    protected function grupo($ldapParams, $groupName){
        list($parametros, $credenciales) = $this->obtenerParametros($ldapParams);
        $conexion = new ldapAccess($parametros, $credenciales);
        $grupo = new grupoSamba($conexion);
        $grupo->setGidNumber($groupName);
        
        return $grupo->getCn();
    }  
    
    /**
     * Obtiene los datos del buzón de correos para el usuario
     * Espera a que $correo sea {empty} para invalidar toda la busqueda si acaso
     * el usuario en cuestion no debe ser modificado por tal administrador
     * @param Array $clavez
     * @param String $correo
     */
    protected function mail($zimbraParams, $conexion, $correo){
        list($parametros, $credenciales) = $this->obtenerClavesZimbra($zimbraParams, $conexion, $this->ipaddress, $this->tokens);
        $mailbox = new \Modelos\mailbox($parametros, $credenciales);
        $mailbox->cuenta($correo);
        
        // Configuramos los datos
        $datos = Array();
        $datos['zimbraMailStatus']= $mailbox->getZimbraMailStatus();
        $datos['zimbraAccountStatus'] = $mailbox->getZimbraAccountStatus();

        return $datos;
    }
    
    
    /**
     * Devuelve los datos administrativos del usuario
     * @param String $usuario
     * @return Array
     */
    protected function obtenerDatosAdministrativos($conexion, $usuario){
        $busqueda = 'select usuario, pregunta, respuesta, fecha_nacimiento, nit, jvs from datos_administrativos where usuario=:usuario';
        try{
            $entrada = $conexion->exec($busqueda, array(':usuario' => $usuario));
            return array('datos' => $entrada);
        }catch (\PDOException $e){
            return array('datos' => Array());
        }
    }
    
    /**
     * Responde a la ruta en GET /usuarios/@usuario
     */
    public function detalles($index, $params){
        $ldapParams = $index->get('ldap');
        $zimbraParams = $index->get('zimbra');
        $username = $params['usuario'];
        
        $usuario = $this->usuario($ldapParams, $username);
        if (array_key_exists('mensaje', $usuario)){
            print json_encode($usuario);
            $index->error(404);
        }

        // TODO: Debería hacerse con el atributo oficina también
        $datos['o'] = $this->comprobarEstablecimiento($index->get('dbconexion'), $usuario['o']);
        
        $datosDB = $this->obtenerDatosAdministrativos($index->get('dbconexion'), $username);
        if (sizeof($datosDB['datos']) > 0){
            $usuario = array_merge($usuario, $datosDB['datos'][0]);
            $date = \DateTime::createFromFormat('Y-d-m', $datosDB['datos'][0]['fecha_nacimiento']);
            $usuario['fecha_nacimiento'] =  $date ? $date->format('d/m/Y') : ''; 
        }
        
        $usuario['grupo'] = $this->grupo($ldapParams, $usuario['gidNumber']);
        
        $datosMail = $this->mail($zimbraParams, $index->get('dbconexion'), $usuario['mail']);

        $usuario = array_merge($usuario, $datosMail);
        
        print json_encode($usuario) . "\n";
    }

    /**
     * Busca en $indice una lista separada por comas que devuelve como un Array,
     * considerando un valor por defecto de no existir
     * @param Array $origen De donde vienen los datos
     * @param String $indice Donde buscar el contenido
     * @return Array
     */
    protected function parsearListado($origen, $indice){
        /** Buscamos en los atributos que nos envian de la peticion */
        if (array_key_exists($indice, $origen)){
            return array_map(
                function($item){
                    return trim($item);
                }, explode(',', $origen[$indice]));
        } else {
            return Array();
        }

    }
    protected function parsearDiccionario($origen, $indice){
        $pf = array_filter(
            explode(",", $origen[$indice]),
            function($item){
                $pos = strpos($item, "="); 
                return  $pos> 0 and !empty(substr($item, $pos + 1));
            });
        
        return array_reduce(
            $pf,
            function($acumulador, $item){
                list($clave, $valor) = explode("=", $item);
                $acumulador[trim($clave)] = trim($valor);
                return $acumulador;
            }, Array());
    }

    public function lista ($index){
        /** Buscamos en los filtros que nos envían de la petición */
        $atributos = $this->parsearListado($index['GET'], 'atributos');
        $atributos = sizeof($atributos) > 0 ? $atributos:  Array('uid','cn','title','o', 'ou','mail', 'telephoneNumber');
        
        $filtros = $this->parsearDiccionario($index['GET'], 'filtros');
        $filtros = sizeof($filtros) > 0 ? $filtros : Array('uid' => "NOT root AND NOT nobody");


        list($parametros, $credenciales) = $this->obtenerParametros($index->get('ldap'));
        $cifrador = new Cifrado();
        $conexion = new ldapAccess($parametros, $credenciales);
        $usuario = new userPosix($conexion, $cifrador);
        print json_encode($usuario->search($filtros, $atributos));
    }
}