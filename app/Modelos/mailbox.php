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
        $this->atributos = array('zimbraAccountStatus', 'zimbraMailStatus', 'zimbraAuthLdapExternalDn');
    }
    
    public function cuenta($usuario){
        $this->configurarDatos($usuario);
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
