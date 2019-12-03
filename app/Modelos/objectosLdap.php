<?php
/**
 * Permite varios operaciones sobre objetos 
 *
 * @author alortiz
 */

namespace App\Modelos;

use App\Acceso\ldapAccess;

class objectosLdap {
    /** @var Array Los atributos del usuario. Recuerde que DN no se considera atributo */
    protected $atributos = Array();

    /** @var Array Los atributos que pueden ser configurables directamente */
    protected $listables = Array();
    
    /** @var Array El contenido de todo cuanto el usuario puede ser */
    protected $entrada = Array();
    
    /** @var App\Clases\Cifrado Clase externa encargada de hashes y cifrados */
    protected $cifrado;
    
    /** @var String El ObjectClass que identifica las entradas LDAP en las clases hijas */
    protected $objeto;
    
    /** @var Array Lista de ObjectClass que se necesitan para definir la entrada LDAP actual */
    protected $objectClass;

    /** @var $link_identifier La conexión con LDAP  */
    protected $conexion;

    public function __construct($conexion, $cifrado = ""){
        $this->conexion = $conexion;
        $this->cifrado = $cifrado;
    }
    
    public function configurarDatos($datos){
        foreach($this->listables as $clave){
            if (array_key_exists($clave, $datos)){
                $this->entrada[$clave] = $datos[$clave];
            }
        }
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
    
    /**
     * Crea el filtro (clave=valor) o (!(clave=valor))
     * @param String $clave
     * @param String $valor
     * @return String
     */
    private function parserFiltro($clave, $valor) {
        if (\preg_match('/^NOT/', $valor)){
            $v = explode(' ', $valor);
            return "(!({$clave}={$v[1]}))";
        } else if(\preg_match('/(?:\*|\w+)/', $valor)) {
            return "({$clave}={$valor})";
        }
    }
    
    /**
     * Divide si es necesario los filtros que se hacen para cada atributo
     * Devuelve algo como (&(clave=valor)(!(clave=valor)))
     * @param String $clave
     * @param String $cadena
     * @return String
     */
    private function parsearItems($clave, $cadena){
        $partes = array_map(
            function($valor) use ($clave){
                return $this->parserFiltro($clave, trim($valor));
            }, preg_split('/AND/', $cadena));
    
        return array_reduce($partes, function($contenido, $actual){
            return $contenido . $actual;
        }, '(&') . ')';
    }

    /**
     * Toma el filtro enviado por el usuario y lo parsear en un filtro ldap válido
     * @param Array $search
     * @return String
     */
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
     * @return String
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
     * En caso de no tener  
     * @param String $atributo
     * @param String $especificacion
     */
    protected function configurarEntrada($atributo, $valor){
        $atributo = strtolower($atributo);
        $filtro = "(&($atributo=$valor)(objectClass=$this->objeto))";
        if (empty($this->entrada)) {
            $this->entrada = $this->conexion->obtenerDatos($filtro, $this->atributos)[0];
            return !empty($this->entrada['dn']); 
        }else{
            /** TODO: ¿Esto en verdad aún sirve? */
            $this->entrada[$atributo] = $valor;
            return true;
        }
    }
    
    /**
     * Realiza la búsqueda en base a un arreglo hash pasado como parametro
     * @param Array $search
     * @param Array $atributes
     * @param Boolean|String $base
     * @return Array
     */
    
    public function busqueda($search, $atributos = false, $base = false, $limite = 499){
       
        $base = $base ? $base: $this->base;
        $atributos = $atributos ? array_merge(array_keys($search), $atributos) : array_keys($search);
        $filtro = $this->crearFiltro($search);
        return  $this->conexion->obtenerDatos($filtro, $atributos, $limite);
    }
    
    /**
     * Actualiza la actual entrada en LDAP los datos actualmente en $this->entrada
     * @return Bool
     */
    public function actualizarEntrada(){
        $dn = $this->entrada['dn'];
        unset($this->entrada['dn']);
        print ("Datos actuales de la entrada en ObjectosLDAP\n");
        print_r($this->entrada);
        return $this->conexion->modificarEntrada($dn, $this->entrada);
    }
   
    /**
     * Crea una nueva entrada LDAP con los datos actualmente en $this->entrada
     * @return Bool 
     */
    public function crearEntrada($dn){
        // Elimina los elementos vacíos (Asignados {empyt} por defecto) mediante self::elementosVacios
        $valores = array_filter($this->entrada, 'self::elementosVacios');
        // El primer índice es dn, pero ya no lo usaremos màs
        $valores['objectClass'] = $this->objectClass;
        if ($this->nuevaEntrada($valores, $dn)) {
            return true;
        } else {
            return false;
        }
    }
}