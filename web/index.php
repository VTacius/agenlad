<?php
/**
 * Esta es la forma en que iniciamos el framework. 
 * Parece estar bajo el patrón Singleton, por lo que esta es la variable que ha de comunicar a todos
 */
include_once '../vendor/autoload.php';

$index = \Base::instance();

/**
 * Leemos el fichero de configuración y configuramos las variables para todo el proyecto
 */
$index->config(__DIR__ . '/../parametros.ini');

/**
 * Ponemos a nuestro disposición todo nuestro código
 */
$index->set('AUTOLOAD','../app/');

/**
 * Nos encargamos de convocar el poder de Twig
 */
$twig_loader = new Twig_Loader_Filesystem(__DIR__ . '/../app/plantillas');

$index->set('twig', 
    $twig = new Twig_Environment($twig_loader, array(
        'cache' => __DIR__ . '/../tmp/cache',
        'auto_reload' => true,
    ))
);

/**
 * Esta función Twig permite usar archivos estáticos desde una ubicación fija
 */
$activos = new Twig_SimpleFunction('activos', function ($activos) {
    return '/' . $activos;
});


$emptiador = new Twig_SimpleFilter('emptiador', function ($valor) {
    return ($valor === "{empty}") ? "" : $valor ;
});

$twig->addFunction($activos);
$twig->addFilter($emptiador);

/**
 * Agregamos la extensión debug para twig, pero en producción sería recomendable quitarla porque no se supone que la uses
 */
$twig->addExtension(new Twig_Extension_Debug());

/**
 * Conecta con la base de datos según los valores que recogimos en parametros.ini
 */
$db_base = $index->get('db_base');
$db_server = $index->get('db_server');
$db_usuario = $index->get('db_usuario');
$db_password = $index->get('db_password');

$dsn = "pgsql:host=$db_server;port=5432;dbname=$db_base";

try{
    $index->set('dbconexion',new \DB\SQL($dsn, $db_usuario, $db_password, array( \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION , \PDO::ATTR_TIMEOUT => 5)));
}catch (\PDOException $e){
    print $e->getMessage();
    print_r($e);
}

/**
 * Configuramos las rutas
 * ADVERTENCIA: Deben ser absolutas cuando se haga referencia a ellas dentro de HTML
 */

// Rutas hacia directorio
$index->route('GET|POST @directorio: /directorio [sync]', 
        'Controladores\directorioControl->display');
$index->route('GET|POST @directorio_busqueda: /directorio/busqueda', 
        'Controladores\directorioControl->mostrarUsuario');
//Rutas hacia login
$index->route('GET|POST @login: /login',
        'Controladores\loginControl->display');
$index->route('GET|POST @login_inicio: /login/inicio', 
        'Controladores\loginControl->autenticar');
$index->route('GET|POST @login_final: /login/final', 
        'Controladores\loginControl->cerrar');
$index->route('GET|POST @login_finalMensaje: /login/cambio/@mensaje', 
        'Controladores\loginControl->cerrarMensaje');
$index->route('GET|POST @login_mensaje: /login/@mensaje',
        'Controladores\loginControl->display');
//Rutas hacia main
$index->route('GET|POST @main: /', 
        'Controladores\mainControl->display');
$index->route('GET|POST @main_index: /main', 
        'Controladores\mainControl->display');
$index->route('POST @cambiologin: /main/cambio [ajax]',
        'Controladores\mainControl->cambioCredenciales');
//Rutas hacia usershow
$index->route('GET|POST @tecnico: /usershow', 
        'Controladores\usuario\usershowControl->display');
$index->route('GET|POST @tecnicoDatos: /usershow/datos [ajax]', 
        'Controladores\usuario\usershowControl->datos');
//Rutas hacia usermod
$index->route('GET|POST @usermod: /usermod', 
        'Controladores\usuario\usermodControl->display');
$index->route('GET|POST @usermod_modificar: /usermod/envio', 
        'Controladores\usuario\usermodControl->mostrarUsuarioPost');
$index->route('GET|POST @usermod_modificar: /usermod/@usuarioModificar', 
        'Controladores\usuario\usermodControl->mostrarUsuarioGet');
$index->route('POST @usermod_modificar: /usermod/cambio [ajax]', 
        'Controladores\usuario\usermodControl->modificarUsuario');
$index->route('POST @zimbra_modificar: /usermod/zimbra [ajax]', 
        'Controladores\usuario\usermodControl->modificarBuzon');
//Rutas hacia useradd
$index->route('GET|POST @useradd: /useradd', 
        'Controladores\usuario\useraddControl->display');
