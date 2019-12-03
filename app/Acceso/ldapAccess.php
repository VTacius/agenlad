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
    /** @var String El dn de quién esta realizando la conexion*/
    protected $dn;
    
    /** @var Array El contenido de todos los datos estará a nivel de clase */
    protected $datos = array();
    
    /** @var $link_identifier La conexión estará a nivel de clase, y no se piensa usar fuera de acá */
    protected $conexion;

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
     * 
     * @param stdClass $parametros 
     * @param stdClass $credenciales 
     * @return Boolean
     * @throws Exception
     */
    function __construct($parametros, $credenciales){
        $this->conexion = ldap_connect($parametros->servidor, $parametros->puerto);
        $this->base = $parametros->base;
		
		ldap_set_option($this->conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->conexion, LDAP_OPT_REFERRALS, 0);
		
		$enlace = @ldap_bind($this->conexion, $credenciales->dn, $credenciales->password);
		if(!$enlace){
			throw new \Exception(ldap_error($this->conexion));
		}
    }

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
        $usuario = array('dn'=>@ldap_get_dn($this->conexion, $entrada));
        foreach ($atributos as $attr) {
            if (($valor = @ldap_get_values($this->conexion, $entrada, $attr))){
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
    public function obtenerDatos($filtro, $atributos, $size=499){
        $datos = Array(); 
        try {
            $busqueda = ldap_search($this->conexion, $this->base, $filtro, $atributos, 0, 0);
            $entrada = ldap_first_entry($this->conexion, $busqueda);
            do {
                array_push($datos, $this->mapa($atributos, $entrada));
            } while ($entrada = @ldap_next_entry($this->conexion, $entrada));
            
        } catch (Exception  $e) {
            $this->setErrorLdap("Error obteniendo datos", $e->getMessage());
            return false;
        }
        return $datos;
    }
    
    /**
     * Empiezan métodos para manipulación de datos
     */
    
    /**
     * Modifica los valores de una entrada ldap para el dn dado
     * @param String $dn
     * @param Array $datos
     * @return Boolean
     * @throws Exception
     */
    public function modificarEntrada ($dn, $datos) {
        if (@ldap_modify($this->conexion, $dn, $datos)) {
            return true;
        } else {
            throw new Exception(ldap_error($this->conexion));
        }
    }

    /**
     * 
     * @param array $valores
     * @param string $dn
     * @return boolean
     * @throws Exception
     */
    public function nuevaEntrada($valores, $dn) {
        try{
            if (@ldap_add($this->conexion, $dn, $valores)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conexion));
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
            if (@ldap_mod_add($this->conexion, $dn, $valores)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conexion));
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
            if (@ldap_mod_del($this->conexion, $dn, $valores)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conexion));
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
            if (ldap_rename($this->conexion, $oldDn, $newRdn, $newParent, true)) {
                return true;
            } else {
                throw new Exception(ldap_error($this->conexion));
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
