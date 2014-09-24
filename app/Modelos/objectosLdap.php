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
     * Se verifica que el valor no esté vacío o que tenga el valor por defecto {empty}
     * @param string $atributo
     * @param string $especificacion
     */
    protected function configurarValor($atributo, $especificacion){
        if (!($especificacion === "" || $especificacion === "{empty}")) {
            $this->entrada[$atributo] = $especificacion;
        }
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
    public function getAll( $attr = false, $base = false){
        if ($base) {
            $this->base =  $base;
        }
        $atributes = $attr === false ? $this->atributos : $attr;
        $filtro = "(objectClass=$this->objeto)";
        return $this->entrada = $this->getDatos($filtro, $atributes);
    }
    
    /**
     * Realiza la búsqueda en base a un arreglo hash pasado como parametro
     * @param array $search
     * @param array $atributes
     * @param boolean|string $base
     * @return array
     */
    
    public function search( $search, $atributes = false, $base = false, $filtro = false){
        if ($base == false) {
            $this->base =  $base;
        }
        $this->datos = array();
        if ($atributes == false){
            $attr = array_keys($search);
        }else{
            $attr = array_merge(array_keys($search), $atributes);
        }
        if (!$filtro){
            $filtro = "(&(objectClass=$this->objeto)";
            foreach($search as $indice => $valor){
                $filtro .= "($indice=$valor)";
            }
            $filtro .= ")";
        }
        return $this->entrada = $this->getDatos($filtro, $attr);
        
    }
    
    /**
     * Devulve la primera base que es posible configurar en un servidor normal
     * @return string
     */
    public function getDNBase(){
        $re = "/((ou=\\w+),((dc=\\w+,*){3}))/";
        $str = $this->entrada['dn'];
        preg_match($re, $str, $matches);
        $resultado = array_key_exists(3, $matches) ? $matches[3]: $this->config['base'];
        
        return $resultado;
    }
    
    public function getDNEntrada(){
        return $this->entrada['dn'];
    }
    
    /**
     * Devuelve la primera rama a la cual pertenece
     * @return string
     */
    public function getDNRama(){
        $re = "/((ou=\\w+),((dc=\\w+,*){3}))/";
        $str = $this->entrada['dn'];
        preg_match($re, $str, $matches);
        $resultado = array_key_exists(1, $matches) ? $matches[1]: $this->config['base'];
        return $resultado;
    }
    
    /**
     * Para debug, pero algo me dice que podrìamos sacarle un provecho real
     */
    public function getEntrada(){
        print_r($this->entrada);
    }
    
    /**
     * Auxiliar de actualizarEntrada, se ejecuta por cada elemento del array 
     * y retorna True si su valor no es {empty}
     * @param type $elemento
     * @return type
     */
    private static function elementosVacios($elemento){
        return !($elemento ==="{empty}");
    }
    
    /**
     * Actualiza la actual entrada en LDAP
     * 
     * @return string
     */
    public function actualizarEntrada(){
        // Elimina los elementos vacíos (Asignados {empyt} por defecto) mediante self::elementosVacios
        $valores = array_filter($this->entrada, 'self::elementosVacios');
        // El primer índice es dn, pero ya no lo usaremos màs
        $dn = array_shift($valores);
        if($this->modificarEntrada($valores, $dn)){
            $mensaje = "La actualización de $dn ha ocurrido sin contratiempos";
        }else{
            $mensaje = $this->mostrarERROR();
        }
        return $mensaje;
    }
}