$index->route('GET|POST @useradd_creacion: /useradd/creacion', 
        'Controladores\usuario\useraddControl->creacionUsuario');
$index->route('GET|POST @useradd_creacion_test: /useradd/test', 
        'Controladores\usuario\useraddPrueba->display');
$index->route('GET|POST @useradd_check_uid: /useradd/checkuid', 
        'Controladores\usuario\useraddControl->checkUid');
// Estas son algunas rutas de pruebas, que espero que no sean muchas
$index->route('GET|POST @prueba_user: /pruebas', 
        'Controladores\pruebaControl->display');
$index->route('GET|POST @prueba_paginacion: /pruebas/paginacion', 
        'Pruebas\paginacion->display');
$index->route('GET|POST @prueba_useradd: /pruebas/useradd', 
        'Pruebas\useraddPrueba->display');
$index->route('GET|POST @prueba_useradd: /pruebas/busqueda/@term', 
        'Controladores\pruebaControl->busqueda');
$index->route('GET|POST @prueba_getdatos: /pruebas/getdatos', 
        'Pruebas\getdatos->display');
$index->route('GET|POST @prueba_userupdate: /pruebas/userupdate', 
        'Controladores\usuario\userActualizacion->pruebas');
$index->route('GET|POST @prueba_comprobar_establecimiento: /pruebas/establecimiento', 
        'Controladores\usuario\usershowControl->comprobarEstablecimientoPrueba');
// Rutas para configuracion de dominios
$index->route('GET|POST @conf_dominios: /confdominios', 
        'Controladores\configuracion\dominioControl->display');
$index->route('GET|POST @conf_dominio_modificar: /confdominios/modificar', 
        'Controladores\configuracion\dominioControl->modificarDominios');
$index->route('GET|POST @conf_dominio_modificar: /confdominios/nuevo', 
        'Controladores\configuracion\dominioControl->mostrarNuevoDominio');
$index->route('GET|POST @conf_dominio_modificar: /confdominios/nuevo/crear', 
        'Controladores\configuracion\dominioControl->crearDominio');
$index->route('GET|POST @conf_dominio_set_password_samba: /confdominios/password/samba', 
        'Controladores\configuracion\dominioControl->setPasswordSamba');
$index->route('GET|POST @conf_dominio_set_password_zimbra: /confdominios/password/zimbra', 
        'Controladores\configuracion\dominioControl->setPasswordZimbra');
$index->route('GET|POST @conf_dominio_detalles: /confdominios/@clave', 
        'Controladores\configuracion\dominioControl->mostrarDetalles');
// Rutas para configuracion de usuarios
$index->route('GET|POST @conf_usuario: /confpermisos', 
        'Controladores\configuracion\usuarioControl->display');
$index->route('GET|POST @conf_usuario_busqueda: /confpermisos/busqueda/@term', 
        'Controladores\configuracion\usuarioControl->busqueda');
$index->route('GET|POST @conf_usuario_busqueda_rol: /confpermisos/rol/', 
        'Controladores\configuracion\usuarioControl->datosRolUsuario');
$index->route('GET|POST @conf_usuario_configuracion_rol: /confpermisos/configurarol', 
        'Controladores\configuracion\usuarioControl->configuracionRolUsuario');
// Rutas para Inicializacion de la aplicacion
$index->route('GET|POST @conf_usuario_busqueda_rol: /inicializacion', 
        'Controladores\configuracion\inicializacion->display');
$index->route('GET|POST @conf_usuario_busqueda_rol: /inicializacion/usuario', 
        'Controladores\configuracion\inicializacion->usuario');
// Agregado el 26/06/15 para que los usuarios puedan actualizar por si mismos sus datos
$index->route('GET|POST @usuario_actualizacion: /actualizacion', 
        'Controladores\usuario\userActualizacion->display');
$index->route('GET|POST @usuario_actualizacion_cambio: /actualizacion/cambio', 
        'Controladores\usuario\userActualizacion->actualizacionCambio');
$index->route('GET|POST @usuario_actualizacion_cambio: /actualizacion/usuario', 
        'Controladores\usuario\userActualizacion->getUsuario');
// Rutas para obtención de datos por parte de todas las partes de la aplicación
$index->route('POST @helpers_establecimientos: /helpers/establecimiento [ajax]', 
        'Controladores\helpers->getEstablecimiento');
$index->route('POST @helpers_oficinas: /helpers/oficina [ajax]', 
        'Controladores\helpers->getOficinas');

// Esta es la forma en que la aplicación empieza
$index->run();
