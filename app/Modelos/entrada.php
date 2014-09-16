<?php
/**
 * Description of entrada
 *
 * @author alortiz
 */

namespace Modelos;

class entrada extends \Modelos\controlLDAP{
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
                $this->entrada[$attr] = isset($this->entrada[$attr])?$this->entrada[$attr]:"$attr: {empty}"; 
            }
        }else{
            // Si alguien ya lleno el array, vea que tiene datos que pueda tener
            $this->entrada[$atributo] = $especificacion;
        }
    }
}
