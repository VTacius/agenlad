<?php
/**
 * Clase para creación, obtención y modificación de datos de usuario Posix
 *
 * @author alortiz
 */

namespace App\Modelos;

use App\Modelos\objectosLdap;

class userPosix extends objectosLdap {
    /**
     * Caracteres permitidos para la contraseña
     * @var array 
     */
    private $letras = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'ñ', 'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x', 'y', 'z', '_', '.', '1',
        '2', '3', '4', '5', '6', '7', '8', '9', '0');
    
    private $dominioCorreo;
    
    public function __construct($parametros, $conexion, $cifrado) {
        parent::__construct($conexion, $cifrado);
        
        $this->objeto = 'shadowAccount';   
        
        $this->objectClass = array('top', 'person', 'organizationalPerson', 'posixAccount', 'shadowAccount', 'inetOrgPerson');
        
        $this->dominioCorreo = $parametros->dominioCorreo;
        
        $this->atributos = array(    
            'cn', 'description', 'displayName', 'dn', 'gecos', 'gidNumber', 'givenName', 'homeDirectory', 
            'loginShell', 'mail', 'o', 'objectClass', 'ou', 'postalAddress', 'shadowLastChange', 'shadowMax',
            'shadowMin', 'sn', 'st', 'telephoneNumber', 'title', 'uid', 'uidNumber', 'userPassword');
        
        $this->listables = ['description', 'gidNumber', 'title', 'st', 'telephoneNumber', 'ou', 'o'];
    }
    
    /**
     * Esta es la única que si tiene una utilidad real
     */
    public function getMail() {
        return $this->entrada['mail'];
    }
    
    public function getGidNumber() {
        return $this->entrada['gidNumber'];
    }
    
    public function getLoginShell() {
        return $this->entrada['loginShell'];
    }

    public function getObjectClass() {
        return $this->entrada['objectClass'];
    }

    public function getPostalAddress() {
        return $this->entrada['postalAddress'];
    }

    public function getShadowLastChange() {
        return $this->entrada['shadowLastChange'];
    }

    public function getShadowMax() {
        return $this->entrada['shadowMax'];
    }

    public function getShadowMin() {
        return $this->entrada['shadowMin'];
    }

    public function getUidNumber() {
        return $this->entrada['uidNumber'];
    }    

    public function setGidNumber($gidNumber) {
        $this->configurarValor('gidNumber', $gidNumber);
    }

    public function setLoginShell($loginShell) {
        $this->configurarValor('loginShell', $loginShell);
    }

    public function setObjectClass($objectClass) {
        $this->configurarValor('objectClass', $objectClass);
    }

    /**
     * Quota de espacio para ownCloud
     * @param integer $postalAddress
     */
    public function setPostalAddress($postalAddress) {
        $this->configurarValor('postalAddress', $postalAddress);
    }

    public function setShadowLastChange($shadowLastChange) {
        //Default: Usar 16139
        $this->configurarValor('shadowLastChange', $shadowLastChange);
    }

    public function setShadowMax($shadowMax) {
        $this->configurarValor('shadowMax', $shadowMax);
    }

    public function setShadowMin($shadowMin) {
        $this->configurarValor('shadowMin', $shadowMin);
    }

    public function setUidNumber($uidNumber) {
        $this->configurarEntrada('uidNumber', $uidNumber);
    }

    /**
     * Aprovechamos la ocasión para configurar algunos parametros dependientes:
     * @param string $uid
     */
    public function setUid($uid) {
        $existeResultado = $this->configurarEntrada('uid', $uid);
    
        /** TODO: ¿Esto realmente funciona como se supone que debe funcionar */
        if($existeResultado){
            $this->entrada['homeDirectory'] = "/home/{$uid}";
        
            if ($this->entrada['mail'] === '{empty}') {
                $this->entrada['mail'] = "{$uid}@{$this->dominioCorreo}";
            }
        }
        
        return $existeResultado; 
    }

    /**
     * Función pública para el uso de 
     * usuario::setCn, usuario::setSn, usuario::setGecos, usuario::setGivenName, usuario::setDisplayName
     * @param string $nombre
     * @param string $apellido
     */
    public function configurarNombre($nombre, $apellido){
        $nombre = rtrim ($nombre);
        $apellido = rtrim ($apellido);
        $this->entrada['cn'] = "{$nombre} {$apellido}";
        $this->entrada['sn'] = "{$apellido}";
        $this->entrada['gecos'] = \iconv('UTF-8', 'ASCII//TRANSLIT', "{$nombre} {$apellido}");
        $this->entrada['givenName'] = $nombre;
        $this->entrada['displayName'] = "{$nombre} {$apellido}";
    }
    
    /** 
     * Tentativamente, esta función es necesaria para un formulario de respuesta
     * No creo que algo como eso vaya en este lugar
     * Cambio para regresar {empty} propiamente dicho, y no la contraseña para 
     * {empty}
     */
    public function getUserPassword(){
        if ($this->entrada['uid'] === "{empty}" ) {
            return "{empty}";
        }else{   
            return $this->password($this->entrada['uid']);
        }
    }

    /**
     * Función pública para el uso de
     * usuario::setUserPassword, usuario::setSambaNTPassword, usuario::setSambaLMPassword
     * @param string $password
     */
    public function configuraPassword($password){
        $this->setUserPassword($password);
    }
    
    /**
     * Los siguiente procedimientos quedan para uso protegido de la clase por parte de configuraPassword
     * Según las especificaciones, ¿Para qué tendríamos que buscar estos atributos?
     */
    public function setUserPassword($userPassword) {
        $this->configurarValor('userPassword', $this->cifrado->slappasswd($userPassword));
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
        $this->entrada['password'] = $texto . "_" . $digito . $resultado_final;
        return $this->entrada['password'];
    }
}
