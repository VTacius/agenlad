<?php
//Quién invoque este fichero podrá usar plantillas twig
require_once('./clases/conect.class.php');
require_once './Twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
  'cache' => './tmp/cache',
));

//Nombre del fichero con las configuraciones
$parametros_aplicacion = './parametros.ini';

/**
 * 
 * @global string $parametros_aplicacion Nombre del archivo con la configuración
 * @param type $clave Atributo que ha de buscarse en el archivo
 * @param type $seccion Seccion del archivo en la cual buscar
 * @return type
 */
function configuracion ($clave, $seccion='conexion') {
  global $parametros_aplicacion;
  /**
   * Devuelve un valor del archivo de configuración parametros.ini
   * con la sección conexión por defecto
   */
  $config = parse_ini_file($parametros_aplicacion, TRUE);
  return $config[$seccion][$clave];
}


