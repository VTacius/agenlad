<?php
/**
 * Clase para acceder a los atributos del usuario en zimbra
 *
 * @author alortiz
 */

namespace Modelos;

class mailbox extends \Modelos\entrada {
    public function __construct($passLDAP) {
        // Configuramos para usar \Modelos\entrada
        $this->objeto='*';
        $this->atributos = array('zimbraaccountstatus', 'zimbramailstatus');
        
        // Demás valores
        $server = "zserver";
        $puerto = "zpuerto";
        $base = "zbase";
        $index = \Base::instance();
        $rdnLDAP = $index->get('lectorzimbra');
        parent::__construct($rdnLDAP, $passLDAP, $server, $puerto, $base);
    }
    
    public function getUid() {
        return $this->uid;
    }

    public function setUid($uid) {
        $this->configurarDatos('uid', $uid);
    }

        
    public function getZimbraAccountStatus() {
        return $this->entrada['zimbraaccountstatus'];
    }

    public function setZimbraAccountStatus($zimbraAccountStatus) {
        $this->zimbraAccountStatus = $zimbraAccountStatus;
    }
    
    public function getZimbraMailStatus() {
        return $this->entrada['zimbramailstatus'];
    }

    public function setZimbraMailStatus($zimbraMailStatus) {
        $this->zimbraMailStatus = $zimbraMailStatus;
    }





    
    
    
    
}
