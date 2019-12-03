<?php
/**
 * Description of grupo
 *
 * @author alortiz
 */

namespace App\Modelos;

use App\Modelos\objectosldap;

class grupoSamba extends objectosldap {
    
    public function __construct($conexion) {
        parent::__construct($conexion);
        $this->objeto='posixGroup';
        $this->atributos = array('cn','displayName','gidNumber','memberUid','objectClass','sambaGroupType','sambaSID');
    }
    
    public function getSambaSID() {
        return $this->entrada['sambaSID'];
    }

    public function setSambaSID($sambaSID) {
        $this->configurarEntrada('sambaSID', $sambaSID);
    }
        
    public function setCn($cn){
        $this->configurarEntrada('cn', $cn);
    }
    
    public function getCn(){
        return $this->entrada['cn'];
    }
    
    public function getGidNumber() {
        return $this->entrada['gidNumber'];
    }

    public function setGidNumber($gidNumber) {
        $this->configurarEntrada('gidNumber', $gidNumber);
    }
    
    

    
}
