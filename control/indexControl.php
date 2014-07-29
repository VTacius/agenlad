<?php
// Se encarga del cifrado
require_once ('/var/www/agenlad/clases/cifrado.class.php');
// Encargada de la conexión ldap
require_once ('/var/www/agenlad/clases/conect.class.php');
// Encargada de la conexión a base de datos
require_once ('/var/www/agenlad/clases/bd.class.php');
// Encargada de la magia con las variables
require_once ('/var/www/agenlad/clases/input_filter.class.php');

$server = configuracion("server");
$puerto = configuracion("puerto");
$dominio = configuracion("dominio");

// Objeto de la clase cifrado
$base = new controlDB();
$hashes = new cifrado();
$setPass = new controlLDAP();

/**
 * Crea los hash para actualizar contraseñas dentro de los atributos LDAP
 * @global cifrado $hashes
 * @param string $passito
 * @return array
 */
function hashPasswordLdap($passito){
    global $hashes;    
	// Creamos un array con todas las contraseñas requeridas para que el usuario pueda cambiarlas realmente
	$password = array();
	$password['userPassword'] = $hashes->slappasswd($passito);
	$password['sambaNTPassword'] = $hashes->NTLMHash($passito);
	$password['sambaLMPassword'] = $hashes->LMhash($passito);
    return $password;
}

/**
 * Configura los hashes necesarios en LDAP para considerar cambiada a la contraseña
 * @global string $server
 * @global integer $puerto
 * @global string $dominio
 * @global controlLDAP $setPass
 * @param string $user
 * @param string $pass
 * @param array $password
 * @return string
 */
function setPasswordLdap($user, $pass, $password){
    // Objeto de la clase conect.class.php
	// La implementación básica de la clase
    global $server, $puerto, $dominio, $setPass;
    
	$setPass->conexion($server, $puerto);
    $base = $setPass->crearBase($dominio);
	$setPass->enlace($user, $base, $pass);
    
	// Le pasamos como parametro un array cuyos índices se corresponden con los atributos 
	// que estamos modificando
	if ( $setPass->modEntrada($password) ){
		return "La contraseña se ha cambiado con exito";
	}else{
		return $setPass->mostrarError();
	}
}

/**
 * 
 * @global controlDB $base
 * @global cifrado $hashes
 * @param string $usuario
 * @param string $passito
 */
function cifrarPasswordBD($usuario, $passito){
    global $base, $hashes;
    $contra = $base->obtenerFirma($usuario);
    $firmas = $hashes->descifrada($contra['firmas'], $passito);
    $firmaz = $hashes->descifrada($contra['firmaz'], $passito);
    // Creamos las firmas;
    $claves = $hashes->encrypt($firmas, $passito);
    $clavez = $hashes->encrypt($firmaz, $passito);
    // Ahora, que actualice la firma en la base de datos con la nueva contraseña
    $base->configuraFirma($usuario, $claves, $clavez);
}

/**
 * Cambio de contraseña para usuarios normales
 * Basta con usar setPasswordLdap con el resulta de hashPasswordLdap como parametro
 * @param string $user
 * @param string $pass
 * @param string $passito
 * @return string
 */
function ejecutar_cambio($user,$pass, $passito){
    // Obtenemos los hash de la contraseña
    $password = hashPasswordLdap($passito);
    // Configuramos la contraseña en en LDAP 
    return setPasswordLdap($user, $pass, $password);
	
}

/**
 * Cambio de contraseña para usuarios administrador o roles parecidos
 * @param string $user
 * @param string $pass
 * @param string $passito
 * @return string
 */
function ejecutar_cambio_admin($user,$pass, $passito){
    // Obtenemos los hash de la contraseña
    $password = hashPasswordLdap($passito);
    // Actualizamos los hash de sus contraseñas en la base de datos
    cifrarPasswordBD($user, $passito);
	//	Configuramos la contraseña en en LDAP 
    return setPasswordLdap($user, $pass, $password);
}


// Acá esta todo el desarrollo del script
$variables['passchangeprima'] = array(6, 'verificaContenido', 'n');
$variables['user'] = array(6, 'verificaContenido', 'n');
$variables['pass'] = array(6, 'verificaContenido', 'n');
$variables['rol'] = array(6, 'verificaContenido', 'n');

$vindex = new verificador($variables);
if ($vindex->comprobar()) {
    $index = $vindex->resultar();
    $passito = $index['passchangeprima'];
    $user = $index['user'];
    $pass = $index['pass'];
    $rol = $index['rol']; 
    if ($rol !== 'usuario') {
        $msg =  ejecutar_cambio_admin($user, $pass, $passito);
    }else{
        $msg =  ejecutar_cambio($user,$pass,$passito);	
    }
    $template = $twig->loadTemplate('login.html.twig');
    $parametros = array(
        'mensaje' => $msg
    );
    $template->display($parametros);
}else{
    $msg = "Revise los datos introducidos";
    $template = $twig->loadTemplate('index.html.twig');
    $parametros = array(
        'mensaje' => $msg
    );
    $template->display($parametros);
}


