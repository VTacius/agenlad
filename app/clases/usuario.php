<?php
namespace clases;
use Exception;
/**
 * Clase para creación, obtención y modificación de datos de usuario
 *
 * @author alortiz
 */


/**
 * 
 * Vas a usar user.php, solo agrega las cosas de acá que no estén allí
 * 
 * Vas bien, pero te hace falta mucho trabajo duro para terminar
 * 
 */
class usuario extends \clases\controlLDAP{


//  $SIDSamba = "S-1-5-21-371878337-141820978-2368272707", 
//  $PDC = "PDC-DEBIAN"){

  
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
  
}
