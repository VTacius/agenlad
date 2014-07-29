<?php
require_once('./clases/utilidades.php');
require_once('./clases/crearSesion.class.php');
require_once('./clases/conect.class.php');
require_once('./clases/input_filter.class.php');

// Capturamos las variables desde el fichero de configuracion
$server = configuracion('server');
$puerto = configuracion('puerto');
$dominio = configuracion('dominio');

// Capturemos las variables del formulario de logueo
$pv['user']     = array(0, 'verificaNombres', 'n');
$pv['pswd']     = array(0, 'verificaContenido', 'n');
$pv['REMOTE_ADDR'] = array(5, 'verificaContenido', 'n' );

$login = new controlLDAP();
$vlogin = new verificador($pv);

// Necesitas la plantilla en general para fabricar la página normalmente o para
// Mostrar errores
$template = $twig->loadTemplate('login.html.twig');


if ($vlogin->comprobar()){
    // Las variables introducidas son válidas
    $val = $vlogin->resultar();
    try {
        $crearSesion = new crearSesion($server, $puerto, $val['user'], $val['pswd'], $dominio);
        // La redirección a la página de inicio esta dentro de la clase
        $crearSesion->sesionar($val['user'], $val['pswd'], $val['REMOTE_ADDR']);
    } catch (Exception $e) {
      // Si algo de todo lo que puede fallar dentro de la clase falla, capturamos el
      // mensaje de error y se lo pasamos a la plantilla
        $parametros = array(
          'mensaje' => $e->getMessage()
        );
        $template->display($parametros);
    }     
}else{
    $parametros = array(
          'mensaje' => "Hay un problema con los datos introducidos"
    );
    $template->display($parametros);
}
