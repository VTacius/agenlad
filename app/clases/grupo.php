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
    private $atributos = array('cn','displayName','gidNumber','memberUid','objectClass','sambaGroupType','sambaSID');
    
    /** 
     * @var string 
     * Indice del modelo
     */
    private $grupo = array();
    /**
     *
     * @var string (ObjectClass) 
     */
    private $objeto;
    
    public function __construct($rdnLDAP, $passLDAP) {
        parent::__construct($rdnLDAP, $passLDAP);
        $this->objeto='posixGroup';
    }
    
    /**
     * Configurar atributos único con los cuales es posible buscar 
     * entradas existente dentro del árbol LDAP
     * En caso 
     * @param string $atributo
     * @param string $especificacion
     */
    private function configurarDatos($atributo, $especificacion){
        $valor = strtolower($atributo);
        $filtro = "(&($valor=$especificacion)(objectClass=$this->objeto))";
        if (empty($this->grupo)) {
            // Si esta vacío, llene el array por primera vez
            $this->grupo = $this->getDatos($filtro, $this->atributos)[0];
            
            foreach ($this->atributos as $attr) {
                $this->grupo[$attr] = isset($this->grupo[$attr])?$this->grupo[$attr]:"--"; 
            }
        }else{
            // Si alguien ya lleno el array, vea que tiene datos que pueda tener
            $this->grupo[$atributo] = $especificacion;
        }
    }
    
    public function getSambaSID() {
        return $this->grupo['sambaSID'];
    }

    public function setSambaSID($sambaSID) {
        $this->configurarDatos('sambaSID', $sambaSID);
    }

        
    public function setcn($cn){
        $this->configurarDatos('cn', $cn);
    }
    
    public function getCn(){
        return $this->grupo['cn'];
    }
    
    public function getGidNumber() {
        return $this->grupo['gidNumber'];
    }

    public function setGidNumber($gidNumber) {
        $this->configurarDatos('gidNumber', $gidNumber);
    }


    
}
