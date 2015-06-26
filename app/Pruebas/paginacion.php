<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pruebas;

/**
 * Description of paginacion
 *
 * @author vtacius
 */
class paginacion {
    protected $conexion;
    public function display(){
        echo "Talvez pagine algo, no lo sé aún";
        $host = "192.168.2.19";
        $port = "389";
        
        $dn_usuario = "cn=admin,dc=hacienda,dc=gob,dc=sv";
        $password = "lector_ldap_hacienda";
        
        
        $base = "dc=sv";
        $filtro = "(objectClass=*)";
        $attr = array('uid');
        
        $this->conexion = ldap_connect($host, $port);
        
        ldap_set_option($this->conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->conexion, LDAP_OPT_NETWORK_TIMEOUT, 2);
        ldap_bind($this->conexion, $dn_usuario, $password);
        
        $pageSize = 10;

        $cookie = '';
        
        $busqueda  = ldap_search($this->conexion, $base, $filtro, $attr);
        $total = ldap_count_entries($this->conexion, $busqueda);
        
        $paginas = floor($total / $pageSize);

        
//        for ($index = 0; $index <= $paginas; $index++) {
//            $cookie = $this->busquedaPaginada($cookie);
//        }
        
            $cookie1 = $this->busquedaPaginada($cookie);
            $cookie2 = $this->busquedaPaginada($cookie1);
            $cookie3 = $this->busquedaPaginada($cookie2);
            $cookie3 = $this->busquedaPaginada($cookie1);
       
    }
        
        public function busquedaPaginada($cookie) {
            $pageSize = 10;
            $base = "dc=sv";
            $filtro = "(objectClass=*)";
            $attr = array('uid');
            ldap_control_paged_result($this->conexion, $pageSize, true, $cookie);
            $busqueda  = ldap_search($this->conexion, $base, $filtro, $attr);
            $entradas = ldap_get_entries($this->conexion, $busqueda);
            ldap_control_paged_result_response($this->conexion, $busqueda, $cookie);

            foreach ($entradas as $e) {
                echo $e['dn'] . PHP_EOL;
                echo "<br>";
            }
            
            return $cookie;
        }
}
