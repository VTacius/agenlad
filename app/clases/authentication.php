<?php
/**
 * Description of authentication
 *
 * @author vtacius
 */
namespace clases;
class authentication {
    //@{ Error messages
	const
		E_LDAP='LDAP connection failure';
	//@}

	protected
		//! Auth storage
		$storage,
		//! Mapper object
		$mapper,
		//! Storage options
		$args;
        
        private $dn;
        
        public function getDn() {
            return $this->dn;
        }

        public function setDn($dn) {
            $this->dn = $dn;
        }

	/**
	*	LDAP storage handler
	*	@return bool
	*	@param $id string
	*	@param $pw string
	**/
	protected function ldap($id,$pw) {
		$dc=@ldap_connect($this->args['dc']);
		if ($dc &&
			ldap_set_option($dc,LDAP_OPT_PROTOCOL_VERSION,3) &&
			ldap_set_option($dc,LDAP_OPT_REFERRALS,0) &&
			ldap_bind($dc,$this->args['rdn'],$this->args['pw']) &&
			($result=ldap_search($dc,$this->args['base_dn'],
				'uid='.$id)) &&
			ldap_count_entries($dc,$result) &&
			($info=ldap_get_entries($dc,$result)) &&
			@ldap_bind($dc,$info[0]['dn'],$pw) &&
			@ldap_close($dc)) {
                        $this->setDN($info[0]['dn']);
			return $info[0]['uid'][0]==$id;
		}
		user_error(self::E_LDAP);
	}
        
	/**
	*	Login auth mechanism
	*	@return bool
	*	@param $id string
	*	@param $pw string
	*	@param $realm string
	**/
	function login($id,$pw) {
		return $this->ldap($id,$pw);
	}


	/**
	*	Instantiate class
	*	@return object
	*	@param $storage string|object
	*	@param $args array
	**/
	function __construct($storage,array $args=NULL) {
		$this->storage=$storage;
		$this->args=$args;
	}
}


