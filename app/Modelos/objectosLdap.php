<?php
/**
 * Permite varios operaciones sobre objetos 
 *
 * @author alortiz
 */

namespace Modelos;

class objectosLdap extends \Modelos\ldapAccess{
    /** 
     * Arreglo de los atributos del usuario. Recuerde que DN no se considera atributo
     * @var array 
     */
    protected $atributos = array();
    
    /**
     * El contenido de todo cuanto el usuario puede ser
     * @var array
     */
    protected $entrada = array();
    
    /**
     * @var clases\cifrado
     */
    protected $hashito;
    
    /**
     * @var string (ObjectClass) 
     */
    protected $objeto;
    
    /**
     * @var array (Una lista de ObjectClass )
     */
    protected $objectClass;
    
    
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
    // TODO: Hay que revisar esto con urgencia
    // TODO: Dicho de la forma más seria posible, esto necesita revision
    protected function configurarDatos($atributo, $especificacion){
//        print "Estoy en configurar datos gracias a " . $atributo . "<br>";
        $valor = strtolower($atributo);
        $filtro = "(&($valor=$especificacion)(objectClass=$this->objeto))";
        if (empty($this->entrada)) {
//            print "La entrada esta vacía en este momento<br>";
            // Si esta vacío, llene el array por primera vez
            $this->entrada = $this->getDatos($filtro, $this->atributos)[0];
            // ¿La busqueda esta vacía?
            if (empty($this->entrada['dn'])){
//                print "dn esta vació, por tanto todo esta vacío<br>";
                foreach ($this->atributos as $attr) {
                    $this->entrada[$attr] = "{empty}"; 
                }
                $this->entrada[$atributo] = $especificacion;
            }else{
//                print "dn Esta lleno y habra que ver que mas esta lleno<br>";
                foreach ($this->atributos as $attr) {
                    $this->entrada[$attr] = isset($this->entrada[$attr]) ? $this->entrada[$attr] : "{empty}"; 
                }
            }

        }else{
            // Si alguien ya lleno el array, vea que tiene datos que pueda tener
//            print "La entrada ya esta llena, así que solo configuro $atributo = $especificacion <br>";
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
    
    private function parserFiltro($attr, $valor){
        $matches = array();
        $filtro = "";
        if (preg_match_all("/(NOT|OR)\s{1,2}\(*(?<valores>[a-z]+)/", $valor, $matches)){
                $pre_attr = "(&";
                foreach($matches['valores'] as $value){
                        $pre_attr .= "(!($attr=$value))";
                }
                $pre_attr .= ")";
                $filtro .= $pre_attr;
        }else if ($attr=="personalizado"){
		$filtro .= $valor;	
	}else{
                $filtro .= "($attr=$valor)";
        }
        return $filtro;
    }
    
    protected function filtro($search){
        $filtro = "(&(objectClass=$this->objeto)";
        foreach ($search as $attr => $valor){
            $filtro .= $this->parserFiltro($attr, $valor);
        }
        $filtro .= ")";
        return $filtro;
    }
    
    /**
     * Realiza la búsqueda en base a un arreglo hash pasado como parametro
     * @param array $search
     * @param array $atributes
     * @param boolean|string $base
     * @return array
     */
    
    public function search( $search, $atributes = false, $base = false){
        if ($base == false) {
            $this->base =  $base;
        }
        $this->datos = array();
        if ($atributes == false){
            $attr = array_keys($search);
        }else{
            $attr = array_merge(array_keys($search), $atributes);
        }
        $filtro = $this->filtro($search);
        $this->entrada = $this->getDatos($filtro, $attr);
        return $this->entrada;
        
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
        return $this->entrada;
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
        $valores = array_filter($this->entrada, self::elementosVacios);
        // El primer índice es dn, pero ya no lo usaremos màs
        $dn = array_shift($valores);
        if($this->modificarEntrada($valores, $dn)){
            return true;
        }else{
            return false;
        }
    }
    
    public function crearEntrada($dn){
        // Elimina los elementos vacíos (Asignados {empyt} por defecto) mediante self::elementosVacios
        $valores = array_filter($this->entrada, 'self::elementosVacios');
        print_r($this->objectClass);
        // El primer índice es dn, pero ya no lo usaremos màs
        print 'Este es el dn de esta entrada<br>';
        print "$dn <br>";
        $valores['objectClass'] = $this->objectClass;
        $valores['dn'] = $dn;
        return $valores;
       //$dn = array_shift($valores);
       // if($this->nuevarEntrada($valores, $dn)){
       //     return true;
       // }else{
       //     return false;
       // }
    }
}
