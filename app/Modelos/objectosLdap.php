<?php
/**
 * Permite varios operaciones sobre objetos 
 *
 * @author alortiz
 */

namespace Modelos;

class objectosLdap extends \Modelos\controlLDAP{
    /** 
     * Arreglo de los atributos del usuario. Recuerde que DN no se considera atributo
     * @var array 
     */
    protected $atributos = array();
    
    /**
     * El contenido de todo cuanto el usurio puede ser
     * @var array
     */
    protected $entrada = array();
    
    /**
     * 
     * @var clases\cifrado
     */
    protected $hashito;
    
    /**
     *
     * @var string (ObjectClass) 
     */
    protected $objeto;
    
    /**
     * Configura el valor de un elemento cualquiera dentro del árbol LDAP
     * Use para los atributos que no son de búsqueda
     * @param string $atributo
     * @param string $especificacion
     */
    protected function configurarValor($atributo, $especificacion){
        $this->entrada[$atributo] = $especificacion;
    }
    
    /**
     * Configurar atributos único con los cuales es posible buscar 
     * entradas existente dentro del árbol LDAP
     * En caso 
     * @param string $atributo
     * @param string $especificacion
     */
    protected function configurarDatos($atributo, $especificacion){
        $valor = strtolower($atributo);
        $filtro = "(&($valor=$especificacion)(objectClass=$this->objeto))";
        if (empty($this->entrada)) {
            // Si esta vacío, llene el array por primera vez
            $this->entrada = $this->getDatos($filtro, $this->atributos)[0];
            
            foreach ($this->atributos as $attr) {
                $this->entrada[$attr] = isset($this->entrada[$attr])?$this->entrada[$attr]:"{empty}"; 
            }
        }else{
            // Si alguien ya lleno el array, vea que tiene datos que pueda tener
            $this->entrada[$atributo] = $especificacion;
        }
    }
    
    /**
     * Obtiene todas las entradas del árbol LDAP disponibles
     * Es posible pasar un array con los attributos que se necesitan 
     * Recuerde que dn no se considera atributo
     * @param array $attr
     * @return array
     */
    public function getAll( $attr = false ){
        $this->datos = array();
        $atributes = $attr === false ? $this->atributos : $attr;
        $filtro = "(objectClass=$this->objeto)";
        return $this->entrada = $this->getDatos($filtro, $atributes);
    }
    
    /**
     * Realiza la búsqueda en base a un arreglo hash pasado como parametro
     * @param array $search
     * @return array
     */
    public function search( $search){
        $this->datos = array();
        $atributes = array_keys($search);
        $filtro = "(&(objectClass=$this->objeto)";
        foreach($search as $indice => $valor){
            $filtro .= "($indice=$valor)";
        }
        $filtro .= ")";
        return $this->entrada = $this->getDatos($filtro, $atributes);
        
    }
}
