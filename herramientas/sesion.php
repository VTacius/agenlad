<?php
require_once './clases/input_filter.class.php';
require_once ('./herramientas/utilidades.php');
// Inicio sesión
session_start();
// ¿Tengo configuradas las variables de sesión que configuramos antes?
$variables['permisos'] = array(6, 'verificaArreglo', 'n');
$variables['datos'] = array(6, 'verificaArreglo', 'n');
$variables['pass'] = array(6, 'verificaContenido', 'n');
$variables['user'] = array(6, 'verificaNombres', 'n');
$variables['rol'] = array(6, 'verificaNombres', 'n');

$v = new verificador($variables);
if ($v->comprobar()) {
  $sesion = $v->resultar();
  $lugarActual = rtrim(ltrim($_SERVER['PHP_SELF'], "/"),".php");
  $sesion['pagina'] =  $lugarActual;
  if (array_key_exists($lugarActual, $sesion['permisos'])) {
    $menu = $sesion['permisos'];
  }else{
    header("HTTP/1.0 404 Not Found");
    exit();
  }
}else{
 $template = $twig->loadTemplate('login.html.twig');
 $parametros = array(
          'mensaje' => "Debe iniciar sesión para usar el sistema"
    );
  $template->display($parametros);
  exit();
}



