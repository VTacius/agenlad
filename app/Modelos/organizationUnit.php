<?php
namespace Modelos;

class organizationUnit extends \Modelos\objectosLdap{
    
    public function __construct($rdnLDAP, $passLDAP) {
        parent::__construct($rdnLDAP, $passLDAP);
        $this->objeto='organizationalUnit';
        $this->atributos = array('objectClass', 'ou');
    }
    
    public function getOu(){
        return $this->entrada['ou'];
    }
    
    public function setOu($ou){
        $this->configurarDatos('ou', $ou);
    }

}
