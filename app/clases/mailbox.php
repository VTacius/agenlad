<?php
namespace clases;
/**
 * Clase para acceder a los atributos del usuario en zimbra
 *
 * @author alortiz
 */
class mailbox extends \clases\controlLDAP {
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
    private $mailbox;

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
        $this->mailbox = $this->getDatos($filtro, $this->atributos)[0];
    }

        
    public function getZimbraAccountStatus() {
        return isset($this->mailbox['zimbraaccountstatus'])? $this->mailbox['zimbraaccountstatus'] : '-' ;
    }

    public function setZimbraAccountStatus($zimbraAccountStatus) {
        $this->zimbraAccountStatus = $zimbraAccountStatus;
    }
    
    public function getZimbraMailStatus() {
        return isset($this->mailbox['zimbramailstatus'])? $this->mailbox['zimbramailstatus'] : '-' ;
    }

    public function setZimbraMailStatus($zimbraMailStatus) {
        $this->zimbraMailStatus = $zimbraMailStatus;
    }





    
    
    
    
}
