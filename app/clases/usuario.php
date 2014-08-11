<?php
namespace clases;
use Exception;
/**
 * Clase para creación, obtención y modificación de datos de usuario
 *
 * @author alortiz
 */


/**
 * Vas a usar user.php, solo agrega las cosas de acá que no estén allí
 */
class usuario extends \clases\controlLDAP{
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
    
    /** @var string Identificador único del usuario dentro de LDAP */
    private $uid; 
    /** @var array Arreglo de los caracteres permitidos en la contraseña*/
    private $letras = array(
        'a','b','c','d','e',
        'f','g','h','i','j',
        'k','l','m','n','ñ',
        'o', 'p','q','r','s',
        't','u','v','w','x',
        'y','z','_','.','1',
        '2','3','4','5','6',
        '7','8','9','0');
    /** @var integer */
    private $gidNumber;
    /** @var string */
    private $grupoPrimario;
    /** @var string.*/
    private $userPassword;
    /** @var string */
    private $cn;
    /** @var string */
    private $o;
    /** @var string */
    private $ou;
    /** @var array */
    private $usuario = array();
    
    public function obtener($attibuto){
        return isset($this->usuario[$attibuto])? $this->usuario[$attibuto] : '-' ;
    }
    /**
     * Configure el usuario sobre el cual hemos de trabajar
     * @param string $uid
     */
    public function setuid($uid){
        $this->uid = $uid;
        $filtro = "uid=$this->uid";
        $this->usuario = $this->getDatos($filtro, $this->atributos)[0];
    }
    
    public function getgidNumber(){
        return $this->usuario['gidNumber'] ;
    }
    
    public function getuserPassword(){
        return $this->password($this->uid);
    }
    
    public function setuserPassword($userPassword){
        $this->userPassword = $userPassword;
    }
    
    public function getGrupoPrimario() {
        return $this->grupoPrimario[0]['cn'];
    }

    public function setGrupoPrimario($grupoPrimario) {
        $this->grupoPrimario = $grupoPrimario;
    }

    public function getCn() {
        return $this->obtener('cn');
    }

    public function setCn($cn) {
        $this->cn = $cn;
    }
    
    public function getO() {
        return isset($this->usuario['o'])? $this->usuario['o'] : '-' ;
    }

    public function setO($o) {
        $this->o = $o;
    }

    public function getOu() {
        return isset($this->usuario['ou'])? $this->usuario['ou'] : '-' ;
    }

    public function setOu($ou) {
        $this->ou = $ou;
    }

    
    public function __construct($rdnLDAP, $passLDAP) {
        parent::__construct($rdnLDAP, $passLDAP);
    }


    /**
   * Inicializa la clase con una conexión ldap con nivel de administrador
   * Su nivel de usuario debe ser administrador para tener la contraseña necesaria
   * @param string $server
   * @param string $port
   * @param string $user
   * @param string $base
   * @param string $pass
   * @param string $SIDSamba
   * @param string $PDC Nombre NetBIOS del servidor
   */
//  function adduser ($server, 
//  $port, 
//  $user, 
//  $base, 
//  $pass , 
//  $rol='admin', 
//  $SIDSamba = "S-1-5-21-371878337-141820978-2368272707", 
//  $PDC = "PDC-DEBIAN"){
//  //    $this->SIDSamba = $SIDSamba;
//    $this->PDC = $PDC;
//    $this->conexion($server, $port);
//    $this->crearDN($user,$base, $rol);
//    if (!($this->enlace($pass))){
//      print $this->mostrarERROR();
//    }
//    $this->hashes = new cifrado();
//  }
  
  /**
   * Ninguna consulta hecha 
   * Llena el array con datos para el usuario
   * El array objectClass como primer atributo
   */
//  function cargardatos  () {
//    $this->usuario["objectClass"][0] = "top";
//    $this->usuario["objectClass"][1] = "person";
//    $this->usuario["objectClass"][2] = "organizationalPerson";
//    $this->usuario["objectClass"][3] = "inetOrgPerson";
//    $this->usuario["objectClass"][4] = "posixAccount";
//    $this->usuario["objectClass"][5] = "shadowAccount";
//    $this->usuario["objectClass"][6] = "sambaSamAccount";
//  }
  
