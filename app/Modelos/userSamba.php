<?php
/**
 * Description of sambauser
 *
 * @author alortiz
 */

namespace Modelos;

class userSamba extends \Modelos\userPosix {
    // Con protected puede acceder de ella desde usuarios
    protected $sambaSID;
    protected $netbiosName;


    public function __construct($rdnLDAP, $passLDAP) {
        parent::__construct($rdnLDAP, $passLDAP);
        $this->sambaSID = $this->index->get('sambasid');
        $this->netbiosName = $this->index->get('netbiosname'); 
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
        $this->sambaAcctFlags = $sambaAcctFlags;
    }

    public function setSambaHomeDrive($sambaHomeDrive) {
        $this->sambaHomeDrive = $sambaHomeDrive;
    }

    /**
     * Configurado desde usuario::setUid
     * @return array 
     */
    public function setSambaHomePath($sambaHomePath) {
        $this->sambaHomePath = $sambaHomePath;
    }

    public function setSambaKickoffTime($sambaKickoffTime) {
        $this->sambaKickoffTime = $sambaKickoffTime;
    }

    public function setSambaLogoffTime($sambaLogoffTime) {
        $this->sambaLogoffTime = $sambaLogoffTime;
    }

    public function setSambaLogonScript($sambaLogonScript) {
        $this->sambaLogonScript = $sambaLogonScript;
    }

    public function setSambaLogonTime($sambaLogonTime) {
        $this->sambaLogonTime = $sambaLogonTime;
    }
        
    public function setSambaPrimaryGroupSID($sambaPrimaryGroupSID) {
        $this->configurarValor('sambaPrimaryGroupSID', $sambaPrimaryGroupSID);
    }

    public function setSambaPwdCanChange($sambaPwdCanChange) {
        $this->sambaPwdCanChange = $sambaPwdCanChange;
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
        $this->configurarDatos('sambaSID', $sambaSID);
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
        $this->configurarValor('sambaLMPassword', $this->hashito->NTLMHash($sambaLMPassword));
    }

    protected function setSambaNTPassword($sambaNTPassword) {
        $this->configurarValor('sambaNTPassword', $this->hashito->NTLMHash($sambaNTPassword));
    }
}
