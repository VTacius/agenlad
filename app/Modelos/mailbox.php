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
        $this->atributos = array('zimbraaccountstatus', 'zimbramailstatus');
    }
    
    public function cuenta($usuario){
        $this->configurarDatos($usuario);
    }


    public function getZimbraAccountStatus() {
        return $this->cuenta['zimbraaccountstatus'];
    }

    
    public function getZimbraMailStatus() {
        return $this->cuenta['zimbramailstatus'];
    }
    
}
