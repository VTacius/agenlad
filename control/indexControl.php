<?php
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && isset($_POST['accion']))){ 
	header("Location: index.php");
}
// Hare todo el manejo desde acá. Entonces
ini_set('default_charset','UTF-8');

//Traemos a cuenta la ayuda necesaria
require_once ('./clases/config.php');
require_once ('./clases/conect.class.php');
require_once ('./clases/cifrado.class.php');
require_once ('./clases/bd.class.php');

function ejecutar_cambio($user,$pass, $passito){
  global $server, $port, $base;
	// Objeto de la clase cifrado
	// Creamos un array con todas las contraseñas requeridas para que el usuario pueda cambiarlas realmente
	$hashes = new cifrado;
	$password = array();
	$password['userPassword'] = $hashes->slappasswd($passito);
	$password['sambaNTPassword'] = $hashes->NTLMHash($passito);
	$password['sambaLMPassword'] = $hashes->LMhash($passito);

	// Objeto de la clase conect.class.php
	// La implementación básica de la clase
	$setPass = new controlLDAP();
	$setPass->conexion($server, $port);
	$setPass->enlace($user, $base, $pass);
	// Le pasamos como parametro un array cuyos índices se corresponden con los atributos 
	// que estamos modificando
	if ( $setPass->modEntrada($password) ){
		return "La contraseña se ha cambiado con exito";
	}else{
		return $setPass->mostrarError();
	}
}

function ejecutar_cambio_admin($user,$pass, $passito){
   global $server, $port, $base;
	// Objeto de la clase cifrado
	// Creamos un array con todas las contraseñas requeridas para que el usuario pueda cambiarlas realmente
	$hashes = new cifrado;
	$password = array();
	$password['userPassword'] = $hashes->slappasswd($passito);
	$password['sambaNTPassword'] = $hashes->NTLMHash($passito);
	$password['sambaLMPassword'] = $hashes->LMhash($passito);
    
    // Obtenemos la contraseña desde la base de datos y las desciframos
    $input = obtenerFirma($user);
    $firmas = $hashes->descifrada($input['firmas'], $pass);
    $firmaz = $hashes->descifrada($input['firmaz'], $pass);
	// Objeto de la clase conect.class.php
	// La implementación básica de la clase
	$setPass = new controlLDAP();
	$setPass->conexion($server, $port);
	$setPass->enlace($user, $base, $pass);
    // Creamos las firmas;
    $claves = $hashes->encrypt($firmas, $passito);
    $clavez = $hashes->encrypt($firmaz, $passito);
    // Ahora, que actualice la firma en la base de datos con la nueva contraseña
    configuraFirma($user, $claves, $clavez);
	// Le pasamos como parametro un array cuyos índices se corresponden con los atributos 
	// que estamos modificando
	if ( $setPass->modEntrada($password) ){
		return "La contraseña se ha cambiado con exito";
	}else{
		return $setPass->mostrarError();
	}
}

$accion = $_POST['accion'];

// Este es el cuerpo del programa. 
switch ($accion) {
	case 'cambiarpassword':
// Iniciamos sesión para ver si ya se ha logueado
	session_start();
// Una vez iniciada sesión, vemos que es lo que vamos a hacer
	if(isset($_SESSION['user'])){
      $user = $_SESSION['user'];
      $pass = $_SESSION['pass'];
      $passito = $_POST['passchangeprima'];

    // TODO: Enviar un mensaje a la vista para que el usuario sepa que debe cambiarla
      $rol = $_SESSION['rol'];
      if ($rol !== 'usuario') {
        print ejecutar_cambio_admin($user, $pass, $passito);
      }else{
        print ejecutar_cambio($user,$pass,$passito);	
      }
    }
	break;
}


?>