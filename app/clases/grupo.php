<?php
namespace clases;

/**
 * Description of grupo
 *
 * @author alortiz
 */
class grupo extends \clases\controlLDAP{
    /** @var array 
     * Arreglo de los atributos del usuario. Recuerde que DN no se considera atributo
     */
    private $atributos = array('cn','gidNumber','description','objectClass');
    /** @var string Indice del modelo*/
    private $cn;
    /** @var strin Indice alterno del modelo */
    private $gidNumber;
    /** @var array */
    private $grupo = array();
    
    public function __construct($rdnLDAP, $passLDAP) {
        parent::__construct($rdnLDAP, $passLDAP);
    }
    
    public function setcn($cn){
        $this->cn = $cn;
        $filtro = "(&(cn=$this->cn)(objectClass=posixGroup))";
        $this->grupo = $this->getDatos($filtro, $this->atributos)[0];
    }
    
    public function getCn(){
        return $this->grupo['cn'];
    }
    
    public function getGidNumber() {
        return $this->grupo['gidNumber'];
    }

    public function setGidNumber($gidNumber) {
        $this->gidNumber = $gidNumber;
        $filtro = "(&(gidnumber=$this->gidNumber)(objectClass=posixGroup))";
        $this->grupo = $this->getDatos($filtro, $this->atributos)[0];
    }


    
}
