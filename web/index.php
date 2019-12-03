<?php
/**
 * Esta es la forma en que iniciamos el framework. 
 * Parece estar bajo el patrón Singleton, por lo que esta es la variable que ha de comunicar a todos
 */
include_once '../vendor/autoload.php';

$index = \Base::instance();

/**
 * Leemos el fichero de configuración
 */
$index->config('./../setup.cfg');

/**
 * Ponemos a nuestro disposición todo nuestro código
 */
$index->set('AUTOLOAD','./../app/');

/**
 * Configuramos como tratar los errores
 */

$index->set('ONERROR', function($base){
    $base->error($base['ERROR']['code'], json_encode($base['ERROR']['trace']));
});

/**
 * Conecta a la base de datos
 */
$db = $index->get('db');
$db_base = $db['base'];
$db_server = $db['server'];
$db_usuario = $db['usuario'];
$db_password = $db['password'];

$dsn = "pgsql:host=${db_server};port=5432;dbname={$db_base}";
$index->set('dbconexion', new \DB\SQL($dsn, $db_usuario, $db_password, array( \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION , \PDO::ATTR_TIMEOUT => 5)));

/**
 * Configura la clave que ayuda en el cifrado de datos
 */
$index->set('claveMaestra', $_ENV['CLAVEMAESTRA']); 

/**
 * Configuramos las rutas
 */

// Rutas hacia usershow
$index->route('GET /usuarios/@usuario [ajax]', 'App\Controladores\usuario\Listado->detalles');
$index->route('GET /usuarios [ajax]', 'App\Controladores\usuario\Listado->lista');
// Rutas hacia login
$index->route('POST /login [ajax]', 'App\Controladores\Login->autenticar');
// Rutas hacia usermod
$index->route('PUT /usuarios/@usuario [ajax]', 'App\Controladores\usuario\Actualizacion->modificarUsuario');
//Rutas hacia useradd
$index->route('GET @useradd: /useradd', 'Controladores\usuario\useraddControl->display');
$index->route('POST @useradd_creacion: /useradd/creacion', 'Controladores\usuario\useraddControl->creacionUsuario');
$index->route('GET|POST @useradd_creacion_test: /useradd/test', 'Controladores\usuario\useraddPrueba->display');
$index->route('GET|POST @useradd_check_uid: /useradd/checkuid', 'Controladores\usuario\useraddControl->checkUid');
// Agregado el 26/06/15 para que los usuarios puedan actualizar por si mismos sus datos
$index->route('GET|POST @usuario_actualizacion: /actualizacion', 'Controladores\usuario\userActualizacion->display');
$index->route('GET|POST @usuario_actualizacion_cambio: /actualizacion/cambio', 'Controladores\usuario\userActualizacion->actualizacionCambio');
$index->route('GET|POST @usuario_actualizacion_cambio: /actualizacion/usuario', 'Controladores\usuario\userActualizacion->getUsuario');
// Rutas para obtención de datos por parte de todas las partes de la aplicación
$index->route('POST @helpers_establecimientos: /helpers/establecimiento [ajax]', 'Controladores\helpers->getEstablecimiento');
$index->route('POST @helpers_oficinas: /helpers/oficina [ajax]', 'Controladores\helpers->getOficinas');

// Esta es la forma en que la aplicación empieza
$index->run();