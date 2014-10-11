<?php
/**
 * Clase para manejar el acceso a LDAP
 *
 * @author alortiz
 */
namespace Acceso;
use Exception;
use ErrorException;

class ldapAccess {
    /** @var El dn de quién esta realizando la conexion*/
    protected $dn;
    /** @var \Base */
    protected $index;
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
    /** @var  bool El enlace estará a nivel de clases */
    protected $enlaceLdap;
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
    
    /**
     * TODO: Estamos duplicados con \clases\sesion
     * Retorna un array con la configuración para el dominio para el cual tiene
     * permisos el usuarios que ha abierto la sesion
     * @return array
     */
    protected function getConfiguracionDominio(){
        $base = $this->index->get('dbconexion');
        $dominio = $this->index->get('SESSION.dominio');
        
        $cmds = "select attr from configuracion where dominio=:dominio";
        $args = array('dominio'=>$dominio);
        $resultado = $base->exec($cmds, $args);
        // El mensaje que retorna si las variables de sesiòn no estàn es increìble
        // No importa como pase despuès, los demàs errores estàn silenciados en el contructor
        if ($base->count() > 0) {
            return unserialize($resultado[0]['attr']);
        }
    }
    
    /**
     * Empiezan métodos necesarios para establecer cualquier conexión
     */
    
    /**
     * 
     * @param string $destino central|personalizado
     * @param array $parametros array ([0]=>servidor, [1]=>puerto, [2]=>base)
     */
    private function conexion ($destino, $parametros){
        // Configuramos según la base de datos
        if (sizeof($parametros) == 0) {
            $parametros = array('sserver', 'spuerto', 'sbase');
        }
        switch ($destino) {
            case 'central':
            case 'personalizado':
                $this->server = $this->index->get($parametros[0]);
                $this->puerto = $this->index->get($parametros[1]);
                $this->base = $this->index->get($parametros[2]);
                break;
            default :
                $this->config = $this->getConfiguracionDominio();      
                $this->server = $this->config['servidor'];
                $this->puerto = $this->config['puerto'];
                $this->base = $this->config['base'];
                break;
        }
        // Empezamos la conexión
        $this->conexionLdap = ldap_connect($this->server,  $this->puerto);
        ldap_set_option($this->conexionLdap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->conexionLdap, LDAP_OPT_NETWORK_TIMEOUT, 2);
    }
    /**
   * El constructor inicia la conexión hacia el servidor LDAP leyendo algunos datos desde el fichero de configuración
   * @param string $rdnLDAP
   * @param strin $passLDAP
   * @return boolean
   * @throws Exception
   */
    
    /**
     * 
     * @param string $rdnLDAP DN del usuario con el cual conectar
     * @param string $passLDAP Contraseña del usuario con el cual conectar
     * @param string  $destino central|personalizado
     * @param array $parametros array ([0]=>servidor, [1]=>puerto, [2]=>base)
     * @return boolean
     * @throws Exception
     */
    function __construct($rdnLDAP, $passLDAP, $destino="", $parametros = array()){
        $this->index = \Base::instance();
        $this->conexion($destino, $parametros);
        try{
            if ((@$this->enlaceLdap = ldap_bind($this->conexionLdap, $rdnLDAP, $passLDAP))){
                // No entiendo porque mi necesidad de configurar acá el controLDAP::dn, si
                // Ya esta configurado en las variables de sesión
                // TODO: Revisar de que va esta onda
                $this->dn = $rdnLDAP;
                return true;
            } else {
                throw new Exception (ldap_error($this->conexionLdap));
            }
        }catch (Exception $e) {
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
     * @param string $filtro
     * @param array $atributos
     * @param int $size
     */
    public function getDatos($filtro, $atributos, $size=499){
        try {
            // Es necesarios silenciar el error (Sobre Timelimit) para que FatFree no lo devuelva 
            if (!($this->searchLDAP = @ldap_search($this->conexionLdap, $this->base, $filtro, $atributos, 0, $size, $size))) {
                throw new ErrorException (ldap_error($this->conexionLdap));
            }
            // probaremos a agregar ldap_sort sin romper compatibilidad
            if(!(@ldap_sort($this->conexionLdap, $this->searchLDAP, $atributos[0]))){
                throw new ErrorException (ldap_error($this->conexionLdap));
            }            
            //Creamos la primera entrada 
            $entrada = @ldap_first_entry($this->conexionLdap, $this->searchLDAP);
            // Al menos una vez, metemos en $this->datos mediante push los valores que $this->mapa nos devuelva
            do {
                array_push($this->datos, $this->mapa($atributos, $entrada));
            } while ($entrada = @ldap_next_entry($this->conexionLdap, $entrada));
            
        } catch (ErrorException  $e) {
            $this->setErrorLdap("Error obteniendo datos", $e->getMessage());
            return false;
        }
        return $this->datos;
    }
    
    /**
     * Crea un filtro con los índices=>valor del array pasado como parametros
     * Por ahora, usar sólo con un único filtro, por favor
     * 
     * OBSOLETO Y SIN USO REAL
     * Parece ser que la forma en que search forma el filtro es suficiente
     * 
     * @param array $filtro Use array('uid','cn','title','o', 'ou','mail')
     * @return string Valores por defecto
     */
    public function createFiltro($filtro){
        $filtrado = "(&(&(!(uid=root))(!(uid=nobody)))";
        $atributos = array('uid','cn','title','o', 'ou','mail');
        foreach ($atributos as $value) {
            if (array_key_exists($value, $filtro)) {
                $filtrado .= "($value={$filtro[$value]})";
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
        if ($dn == false) {
            $dn = $this->dn;
        }
        try{
            if (ldap_modify($this->conexionLdap, $dn, $valores)) {
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
            if (ldap_add($this->conexionLdap, $dn, $valores)) {
                return true;
            } else {
//                print_r($this->conexionLdap);
//                print "Estoy ac{a sin razpn aparente";
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
