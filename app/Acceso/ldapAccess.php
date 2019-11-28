<?php
/**
 * Clase para manejar el acceso a LDAP
 *
 * @author alortiz
 */
namespace App\Acceso;
use Exception;
use ErrorException;

class ldapAccess {
    /** @var El dn de quién esta realizando la conexion*/
    protected $dn;
    /** @var array El contenido de todos los datos estará a nivel de clase */
    protected $datos = array();
     /** Configuracion de los parametros de conexión  */
    protected $base;
    protected $server;
    protected $puerto;
    /** @var array La configuración que trajimos desde la base de datos*/
    protected $config;
    /** @var $link_identifier La conexión estará a nivel de clase, y no se piensa usar fuera de acá */
    protected $conexionLdap;
    /** @var  array Errores ocurridos durante las operaciones LDAP */
    protected $errorLdap = array();
    /** @var  bool Tendremos a la mano la busqueda lista para ordenarla después */
    protected $searchLDAP; 
 
    /**
    * Get auth::dn
    * @return string
    */
    public function getDN(){
        return $this->dn;
    }
    
    public function setErrorLdap($titulo, $mensaje){
        $this->errorLdap[] = array( 'titulo' => $titulo, 'mensaje' => $mensaje);
    }
    /**
     * 
     * @return errorLdap
     */
    public function getErrorLdap(){ 
        if (sizeof($this->errorLdap)>0) {
            return $this->errorLdap;
        }
    }

    /*
     * Esta función agrega compatiblidad con la función creada más arriba 
     * listarAtributosUsuarios
     */
    public function setBaseLdapAccess($base){
        $this->base = $base;
    }
    
    /**
     * Empiezan métodos necesarios para establecer cualquier conexión
     */
    
    /**
     * 
     * @param stdClass $parametros 
     * @param stdClass $credenciales 
     * @return Boolean
     * @throws Exception
     */
    function __construct($parametros, $credenciales){
        try{
            $this->base = $parametros->base;
            $this->conexionLdap = ldap_connect($parametros->servidor);
            ldap_set_option($this->conexionLdap, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($this->conexionLdap, LDAP_OPT_NETWORK_TIMEOUT, 2);
            if ((@$this->enlaceLdap = ldap_bind($this->conexionLdap, $credenciales->dn, $credenciales->password))){
                return true;
            } else {
                throw new Exception (ldap_error($this->conexionLdap));
            }
        } catch (Exception $e) {
            $this->setErrorLdap("Error en la conexion", $e->getMessage());
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
     * Auxiliar de datos
     * Obtiene los atributos solicitados en el array $atributos si $entrada lo 
     * contiene, sino lo configura vacío
     * @param array $atributos
     * @param $result_entry_identifier $entrada
     */
    protected function mapa($atributos, $entrada){
        $usuario = array('dn'=>@ldap_get_dn($this->conexionLdap, $entrada));
        foreach ($atributos as $attr) {
            if (($valor = @ldap_get_values($this->conexionLdap, $entrada, $attr))){
                array_pop($valor);
                $usuario[$attr] = count($valor)==1? $valor[0]:  $valor;
            }
        }
        return $usuario;
    }
    
    /**
     * Hacemos una busqueda y obtenemos resultados en $this->datos
     * @param String $filtro
     * @param Array $atributos
     * @param Integer $size
     */
    public function getDatos($filtro, $atributos, $size=499){
        
        try {
            $this->searchLDAP = ldap_search($this->conexionLdap, $this->base, $filtro, $atributos, 0, 0);
            $entrada = ldap_first_entry($this->conexionLdap, $this->searchLDAP);
            do {
                array_push($this->datos, $this->mapa($atributos, $entrada));
            } while ($entrada = @ldap_next_entry($this->conexionLdap, $entrada));
            
        } catch (Exception  $e) {
            $this->setErrorLdap("Error obteniendo datos", $e->getMessage());
            return false;
        }
        return $this->datos;
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
        if ($dn == false) {
            $dn = $this->dn;
        }
        try{
            if (@ldap_modify($this->conexionLdap, $dn, $valores)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conexionLdap));
            }
        }catch(Exception $e){
            $this->setErrorLdap("Error en modificación", $e->getMessage());	
            return false;
        }
    }

    /**
     * 
     * @param array $valores
     * @param string $dn
     * @return boolean
     * @throws Exception
     */
    public function nuevaEntrada( $valores, $dn ) {
        try{
            if (@ldap_add($this->conexionLdap, $dn, $valores)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conexionLdap));
            }
        }catch(Exception $e){
            $this->setErrorLdap("Error agregando entrada", $e->getMessage());
            return false;
        }
    }

    function agregarAtributos( $dn, $valores) {
        // En realidad, parece que esta función agrega un atributo del tipo 
        // "permito varios y no me ahuevo"
        try{
            if (@ldap_mod_add($this->conexionLdap, $dn, $valores)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conexionLdap));
            }
        }catch(Exception $e){
            $this->setErrorLdap("Error en modificación", $e->getMessage());	
            return false;
        }
    }
    
    function removerAtributos( $dn, $valores) {
        // En realidad, parece que esta función agrega un atributo del tipo 
        // "permito varios y no me ahuevo"
        try{
            if (@ldap_mod_del($this->conexionLdap, $dn, $valores)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conexionLdap));
            }
        }catch(Exception $e){
            $this->setErrorLdap("Error en modificación", $e->getMessage());
            return false;
        }
    }
    
    public function moverEntrada ($oldDn, $newParent, $newRdn = NULL){
        if (!$newRdn) {
            $re = "/(\\w+=\\w+)/";
            preg_match($re, $oldDn, $matches);
            $newRdn = $matches[1];
        }
        try {
            if (ldap_rename($this->conexionLdap, $oldDn, $newRdn, $newParent, true)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conexionLdap));
            }
        } catch (Exception $e) {
            $this->setErrorLdap("Error en modificación", $e->getMessage());
            return false;
        }
    }
    
    /**
     * Terminan métodos para manipulación de datos	
     */
}
