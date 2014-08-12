<?php
/**
 * Clase para acceder a los atributos del usuario en zimbra
 *
 * @author alortiz
 */

namespace Modelos;

class mailbox extends \Modelos\controlLDAP {
    /** @var array 
     * Arreglo de los atributos del usuario. Recuerde que DN no se considera atributo
     */
    private $atributos = array('zimbraaccountstatus', 'zimbramailstatus');
    /** @var string */
    private $uid;
    /** @var string block|enable */
    private $zimbraAccountStatus;
    /** @var string block|active */
    private $zimbraMailStatus;
    /** @var string */
    private $entrada;

    public function __construct($passLDAP) {
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
        $this->uid = $uid;
        $filtro = "uid=$this->uid";
        $this->entrada = $this->getDatos($filtro, $this->atributos)[0];
    }

        
    public function getZimbraAccountStatus() {
        return isset($this->entrada['zimbraaccountstatus'])? $this->entrada['zimbraaccountstatus'] : '-' ;
    }

    public function setZimbraAccountStatus($zimbraAccountStatus) {
        $this->zimbraAccountStatus = $zimbraAccountStatus;
    }
    
    public function getZimbraMailStatus() {
        return isset($this->entrada['zimbramailstatus'])? $this->entrada['zimbramailstatus'] : '-' ;
    }

    public function setZimbraMailStatus($zimbraMailStatus) {
        $this->zimbraMailStatus = $zimbraMailStatus;
    }





    
    
    
    
}
