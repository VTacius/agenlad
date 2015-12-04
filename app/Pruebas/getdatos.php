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
class getdatos extends \Clases\sesion{
    public function __construct() {
        parent::__construct();
        $this->pagina = "useradd";
    }

    public function display(){
        print "Probaremos la busqueda de uidNumber";
        print "<pre>";
        print_r($this->listarAtributosUsuarios("uidNumber"));
        print "</pre>";
    }

/**
    private function listarAtributosUsuarios($atributo){
        print "Aún usa el search de su propia tipo, por lo que restringe su búsqueda solo para los objetos ldap que tengan el mismo objectClass";
        $claves = $this->getClaves();
        $usuarios = new \Modelos\userPosix($claves['dn'], $claves['pswd'], 'central' );
        $filtro =array($atributo => '*');
        $datos = $usuarios->search($filtro, false, 'dc=sv', 0);
        $lista = array();
        foreach ($datos as $valor) {
            $lista[] = $valor[$atributo];
        }
        return $lista;
    }
*/
    /**
     * Para solucionar el problema que tenía, dado el cual sólo estaba buscando los id de los objetos usuarios, 
     * uso getDatos desde Acceso\ldapAccess, es decir, bastante primitivo
     * y me obliga a agregar el método público setBaseLdapAccess en la misma clase
     * para poder manipularlo desde tan lejos 
     */
    private function listarAtributosUsuarios($atributo){
        $claves = $this->getClaves();
        $usuarios = new \Modelos\userPosix($claves['dn'], $claves['pswd'], 'central' );
        $filtro = "$atributo=*";
        // TODO: No se preveé que esta aplicación funcione en una forma donde parametrizar esto sea necesario, 
        // pero que quede constancia del la necesidad de parametrizar esto
        $usuarios->setBaseLdapAccess("dc=salud,dc=gob,dc=sv");
        // "Buscamos sobre toda la creacion de LDAP";
        $datos = $usuarios->getDatos($filtro, array($atributo), 0);
        $lista = array();
        foreach ($datos as $valor) {
            $lista[] = $valor[$atributo];
        }
        return $lista;
    }
}