  /**
   * Consulta por todos los uid
   * Verifica que $uid no este siendo usado
   * Guarda en $usuario['verificauid'] un mensaje sobre dicha operación
   * @param string $uid
   * @param string $base
   * @return boolean
   */
//  function verificauid  ($uid, $base) {
//    $atributos = array('uid');
//    $filtro = "uid=$uid";
//    $basel = $this->crearBase($base);
//    $this->datos($basel, $filtro, $atributos, 5);
//    $listado = $this->arrayDatosLDAP($atributos);
//    // Devolvemos falso o verdadero y el resultado lo tiramos aparte
//    if (empty($listado)){
//      $this->usuario['verificauid'] = "Usuario válido";
//      return TRUE;
//    }else{
//      $this->usuario['verificauid'] = "Ese usuario ya esta ocupado";
//      return FALSE;
//    }
//  }
  
  /**
   * Consulta por todos los uid 
   * Crea el UID verificando que sea único, caso contrario lo va creando
   * Hasta en tres ocasiones
   * @param string $nombre
   * @param string $apellido
   * @param string $base
   */
//  function crearuid     ($nombre, $apellido, $base) {
//    $i=0;
//    while(true){
//      $pre_nombre = explode(" ", $nombre);
//      $pre_apellido = explode(" ", $apellido);
//      $int_posible = count($pre_apellido);
//      $p = $i > $int_posible ? 0 :  1;
//      if ($i<=1) {
//        $nombres = str_split($pre_nombre[0]);
//        $uid = $nombres[0] . $pre_apellido[$p-1] ;
//      }else{
//        $uid = $nombres[0] . $pre_apellido[0] . $pre_apellido[$p][0] ;
//      }
//      if ( $this->verificauid( $uid,$base) ){
//        break;
//      } elseif ($i >= 2) {
//        $uid = "No pudimos crear un usuario único";
//        break;
//      }
//      $i +=1;
//    }
//    $this->usuario['uid'] = strtolower($uid);
//    return $uid;
//  }
  
  /**
   * Ninguna consulta hecha 
   * Atributos relacionados con el nombre
   * @param string $uid
   * @param string $nombre
   * @param string $apellido
   */
//  function nombreusario ( $uid, $nombre, $apellido ){
//    $this->usuario['uid']        = $uid;
//    $this->usuario['cn']         = $nombre . " " . $apellido;
//    $this->usuario['sn']         = $apellido ;
//    $this->usuario['gecos']      = $nombre . " " .  $apellido;
//    $this->usuario['givenName']  = $nombre ;
//    $this->usuario['displayName']= $nombre . " " .  $apellido;
//  }
  
  /**
   * Ninguna consulta hecha
   * Llena el array con datos para el usuario
   * @param string $uid
   */
//  function homeusuario  ( $uid ) {
//    $this->usuario['homeDirectory'] = "/home/$uid" ;
//  }
  
  /**
   * Ninguna consulta hecha 
   * Llena el array con datos para el usuario
   * @param string $uid
   * @param string $baseDNS
   */
//  function mailusuario  ( $uid, $baseDNS ) {
//    $this->usuario['mail'] = "$uid$baseDNS";
//  }
  
  /**
   * Ninguna consulta hecha 
   * Llena el array con datos para el usuario
   * @param string $uid
   */
//  function sambapath    ( $uid ) {
//    $this->usuario["sambaHomePath"] = "\\\\$this->PDC\\$uid";
//  }
  
  /**
   * Consulta por todos los uidNumber
   * Crea el uidNumber y verifica que sea único
   * @param string $base
   */
//  function crearuidnumber ( $base ) {
//    $atributos = array( 'uidnumber' );
//  // Sacamos a root y nobody de la búsqueda
//    $filtro = "(!(|(uid=nobody)(uid=root)))";
//    $basel = $this->crearBase($base, 'usuario');
//    $this->datos($basel, $filtro, $atributos, 0);
//    $listado = $this->arrayDatosLDAP($atributos);
//    $items = count($listado) - 1;
//  // En dos cosas confiamos: Como esta ordenado de mayor a menor, el último uidNumber 
//  // debe ser el mayor
//    $uidNumber         = $listado[$items]['uidnumber'] + 1;
//    $this->usuario['uidNumber'] = $uidNumber;
//  }
  
  /**
   * Ninguna consulta hecha 
   * Llena el array con datos para el usuario
   * Crea el sambaSID en base a uidNumber
   * @param type $uid
   * @return string
   */
//  function crearsambasid  ($uid) {
//    $sambaSID = $this->SIDSamba . "-" . strval(($uid *2) + 1000);
//    $this->usuario['sambaSID'] = $sambaSID;
//  }
  
