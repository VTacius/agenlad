<?php
/**
 * Description of grupo
 *
 * @author alortiz
 */

namespace Modelos;

class grupoSamba extends \Modelos\objectosLdap{
    
    public function __construct($rdnLDAP, $passLDAP) {
        parent::__construct($rdnLDAP, $passLDAP);
        $this->objeto='posixGroup';
        $this->atributos = array('cn','displayName','gidNumber','memberUid','objectClass','sambaGroupType','sambaSID');
    }
    
    public function getSambaSID() {
        return $this->entrada['sambaSID'];
    }

    public function setSambaSID($sambaSID) {
        $this->configurarDatos('sambaSID', $sambaSID);
    }
        
    public function setcn($cn){
        $this->configurarDatos('cn', $cn);
    }
    
    public function getCn(){
        return $this->entrada['cn'];
    }
    
    public function getGidNumber() {
        return $this->entrada['gidNumber'];
    }

    public function setGidNumber($gidNumber) {
        $this->configurarDatos('gidNumber', $gidNumber);
    }
    
    

    
}
