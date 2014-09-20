<?php
/**
 * Clase para manejar el acceso a LDAP
 *
 * @author alortiz
 */
namespace Modelos;
use Exception;
class controlLDAP {
    /** @var \Base */
    protected $index;
    // Configuración de parametros
    protected $server;
    protected $puerto;
    protected $base;
    /** @var $link_identifier La conexión estará a nivel de clase, y no se piensa usar fuera de acá */
    private $conLDAP;
    /** @var  bool El enlace estará a nivel de clases */
    protected $bindLDAP;
    // Errores, si es que podemos enviar esto
    protected $errorLDAP = "";
    // Tendremos a la mano la busqueda lista para ordenarla después
    protected $searchLDAP; 
    /** @var array El contenido de todos los datos estará a nivel de clase */
    protected $datos = array();
    /** @var El dn se encuentra disponible a nivel de clase*/
    protected $dn;
  
  /**
    * Get auth::dn
    * @return string
    */
    public function getDN(){
        return $this->dn;
    }
    
  /**
   * Set auth::dn
   * @param string $dn
   */
    public function setDN($dn){
        $this->dn = $dn;
    }
    
    /**
     * Empiezan métodos necesarios para establecer cualquier conexión
     */
    
  /**
   * El constructor inicia la conexión hacia el servidor LDAP leyendo algunos datos desde el fichero de configuración
   * @param string $rdnLDAP
   * @param strin $passLDAP
   * @return boolean
   * @throws Exception
   */
    function __construct($rdnLDAP, $passLDAP, $server = "sserver", $puerto = "spuerto", $base = "sbase"){
        $this->index = \Base::instance();
        // Traemos los parametros que necesitamos desde el archivo de configuración que 
        // leímos en index.php
        $this->server = $this->index->get($server);
        $this->puerto = $this->index->get($puerto);
        $this->base = $this->index->get($base);
        // Empezamos la conexión
        $this->conLDAP = ldap_connect($this->server,  $this->puerto);
        ldap_set_option($this->conLDAP, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->conLDAP, LDAP_OPT_NETWORK_TIMEOUT, 2);
        // Hacemos el enlace de una vez
        try{
            if (empty($rdnLDAP) | empty($passLDAP)) {
                throw new Exception ("Credenciales vacías");
                // Odio tener que enmascarar los errores de esta manera
            } elseif ((@$this->bindLDAP = ldap_bind($this->conLDAP, $rdnLDAP, $passLDAP))){
                // No entiendo porque mi necesidad de configurar acá el controLDAP::dn, si
                // Ya esta configurado en las variables de sesión
                $this->dn = $rdnLDAP;
                return true;
            } else {
                throw new Exception ("Error en la conexión: <b>".ldap_error($this->conLDAP)."</b>");
            }
        }catch (Exception $e) {
                $this->errorLDAP = $e->getMessage();	
                return false;
        }
    }
	
    /**
     * Terminan métodos necesarios para establecer cualquier conexión 
     */
	
    /**
     * Empiezan métodos auxiliares de primer nivel
     */
    
    /**
     * 
     * @return errorLDAP
     */
    public function mostrarERROR(){ 
        return $this->errorLDAP;
    }
    
    /**
     * Auxiliar de datos
     * Obtiene los atributos solicitados en el array $atributos si $entrada lo 
     * contiene, sino lo configura vacío
     * @param array $atributos
     * @param $result_entry_identifier $entrada
     */
    protected function mapa($atributos, $entrada){
        $usuario = array('dn'=>@ldap_get_dn($this->conLDAP, $entrada));
        foreach ($atributos as $attr) {
            if (($valor = @ldap_get_values($this->conLDAP, $entrada, $attr))){
                array_pop($valor);
                $usuario[$attr] = count($valor)==1? $valor[0]:  $valor;
            }
        }
        return $usuario;
    }
    
    /**
     * Hacemos una busqueda y obtenemos resultados en $this->datos
     * @param string $filtro
     * @param array $atributos
     * @param int $size
     */
    public function getDatos($filtro, $atributos, $size=499){
        try {
            // Es necesarios silenciar el error (Sobre Timelimit) para que FatFree no lo devuelva 
            $this->searchLDAP = @ldap_search ($this->conLDAP, $this->base, $filtro, $atributos, 0, $size, $size);
            
            // probaremos a agregar ldap_sort sin romper compatibilidad
            ldap_sort($this->conLDAP, $this->searchLDAP, $atributos[0]);
            //Creamos la primera entrada 
            $entrada = ldap_first_entry($this->conLDAP, $this->searchLDAP);
            // Al menos una vez, metemos en $this->datos mediante push los valores que $this->mapa nos devuelva
            do {
                array_push($this->datos, $this->mapa($atributos, $entrada));
            } while ($entrada = @ldap_next_entry($this->conLDAP, $entrada));
            
        } catch (Exception $e) {
            $this->errorLDAP = $e->getMessage();
        }
        return $this->datos;
    }
    
    /**
     * Crea un filtro con los índices=>valor del array pasado como parametros
     * Por ahora, usar sólo con un único filtro, por favor
     * @param array $filtro Use array('uid','cn','title','o', 'ou','mail')
     * @return string Valores por defecto
     */
    public function createFiltro($filtro){
        $filtrado = "(&(&(!(uid=root))(!(uid=nobody)))";
        $atributos = array('uid','cn','title','o', 'ou','mail');
        foreach ($atributos as $value) {
            if (array_key_exists($value, $filtro)) {
                $filtrado .= "($value=$filtro[$value])";
            }
        }
        $filtrado .= $filtrado=="(&(&(!(uid=root))(!(uid=nobody)))" ? "(uid=*))" :  ")";
        return $filtrado;
    }

    /**
     * Empiezan métodos para manipulación de datos
     */
    
    /**
     * Modifica los valores de una entrada ldap para el dn dado
     * @param array $valores
     * @return boolean
     * @throws Exception
     */
    public function modificarEntrada ($valores, $dn = false) {
        // Mantenemos la compatibilidad con la forma en que se usa para cambiar contraseña
        if (!$dn) {
            $dn = $this->dn;
        }
        try{
            if (ldap_modify($this->conLDAP, $dn, $valores)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conLDAP));
            }
        }catch(Exception $e){
            $this->errorLDAP = $e->getMessage();	
            return false;
        }
    }

    function nuevaEntrada( $valores, $entry ) {
        try{
            if (ldap_add($this->conLDAP, $entry, $valores)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conLDAP));
            }
        }catch(Exception $e){
            $this->errorLDAP = $e->getMessage();
            return false;
        }
    }

    function agregarAtributos( $dn, $valores) {
        // En realidad, parece que esta función agrega un atributo del tipo 
        // "permito varios y no me ahuevo"
        try{
            if (@ldap_mod_add($this->conLDAP, $dn, $valores)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conLDAP));
            }
        }catch(Exception $e){
            $this->errorLDAP = $e->getMessage();
            return false;
        }
    }
    
    function removerAtributos( $dn, $valores) {
        // En realidad, parece que esta función agrega un atributo del tipo 
        // "permito varios y no me ahuevo"
        try{
            if (@ldap_mod_del($this->conLDAP, $dn, $valores)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conLDAP));
            }
        }catch(Exception $e){
            $this->errorLDAP = $e->getMessage();
            return false;
        }
    }
    
    /**
     * Terminan métodos para manipulación de datos	
     */
}
