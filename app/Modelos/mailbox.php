<?php
/**
 * Clase para acceder a los atributos del usuario en zimbra
 *
 * @author alortiz
 */

namespace Modelos;

class mailbox extends \Modelos\objetosSoap {
        
    public function __construct($administrador, $password) {
        parent::__construct($administrador, $password);
        // Agregados atributos como description
        $this->atributos = array('zimbraAccountStatus', 'zimbraMailStatus', 
            'zimbraAuthLdapExternalDn', 'company ','mail', 'l', 'sn', 'title', 
            'givenName', 'displayName', 'telephoneNumber', 'description', 'st');
    }
    
    public function cuenta($usuario){
        $this->configurarDatos($usuario);
    }
    
    function getCompany() {
        return $this->cuenta['company'];
    }

    function getDescription() {
        return $this->cuenta['description'];
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

    function getTelephoneNumber() {
        return $this->cuenta['telephoneNumber'];
    }
    
    function getMail(){
        return $this->cuenta['mail'];
    }
    
    function setCompany($company) {
        $this->cuenta['company'] = $company;
    }

    function setDescription($description) {
        $this->cuenta['description'] = $description;
    }

    function setSt($st) {
        $this->cuenta['st'] = $st;
    }

    function setOu($ou) {
        $this->cuenta['l'] = $ou;
    }

    function setTitle($title) {
        $this->cuenta['title'] = $title;
    }

    function configuraNombre($nombre, $apellido){
        $this->cuenta['givenName'] = $nombre;
        $this->cuenta['sn'] = $apellido;
        $this->cuenta['displayName'] = $nombre . " " . $apellido;
    }

    function setTelephoneNumber($telephoneNumber) {
        $this->cuenta['telephoneNumber'] = $telephoneNumber;
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
