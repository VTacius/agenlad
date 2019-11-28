<?php
/**
 * Permite varios operaciones sobre objetos 
 *
 * @author alortiz
 */

namespace App\Modelos;
use App\Acceso\ldapAccess;

class objectosLdap {
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
     * @var Clases\cifrado
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
     * @var $link_identifier La conexión con LDAP 
     */
    protected $conexion;

    public function __construct($conexion, $cifrado = ""){
        $this->conexion = $conexion;
        $this->cifrado = $cifrado;
    }
    
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
    
    private function parserFiltro($clave, $valor) {
        if (\preg_match('/^NOT/', $valor)){
            $v = explode(' ', $valor);
            return "(!({$clave}={$v[1]}))";
        } else if(\preg_match('/(?:\*|\w+)/', $valor)) {
            return "({$clave}={$valor})";
        }
    }
   
    private function parsearItems($clave, $cadena){
        $partes = array_map(
            function($valor) use ($clave){
                return $this->parserFiltro($clave, trim($valor));
            }, preg_split('/AND/', $cadena));
    
        return array_reduce($partes, function($contenido, $actual){
            return $contenido . $actual;
        }, '(&') . ')';
    }

    protected function crearFiltro($search){
        $filtro = "(&(objectClass=$this->objeto)";
        foreach ($search as $clave => $valor){
            $filtro .= $this->parsearItems($clave, $valor);
        }
        $filtro .= ")";
        return $filtro;
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
        return !($elemento ==="{empty}" || $elemento ==="" || $elemento == NULL || $elemento===' ');
    }
    
    /**
     * Configurar atributos único con los cuales es posible buscar 
     * entradas existente dentro del árbol LDAP
     * En caso 
     * @param string $atributo
     * @param string $especificacion
     */
    protected function configurarDatos($atributo, $valor){
        $atributo = strtolower($atributo);
        $filtro = "(&($atributo=$valor)(objectClass=$this->objeto))";
        if (empty($this->entrada)) {
            $this->entrada = $this->conexion->getDatos($filtro, $this->atributos)[0];
            return !empty($this->entrada['dn']); 
        }else{
            /** TODO: ¿Esto en verdad aún sirve? */
            $this->entrada[$atributo] = $valor;
            return true;
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
        $this->entrada = $this->conexion->getDatos($filtro, $atributes);
        return $this->entrada; 
    }
    
    /**
     * Realiza la búsqueda en base a un arreglo hash pasado como parametro
     * @param array $search
     * @param array $atributes
     * @param boolean|string $base
     * @return array
     */
    
    public function search($search, $atributos = false, $base = false, $limite = 499){
       
        $base = $base ? $base: $this->base;
        $atributos = $atributos ? array_merge(array_keys($search), $atributos) : array_keys($search);
        $filtro = $this->crearFiltro($search);
        $this->entrada = $this->conexion->getDatos($filtro, $atributos, $limite);
        
        return $this->entrada;
    }
    
    /**
     * Actualiza la actual entrada en LDAP
     * 
     * @return bool
     */
    public function actualizarEntrada(){
        // Elimina los elementos vacíos (Asignados {empyt} por defecto) mediante self::elementosVacios
        $valores = array_filter($this->entrada, 'self::elementosVacios');
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
        // El primer índice es dn, pero ya no lo usaremos màs
        $valores['objectClass'] = $this->objectClass;
        if($this->nuevaEntrada($valores, $dn)){
            return true;
        }else{
            return false;
        }
    }
}