<?php
namespace clases;
/**
 * Clase para creación, obtención y modificación de datos de usuario
 *
 * @author alortiz
 */
class user extends \clases\controlLDAP{
    /** @var array 
     * Arreglo de los atributos del usuario. Recuerde que DN no se considera atributo
     */
    private $atributos = array(    
    'cn','displayName','dn','gecos','gidNumber',
    'givenName','homeDirectory','loginShell','mail','o',
    'objectClass','ou','postalAddress','sambaAcctFlags','sambaHomeDrive',
    'sambaHomePath','sambaKickoffTime','sambaLMPassword','sambaLogoffTime','sambaLogonScript',
    'sambaLogonTime','sambaNTPassword','sambaPrimaryGroupSID','sambaPwdCanChange','sambaPwdLastSet',
    'sambaPwdMustChange','sambaSID','shadowLastChange','shadowMax','shadowMin',
    'sn','telephoneNumber','title','uid','uidNumber',
    'userPassword');
    
    /**
     *
     * @var array
     */
    public $usuario = array();
    
    private function configurarDatos($atributo, $especificacion){
        $valor = strtolower($atributo);
        $filtro = "$valor=$especificacion";
        if (empty($this->usuario)) {
            // Si esta vacío, llene el array por primera vez
            $this->usuario = $this->getDatos($filtro, $this->atributos)[0];
            
            foreach ($this->atributos as $attr) {
                $this->usuario[$attr] = isset($this->usuario[$attr])?$this->usuario[$attr]:"--"; 
            }
        }else{
            // Si alguien ya lleno el array, vea que tiene datos que pueda tener
            $this->usuario[$atributo] = $especificacion;
        }
        
    }
    
    public function getCn() {
        return $this->usuario['cn'];
    }

    public function getDisplayName() {
        return $this->displayName;
    }

    public function getGecos() {
        return $this->gecos;
    }

    public function getGidNumber() {
        return $this->gidNumber;
    }

    public function getGivenName() {
        return $this->givenName;
    }

    public function getHomeDirectory() {
        return $this->homeDirectory;
    }

    public function getLoginShell() {
        return $this->loginShell;
    }

    public function getMail() {
        return $this->mail;
    }

    public function getO() {
        return $this->usuario['o'];
    }

    public function getObjectClass() {
        return $this->objectClass;
    }

    public function getOu() {
        return $this->usuario['ou'];
    }

    public function getPostalAddress() {
        return $this->usuario['postalAddress'];
    }

    public function getSambaAcctFlags() {
        return $this->sambaAcctFlags;
    }

    public function getSambaHomeDrive() {
        return $this->sambaHomeDrive;
    }

    public function getSambaHomePath() {
        return $this->sambaHomePath;
    }

    public function getSambaKickoffTime() {
        return $this->sambaKickoffTime;
    }

    public function getSambaLMPassword() {
        return $this->sambaLMPassword;
    }

    public function getSambaLogoffTime() {
        return $this->sambaLogoffTime;
    }

    public function getSambaLogonScript() {
        return $this->sambaLogonScript;
    }

    public function getSambaLogonTime() {
        return $this->sambaLogonTime;
    }

    public function getSambaNTPassword() {
        return $this->sambaNTPassword;
    }

    public function getSambaPrimaryGroupSID() {
        return $this->sambaPrimaryGroupSID;
    }

    public function getSambaPwdCanChange() {
        return $this->sambaPwdCanChange;
    }

    public function getSambaPwdLastSet() {
        return $this->sambaPwdLastSet;
    }

    public function getSambaPwdMustChange() {
        return $this->sambaPwdMustChange;
    }

    public function getSambaSID() {
        return $this->sambaSID;
    }

    public function getShadowLastChange() {
        return $this->shadowLastChange;
    }

    public function getShadowMax() {
        return $this->shadowMax;
    }

    public function getShadowMin() {
        return $this->shadowMin;
    }

    public function getSn() {
        return $this->usuario['sn'];
    }

    public function getTelephoneNumber() {
        return $this->telephoneNumber;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getUid() {
        return $this->uid;
    }

    public function getUidNumber() {
        return $this->uidNumber;
    }

    public function getUserPassword() {
        return $this->userPassword;
    }

    public function setCn($cn) {
        $this->cn = $cn;
    }

    public function setDisplayName($displayName) {
        $this->displayName = $displayName;
    }

    public function setGecos($gecos) {
        $this->gecos = $gecos;
    }

    public function setGidNumber($gidNumber) {
        $this->gidNumber = $gidNumber;
    }

    public function setGivenName($givenName) {
        $this->givenName = $givenName;
    }

    public function setHomeDirectory($homeDirectory) {
        $this->homeDirectory = $homeDirectory;
    }

    public function setLoginShell($loginShell) {
        $this->loginShell = $loginShell;
    }

    public function setMail($mail) {
        $this->mail = $mail;
    }

    public function setO($o) {
        $this->configurarDatos('o', $o);
    }

    public function setObjectClass($objectClass) {
        $this->objectClass = $objectClass;
    }

    public function setOu($ou) {
        $this->configurarDatos('ou', $ou);
    }

    public function setPostalAddress($postalAddress) {
        $this->postalAddress = $postalAddress;
    }

    public function setSambaAcctFlags($sambaAcctFlags) {
        $this->sambaAcctFlags = $sambaAcctFlags;
    }

    public function setSambaHomeDrive($sambaHomeDrive) {
        $this->sambaHomeDrive = $sambaHomeDrive;
    }

    public function setSambaHomePath($sambaHomePath) {
        $this->sambaHomePath = $sambaHomePath;
    }

    public function setSambaKickoffTime($sambaKickoffTime) {
        $this->sambaKickoffTime = $sambaKickoffTime;
    }

    public function setSambaLMPassword($sambaLMPassword) {
        $this->sambaLMPassword = $sambaLMPassword;
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

    public function setSambaNTPassword($sambaNTPassword) {
        $this->sambaNTPassword = $sambaNTPassword;
    }

    public function setSambaPrimaryGroupSID($sambaPrimaryGroupSID) {
        $this->sambaPrimaryGroupSID = $sambaPrimaryGroupSID;
    }

    public function setSambaPwdCanChange($sambaPwdCanChange) {
        $this->sambaPwdCanChange = $sambaPwdCanChange;
    }

    public function setSambaPwdLastSet($sambaPwdLastSet) {
        $this->sambaPwdLastSet = $sambaPwdLastSet;
    }

    public function setSambaPwdMustChange($sambaPwdMustChange) {
        $this->sambaPwdMustChange = $sambaPwdMustChange;
    }

    public function setSambaSID($sambaSID) {
        $this->sambaSID = $sambaSID;
    }

    public function setShadowLastChange($shadowLastChange) {
        $this->shadowLastChange = $shadowLastChange;
    }

    public function setShadowMax($shadowMax) {
        $this->shadowMax = $shadowMax;
    }

    public function setShadowMin($shadowMin) {
        $this->shadowMin = $shadowMin;
    }

    public function setSn($sn) {
        $this->configurarDatos('sn', $sn);
    }

    public function setTelephoneNumber($telephoneNumber) {
        $this->telephoneNumber = $telephoneNumber;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setUid($uid) {
        $this->configurarDatos('uid', $uid);
    }

    public function setUidNumber($uidNumber) {
        $this->uidNumber = $uidNumber;
    }

    public function setUserPassword($userPassword) {
        $this->userPassword = $userPassword;
    }


}