  /**
   * Ninguna consulta hecha 
   * Llena el array con datos para el usuario
   * @param string $cargo
   */
//  function cargousuario   ($cargo) {
//    $this->usuario['title'] = $cargo;	
//  }
  
  /**
   * Ninguna consulta hecha
   * Llena el array con datos para el usuario
   * @param string $telefono
   */
//  function telefono       ( $telefono ) {
//    $this->usuario['telephoneNumber'] = $telefono;
//  }
  
  /**
   * Ninguna consulta hecha 
   * Llena el array con datos para el usuario
   * @param string $shell
   */
//  function loginshell     ( $shell ){
//    $this->usuario['loginshell'] = $shell;	
//  }
  
  /**
   * Ninguna consulta hecha
   * Llena el array con datos para el usuario
   * @param string $company
   * @param string $office
   */
//  function company    ( $company, $office ){
//    $this->usuario['o'] = $company;
//    $this->usuario['ou']= $office;
//  }
    
  /**
   * Ninguna consulta hecha 
   * Llena el array con datos para el usuario
   */
//  function defposix   () {	
//      $this->usuario['shadowMin'] = "99999";	
//      $this->usuario['shadowMax'] = "99999";	
//      $this->usuario['shadowLastChange'] = "16139";
//  }
  
  /**
   * Ninguna consulta hecha 
   * Llena el array con datos para el usuario
   */
//  function defsamba   (){
//    $this->usuario['sambaPwdLastSet'] = "1394471418";
//    $this->usuario['sambaPwdMustChange'] = "10034385018";
//    $this->usuario["sambaLogonTime"] = 0;
//    $this->usuario["sambaLogoffTime"] = 2147483647;
//    $this->usuario["sambaKickoffTime"] = 2147483647; 
//    $this->usuario["sambaPwdCanChange"] = 0;
//    $this->usuario["sambaLogonScript"] = "logon.bat";
//    $this->usuario["sambaHomeDrive"] = "H:";
//  }
  
  /**
   * Ninguna consulta hecha 
   * Llena el array con datos para el usuario
   */
//  function estatutoUser ( $statuto ) {  
//    $bandera = ($statuto == 1)? "[U]" : "[DU]";
//    $this->usuario["sambaAcctFlags"] = $bandera;
//  }
  
  /**
   * Consulta para averiguar gidNumber y sambaSID del grupo
   * Verifica la existencia del grupo
   * @param string $grupo
   * @param string $base
   */
//  function datosgrupo ( $grupo, $base ) {
//    $atributos = array('cn','gidnumber','sambasid');
//    $filtro = "cn=$grupo";
//    $basel = $this->crearBase($base, 'grupo');
//    $this->datos($basel, $filtro, $atributos, 5);
//    $listado = $this->arrayDatosLDAP($atributos);
//    if (count($listado) >= 1) {
//      $this->usuario['gidNumber'] =  $listado[0]['gidnumber'];
//      $this->usuario['sambaPrimaryGroupSID'] = $listado[0]['sambasid'];
//    }else{
//      $this->usuario['gidNumber'] = "Ese grupo no existe";
//    }
//    
//  }
  
  /**
   * Consulta para averiguar gidNumber y sambaSID del grupo
   * Apoya la funcionalidad del formulario
   * @param string $base
   * @return string
   */
//  function listargrupos ( $base ) {
//      $atributos = array("cn","gidnumber");
//      $filtro = "(objectClass=posixGroup)";
//      $basel = $this->crearBase($base, 'grupo');
//      $this->datos($basel, $filtro, $atributos, 500);
//      $listado = $this->arrayDatosLDAP($atributos);
//      return $this->datosSelect($atributos, $listado); 
//  }
  
  /**
   * Ninguna consulta hecha
   * Uso un objeto de la clase cifrado
   * @param string $passito
   */
//  function password ( $passito ) {
//    $this->usuario['Password'] = $passito;
//    $this->usuario['userPassword'] = $this->hashes->slappasswd($passito);
//    $this->usuario['sambaNTPassword'] = $this->hashes->NTLMHash($passito);
//    $this->usuario['sambaLMPassword'] = $this->hashes->LMhash($passito);
//  }

  /**
   * Ninguna consulta hecha
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
