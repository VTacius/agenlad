<?php

namespace App\Clases;

use App\Clases\Cifrado;

class BaseControlador {

    /**
     * 
     * Obtiene los datos enviados por Json en $origen según $requerimientos
     * @param Array $requerimientos
     * @param Array $origen
     * @return Array
     */
    protected function parsearDatosPeticion($requerimientos, $origen){
        $datos = json_decode($origen, true);
        $resultado = Array();
        foreach ($requerimientos as $clave => $valor) {
            if(array_key_exists($clave, $datos) && !is_null($datos[$clave])){
                 $resultado[$clave] = $datos[$clave];
            } else if (in_array('requerido', $valor['validaciones'])) {
                throw new \Exception("Error Processing Request", 1);
            } 
        }
        return $resultado;
    }

    /**
     * Obtiene parametros de conexión y valores por defecto para LDAP
     * @param Array ldapParams
     * @return List
     */
    protected function obtenerParametros($ldapParams){
        $parametros = new \stdClass();
        $parametros->servidor = $ldapParams['servidor'];
        $parametros->puerto = $ldapParams['puerto'];
        $parametros->base = $ldapParams['base'];
        $parametros->sambaSID = $ldapParams['sambaSID'];
        $parametros->netbiosName = $ldapParams['netbiosName'];
        $parametros->dominioCorreo = $ldapParams['dominioCorreo'];
       
        $credenciales = new \stdClass();
        $credenciales->dn = $ldapParams['dn'];
        $credenciales->password = $ldapParams['password'];

        return [$parametros, $credenciales];
    }

    /**
     * Obtiene usuario y contraseña para conectarse con zimbra
     * @param PDOConection
     * @return Array
     */
    protected function obtenerClavesZimbra($zimbraParams, $conexion, $ipaddress, $tokens){
        $parametros = new \stdClass();

        $consulta = "select creds.firmas, creds.firmaz 
                     from agenldap_token token 
                     left join agenldap_credenciales creds on token.token = creds.token_id 
                     where token.token=:token and token.ipaddress=:ipaddress";

        $contenido = $conexion->exec($consulta, Array('token' => $tokens->usuario, 'ipaddress' => $ipaddress));
        if (\sizeof($contenido) === 0){
            return  false;
        } 

        $firmaz = $contenido[0]['firmaz'];
        $clave = crypt($tokens->maestro, $tokens->usuario);
        $resultado = $this->cifrado->descifrar($firmaz, $clave);
        if(!mb_check_encoding($resultado, 'utf-8')){
            return false;
        }
        $datos = unserialize($resultado);
        
        $parametros->servidor = $zimbraParams['servidor'];
        
        $credenciales = new \stdClass();
        $credenciales->usuario = $datos['usuario'];
        $credenciales->password = $datos['password'];

        return [$parametros, $credenciales];


    }

}