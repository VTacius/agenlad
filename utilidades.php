<?php
//Quién invoque este fichero podrá usar plantillas twig
require_once('./conect.class.php');
require_once './Twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
  'cache' => './tmp/cache',
));
//Nombre del fichero con las configuraciones
$parametros_aplicacion = './parametros.ini';
function configuracion ($clave, $seccion='conexion') {
  global $parametros_aplicacion;
  /**
   * Devuelve un valor del archivo de configuración parametros.ini
   * con la sección conexión por defecto
   */
  $config = parse_ini_file($parametros_aplicacion, TRUE);
  return $config[$seccion][$clave];
}


