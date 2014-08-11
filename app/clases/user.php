<?php
namespace clases;
/**
 * Clase para creación, obtención y modificación de datos de usuario
 *
 * @author alortiz
 */
class user extends \clases\controlLDAP{
    /**
     * Caracteres permitidos para la contraseña
     * @var array 
     */
    private $letras = array(
        'a','b','c','d','e',
        'f','g','h','i','j',
        'k','l','m','n','ñ',
        'o', 'p','q','r','s',
        't','u','v','w','x',
        'y','z','_','.','1',
        '2','3','4','5','6',
        '7','8','9','0');
    /** Arreglo de los atributos del usuario. Recuerde que DN no se considera atributo
    / @var array 
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
     * El contenido de todo cuanto el usurio puede ser
     * @var array
     */
    private $usuario = array();
    
    /**
     * 
     * @var clases\cifrado
     */
    private $hashito;
    
    /**
     *
     * @var string (ObjectClass) 
     */
    private $objeto;
    
    /**
     * Configura el valor de un elemento cualquiera dentro del árbol LDAP
     * Use para los atributos que no son de búsqueda
     * @param string $atributo
     * @param string $especificacion
     */
    private function configurarValor($atributo, $especificacion){
        $this->usuario[$atributo] = $especificacion;
    }
    
    /**
     * Configurar atributos único con los cuales es posible buscar 
     * entradas existente dentro del árbol LDAP
     * En caso 
     * @param string $atributo
     * @param string $especificacion
     */
    private function configurarDatos($atributo, $especificacion){
        $valor = strtolower($atributo);
        $filtro = "(&($valor=$especificacion)(objectClass=$this->objeto))";
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
    
    public function __construct($rdnLDAP, $passLDAP) {
        parent::__construct($rdnLDAP, $passLDAP);
        // Usamos desde acá la clase cifrado. 
        $this->hashito = new \clases\cifrado();
        $this->objeto='shadowAccount';
    }
    

    public function getGidNumber() {
        return $this->usuario['gidNumber'];
    }
    
    public function getCn() {
        return $this->usuario['cn'];
    }

    public function getDisplayName() {
        return $this->usuario['displayName'];
    }

    public function getGecos() {
        return $this->usuario['gecos'];
    }

    public function getGivenName() {
        return $this->usuario['givenName'];
    }
    
    public function getSn() {
        return $this->usuario['sn'];
    }

    public function getHomeDirectory() {
        return $this->usuario['homeDirectory'];
    }

    public function getLoginShell() {
        return $this->usuario['loginShell'];
    }

    public function getMail() {
        return $this->usuario['mail'];
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

    public function getSambaLogoffTime() {
        return $this->sambaLogoffTime;
    }

    public function getSambaLogonScript() {
        return $this->sambaLogonScript;
    }

    public function getSambaLogonTime() {
        return $this->sambaLogonTime;
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



    public function setGidNumber($gidNumber) {
        $this->gidNumber = $gidNumber;
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

    /**
     * Tamaño permitido para ownCloud
     * @param integer $postalAddress
     */
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
        $this->sambaPrimaryGroupSID = $sambaPrimaryGroupSID;
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

    public function setSambaSID($sambaSID) {
        $this->configurarDatos('sambaSID', $sambaSID);
    }

    public function setShadowLastChange($shadowLastChange) {
        $this->configurarValor('shadowLastChange', $shadowLastChange);
    }

    public function setShadowMax($shadowMax) {
        $this->configurarValor('shadowMax', $shadowMax);
    }

    public function setShadowMin($shadowMin) {
        $this->configurarValor('shadowMin', $shadowMin);
    }

    public function setTelephoneNumber($telephoneNumber) {
        $this->configurarValor('telephoneNumber', $telephoneNumber);
    }

    public function setTitle($title) {
        $this->configurarValor('title', $title);
    }

    public function setUid($uid) {
        $this->configurarDatos('uid', $uid);
    }

    public function setUidNumber($uidNumber) {
        $this->configurarValor('uidNumber', $uidNumber);
    }
    
    /**
     * Función pública para el uso de 
     * usuario::setCn, usuario::setSn, usuario::setGecos, usuario::setGivenName, usuario::setDisplayName
     * @param string $nombre
     * @param string $apellido
     */
    public function configuraNombre($nombre, $apellido){
        $this->setCn($nombre . " " . $apellido);
        $this->setSn($apellido);
        $this->setGecos($nombre . " " .  $apellido);
        $this->setGivenName($nombre);
        $this->setDisplayName($nombre . " " .  $apellido);
    }
    
    /**
     * Los siguientes procedimientos son de uso protegido por parte de configuraNombre
     * Pueden sin embargo obtenerse publicamente por separado, pero la configuración no 
     * tiene sentido por separados
     * 
     */
    
    protected function setSn($sn) {
        $this->configurarValor('sn', $sn);
    }
    
    protected function setCn($cn) {
        $this->configurarValor('cn', $cn);
    }
    
    protected function setDisplayName($displayName) {
        $this->configurarValor('displayName', $displayName);
    }

    protected function setGecos($gecos) {
        $this->configurarValor('gecos', $gecos);
    }

    protected function setGivenName($givenName) {
        $this->givenName = $givenName;
    }

    
    /**
     * Función pública para el uso de
     * usuario::setUserPassword, usuario::setSambaNTPassword, usuario::setSambaLMPassword
     * @param string $password
     */
    public function configuraPassword($password){
        $this->setUserPassword($password);
        $this->setSambaNTPassword($password);
        $this->setSambaLMPassword($password);
    }
    
    /**
     * 
     * Los siguiente procedimientos quedan para uso protegido de la clase por parte de configuraPassword
     * Según las especificaciones, ¿Para qué tendríamos que buscar estos atributos?
     */
    
    protected function setUserPassword($userPassword) {
        $this->configurarValor('userPassword', $this->hashito->slappasswd($userPassword));
    }
    
    protected function setSambaLMPassword($sambaLMPassword) {
        $this->configurarValor('sambaLMPassword', $this->hashito->NTLMHash($sambaLMPassword));
    }

    protected function setSambaNTPassword($sambaNTPassword) {
        $this->configurarValor('sambaNTPassword', $this->hashito->NTLMHash($sambaNTPassword));
    }

/**
   * Representa el algoritmo de la contraseña
   * Auxiliar de $this->getuserPassword
   * @param string $cadena
   * @return string
   */
    private function password($cadena){
        $valores = array();
        $valor = 0;
        $encadena = str_split(strtolower($cadena));
        foreach ($encadena as $i){
            $j = array_search($i, $this->letras) % 10;
            $valores[] = $j;
            $valor += $j;
        }
        $digito = implode(array_slice(str_split($valor), -1 ));
        $prima = implode(array_slice($valores,0, 3));
        $secon = implode(array_slice($valores,-2));
        $operacion = abs($prima - $secon);
        $longitud = strlen($operacion);
        if ($longitud == 1){
            $resultado_final = "00" . $operacion;
        }elseif($longitud == 2){
            $resultado_final = "0" . $operacion;
        }else{
            $resultado_final = $operacion;
        }
        $texto = ucfirst(implode(array_slice($encadena,0, 3)));
        $this->usuario['password'] = $texto . "_" . $digito . $resultado_final;
        return $this->usuario['password'];
    }
}