<?php
/**
 * authentication
 * Con esto me autentico, poca cosa mas
 * @author vtacius
 */

 namespace App\Clases;

class Authentication {
	public function __construct($parametros, $credenciales){
		$this->parametros = $parametros;
		$this->credenciales = $credenciales;
	}
	
	/**
	 *	LDAP storage handler
	 *	@param String $usuario 
	 *	@param String $password 
	 *	@return bool
	 */
	protected function ldap($usuario, $password, $parametros, $credenciales) {
		try {
			$conexion = ldap_connect($parametros->servidor, $parametros->puerto);
			
			ldap_set_option($conexion, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($conexion, LDAP_OPT_REFERRALS, 0);
			
			$enlace = @ldap_bind($conexion, $credenciales->dn, $credenciales->password);
			
			if(!$enlace){
				throw new \Exception(ldap_error($conexion));
			}
		
			$resultado = ldap_search($conexion, $parametros->base, "uid=$usuario");
			$informacion = ldap_get_entries($conexion, $resultado);

			return @ldap_bind($conexion, $informacion[0]['dn'], $password); 
		} catch (\Exception $e){
			print($e->getMessage() . "\n");
		}
		
	}
        
	/**
	*	Login auth mechanism
	 *	@param String $usuario 
	 *	@param String $password 
	*	@return bool
	**/
	function login($usuario, $password) {
		return $this->ldap($usuario, $password, $this->parametros, $this->credenciales);
	}

}


