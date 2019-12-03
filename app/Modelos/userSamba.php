<?php
/**
 * Clase para creación, obtención y modificación de datos de usuario Samba
 *
 * @author alortiz
 */

namespace App\Modelos;

use App\Modelos\userPosix;

class userSamba extends userPosix {
    // Con protected puede acceder de ella desde usuarios
    protected $sambaSID;
    protected $netbiosName;

    public function __construct($parametros, $conexion, $cifrado) {
        parent::__construct($parametros, $conexion, $cifrado);
        
        $this->objeto = 'sambaSamAccount';
         
        $this->atributos = array_merge($this->atributos, array(    
            'sambaAcctFlags', 'sambaHomeDrive', 'sambaHomePath', 'sambaKickoffTime', 'sambaLMPassword', 'sambaLogoffTime',
            'sambaLogonScript', 'sambaLogonTime', 'sambaNTPassword', 'sambaPrimaryGroupSID', 'sambaPwdCanChange', 
            'sambaPwdLastSet', 'sambaPwdMustChange','sambaSID',));
        
        $this->objectClass = array( 'top', 'person', 'organizationalPerson', 'posixAccount', 'shadowAccount', 'inetOrgPerson', 'sambaSamAccount');
        
        $this->sambaSID = $parametros->sambaSID;
        
        $this->netbiosName = $parametros->netbiosName; 
    }

    public function getSambaAcctFlags() {
        return $this->entrada['sambaAcctFlags'];
    }

    public function getSambaHomeDrive() {
        return $this->entrada['sambaHomeDrive'];
    }

    protected function getSambaHomePath() {
        return $this->entrada['sambaHomePath'];
    }

    public function getSambaKickoffTime() {
        return $this->entrada['sambaKickoffTime'];
    }

    public function getSambaLogoffTime() {
        return $this->entrada['sambaLogoffTime'];
    }

    public function getSambaLogonScript() {
        return $this->entrada['sambaLogonScript'];
    }

    public function getSambaLogonTime() {
        return $this->entrada['sambaLogonTime'];
    }

    public function getSambaPrimaryGroupSID() {
        return $this->entrada['sambaPrimaryGroupSID'];
    }

    public function getSambaPwdCanChange() {
        return $this->entrada['sambaPwdCanChange'];
    }

    public function getSambaPwdLastSet() {
        return $this->entrada['sambaPwdLastSet'];
    }

    public function getSambaPwdMustChange() {
        return $this->entrada['sambaPwdMustChange'];
    }

    public function getSambaSID() {
        return $this->entrada['sambaSID'];
    }
    
    public function setSambaAcctFlags($sambaAcctFlags) {
        $this->configurarValor('sambaAcctFlags', $sambaAcctFlags);
    }

    public function setSambaHomeDrive($sambaHomeDrive) {
        $this->configurarValor('sambaHomeDrive', $sambaHomeDrive);
    }

    /**
     * Configurado desde usuario::setUid
     * @return array 
     */
    public function setSambaHomePath($sambaHomePath) {
        $this->configurarValor('sambaHomePath', $sambaHomePath);
    }

    public function setSambaKickoffTime($sambaKickoffTime) {
        $this->configurarValor('sambaKickoffTime', $sambaKickoffTime);
    }

    public function setSambaLogoffTime($sambaLogoffTime) {
        $this->configurarValor('sambaLogoffTime', $sambaLogoffTime);
    }

    public function setSambaLogonScript($sambaLogonScript) {
        $this->configurarValor('sambaLogonScript', $sambaLogonScript);
    }

    public function setSambaLogonTime($sambaLogonTime) {
        $this->configurarValor('sambaLogonTime', $sambaLogonTime);
    }
        
    public function setSambaPrimaryGroupSID($sambaPrimaryGroupSID) {
        $this->configurarValor('sambaPrimaryGroupSID', $sambaPrimaryGroupSID);
    }

    public function setSambaPwdCanChange($sambaPwdCanChange) {
        $this->configurarValor('sambaPwdCanChange', $sambaPwdCanChange);
    }

    public function setSambaPwdLastSet($sambaPwdLastSet) {
        $this->configurarValor('sambaPwdLastSet', $sambaPwdLastSet);
    }

    public function setSambaPwdMustChange($sambaPwdMustChange) {
        $this->configurarValor('sambaPwdMustChange', $sambaPwdMustChange);
    }

    /**
     * Configurado desde usuario::setUidNumber
     * @param string $sambaSID
     */
    protected function setSambaSID($sambaSID) {
        $this->configurarEntrada('sambaSID', $sambaSID);
    }
    
    /**
     * Función pública para el uso de
     * usuario::setUserPassword, sambauser::setSambaNTPassword, sambauser::setSambaLMPassword
     * @param type $password
     */
    public function configuraPassword($password) {
        parent::configuraPassword($password);
        $this->setSambaNTPassword($password);
        $this->setSambaLMPassword($password);
    }
       
    protected function setSambaLMPassword($sambaLMPassword) {
        $this->configurarValor('sambaLMPassword', $this->hashito->LMhash($sambaLMPassword));
    }

    protected function setSambaNTPassword($sambaNTPassword) {
        $this->configurarValor('sambaNTPassword', $this->hashito->NTLMHash($sambaNTPassword));
    }    

    public function setUidNumber($uidNumber) {
        parent::setUidNumber($uidNumber);
        // $this->sambaSID y $this->setSambaSID están definidas en sambaUser
        // Pues parece que funciona después de todo, aunque creo que esto es una de esas cosas
        // que cualquiera en su sano juicio desaconsejaría
        $sambaSID = $this->sambaSID . "-" . strval(($uidNumber *2) + 1000);
        $this->setSambaSID($sambaSID);
    }
    
    public function setUid($uid) {
        $existeResultado = parent::setUid($uid);
        if($existeResultado){
            $this->setSambaHomePath("\\\\{$this->netbiosName}\\{$uid}");
        }
    }
}
