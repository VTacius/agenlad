<?php
require_once './clases/input_filter.class.php';
// Inicio sesión
session_start();
// ¿Tengo configuradas las variables de sesión que configuramos antes?
$variables['permisos'] = array(6, 'verificaArreglo', 'u');
$variables['datos'] = array(6, 'verificaArreglo', 'u');
$variables['pass'] = array(6, 'verificaContenido', 'u');
$variables['user'] = array(6, 'verificaNombres', 'u');
$variables['rol'] = array(6, 'verificaNombres', 'u');

$v = new verificador($variables);
if ($v->comprobar()) {
  $sesion = $v->resultar();
  $sesion['PHP_SELF'] =  $_SERVER['PHP_SELF'];
  $lugarActual = rtrim(ltrim($sesion['PHP_SELF'], "/"),".php");
  if (array_key_exists($lugarActual, $sesion['permisos'])) {
    $menu = $lg['permisos'];
  }else{
    header("HTTP/1.0 404 Not Found");
    exit();
  }
}else{
  print "Debe autenticarse para usar el sistema";
  exit();
}



