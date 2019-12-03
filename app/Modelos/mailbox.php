<?php
/**
 * Clase para acceder a los atributos del usuario en zimbra
 *
 * @author alortiz
 */

namespace App\Modelos;

use App\Modelos\objetosSoap;

class mailbox extends objetosSoap {
        
    public function __construct($parametros, $credenciales) {
        parent::__construct($parametros, $credenciales);
        
        $this->atributos = Array('company', 'description', 'displayName', 'givenName', 'l', 
            'mail', 'sn', 'telephoneNumber', 'title', 'st', 'zimbraAccountStatus', 
            'zimbraAuthLdapExternalDn', 'zimbraMailStatus' );

        $this->traduccion = Array('o' => 'company', 'ou' => 'l');
        $this->listables = Array('description', 'o', 'ou', 'title', 'telephoneNumber');
    }
    
    public function cuenta($usuario){
        $this->configurarCuenta($usuario);
    }
   
    public function configurarDatos($datos){
        foreach($this->listables as $clave){
            if (array_key_exists($clave, $datos)){
                $clave = array_key_exists($clave, $this->traduccion) ? $this->traduccion[$clave] : $clave;
                $this->entrada[$clave] = $datos[$clave];
            }
        }
    }
    
    public function getCuenta(){
        return $this->cuenta;
    }
    
    function getSt() {
        return $this->cuenta['st'];
    }

    function getOu() {
        return $this->cuenta['l'];
    }

    function getSn() {
        return $this->cuenta['sn'];
    }

    function getTitle() {
        return $this->cuenta['title'];
    }

    function getGivenName() {
        return $this->cuenta['givenName'];
    }

    function getDisplayName() {
        return $this->cuenta['displayName'];
    }

    function getMail(){
        return $this->cuenta['mail'];
    }
    
    function setSt($st) {
        $this->cuenta['st'] = $st;
    }

    function setTitle($title) {
        $this->cuenta['title'] = $title;
    }

    function configurarNombre($nombre, $apellido){
        $this->cuenta['givenName'] = $nombre;
        $this->cuenta['sn'] = $apellido;
        $this->cuenta['displayName'] = $nombre . " " . $apellido;
    }

    public function getZimbraAccountStatus() {
        return $this->cuenta['zimbraAccountStatus'];
    }
    
    public function getZimbraAuthLdapExternalDn(){
        return $this->cuenta['zimbraAuthLdapExternalDn'];
    }
    
    public function getZimbraMailStatus() {
        return $this->cuenta['zimbraMailStatus'];
    }
    
    public function setZimbraAccountStatus($zimbraAccountStatus) {
        $this->cuenta['zimbraAccountStatus'] = $zimbraAccountStatus;
    }
    
    public function setZimbraMailStatus($zimbraMailStatus) {
        return $this->cuenta['zimbraMailStatus'] = $zimbraMailStatus;
    }
}
