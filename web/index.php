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
 * Veamos que tal le va con una conexión a nivel de aplicación
 * Supongo que abrirá una por ¿Cada equipo conectado?
 * En realidad, la suponen de tal forma para manejar sesiones
 */
$dbbase = $index->get('dbbase');
$dbserver = $index->get('dbserver');
$dbusuario = $index->get('dbusuario');
$dbpassword = $index->get('dbpassword');
$dsn = "mysql:host=$dbserver;port=3306;dbname=$dbbase";
// Que pena que esto no tuviera un try como debiera ser, siendo que ejecuta el constructor en este lugar sin más
try{
    $index->set('dbconexion',new \DB\SQL($dsn, $dbusuario, $dbpassword, array( \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION , \PDO::ATTR_TIMEOUT => 5)));
}catch (\PDOException $e){
    $e->getMessage();
}

/**
 * Configuramos las rutas
 * ADVERTENCIA: Deben ser absolutas cuando se haga referencia a ellas dentro de HTML
 */

// Rutas hacia directorio
$index->route('GET|POST @directorio: /directorio [sync]', 
        'controladores\directorioControl->display');
$index->route('GET|POST @directorio_busqueda: /directorio/busqueda', 
        'controladores\directorioControl->mostrarUsuario');
//Rutas hacia login
$index->route('GET|POST @login: /login',
        'controladores\loginControl->display');
$index->route('GET|POST @login_inicio: /login/inicio', 
        'controladores\loginControl->autenticar');
$index->route('GET|POST @login_final: /login/final', 
        'controladores\loginControl->cerrar');
$index->route('GET|POST @login_finalMensaje: /login/cambio/@mensaje', 
        'controladores\loginControl->cerrarMensaje');
$index->route('GET|POST @login_mensaje: /login/@mensaje',
        'controladores\loginControl->display');
//Rutas hacia main
$index->route('GET|POST @main: /', 
        'controladores\mainControl->display');
$index->route('GET|POST @main_index: /main', 
        'controladores\mainControl->display');
$index->route('POST @cambiologin: /main/cambio [ajax]',
        'controladores\mainControl->cambioCredenciales');
//Rutas hacia usershow
$index->route('GET|POST @tecnico: /usershow', 
        'controladores\usuario\usershowControl->display');
$index->route('GET|POST @tecnicoDatos: /usershow/datos [ajax]', 
        'controladores\usuario\usershowControl->datos');
//Rutas hacia usermod
$index->route('GET|POST @usermod: /usermod', 
        'controladores\usuario\usermodControl->display');
$index->route('GET|POST @usermod_modificar: /usermod/envio', 
        'controladores\usuario\usermodControl->mostrarUsuarioPost');
$index->route('GET|POST @usermod_modificar: /usermod/@usuarioModificar', 
        'controladores\usuario\usermodControl->mostrarUsuarioGet');
$index->route('POST @usermod_modificar: /usermod/cambio [ajax]', 
        'controladores\usuario\usermodControl->modificarUsuario');
$index->route('POST @zimbra_modificar: /usermod/zimbra [ajax]', 
        'controladores\usuario\usermodControl->modificarBuzon');
//Rutas hacia useradd
$index->route('GET|POST @useradd: /useradd', 
        'controladores\usuario\useraddControl->display');
$index->route('GET|POST @useradd_creacion: /useradd/creacion', 
        'controladores\usuario\useraddControl->creacionUsuario');
$index->route('GET|POST @useradd_creacion_test: /useradd/test', 
        'controladores\usuario\useraddPrueba->display');
$index->route('GET|POST @useradd_check_uid: /useradd/checkuid', 
        'controladores\usuario\useraddControl->checkUid');
// Estas son algunas rutas de pruebas, que espero que no sean muchas
$index->route('GET|POST @prueba_user: /pruebas', 
        'controladores\pruebaControl->display');
$index->route('GET|POST @prueba_paginacion: /pruebas/paginacion', 
        'Pruebas\paginacion->display');
$index->route('GET|POST @prueba_useradd: /pruebas/useradd', 
        'Pruebas\useraddPrueba->display');
$index->route('GET|POST @prueba_useradd: /pruebas/busqueda/@term', 
        'controladores\pruebaControl->busqueda');
$index->route('GET|POST @prueba_getdatos: /pruebas/getdatos', 
        'Pruebas\getdatos->display');
$index->route('GET|POST @prueba_userupdate: /pruebas/userupdate', 
        'controladores\usuario\userActualizacion->pruebas');
// Rutas para configuracion de dominios
$index->route('GET|POST @conf_dominios: /confdominios', 
        'controladores\configuracion\dominioControl->display');
$index->route('GET|POST @conf_dominio_modificar: /confdominios/modificar', 
        'controladores\configuracion\dominioControl->modificarDominios');
$index->route('GET|POST @conf_dominio_modificar: /confdominios/nuevo', 
        'controladores\configuracion\dominioControl->mostrarNuevoDominio');
$index->route('GET|POST @conf_dominio_modificar: /confdominios/nuevo/crear', 
        'controladores\configuracion\dominioControl->crearDominio');
$index->route('GET|POST @conf_dominio_set_password_samba: /confdominios/password/samba', 
        'controladores\configuracion\dominioControl->setPasswordSamba');
$index->route('GET|POST @conf_dominio_set_password_zimbra: /confdominios/password/zimbra', 
        'controladores\configuracion\dominioControl->setPasswordZimbra');
$index->route('GET|POST @conf_dominio_detalles: /confdominios/@clave', 
        'controladores\configuracion\dominioControl->mostrarDetalles');
// Rutas para configuracion de usuarios
$index->route('GET|POST @conf_usuario: /confpermisos', 
        'controladores\configuracion\usuarioControl->display');
$index->route('GET|POST @conf_usuario_busqueda: /confpermisos/busqueda/@term', 
        'controladores\configuracion\usuarioControl->busqueda');
$index->route('GET|POST @conf_usuario_busqueda_rol: /confpermisos/rol/', 
        'controladores\configuracion\usuarioControl->datosRolUsuario');
$index->route('GET|POST @conf_usuario_configuracion_rol: /confpermisos/configurarol', 
        'controladores\configuracion\usuarioControl->configuracionRolUsuario');
// Rutas para Inicializacion de la aplicacion
$index->route('GET|POST @conf_usuario_busqueda_rol: /inicializacion', 
        'controladores\configuracion\inicializacion->display');
$index->route('GET|POST @conf_usuario_busqueda_rol: /inicializacion/usuario', 
        'controladores\configuracion\inicializacion->usuario');
// Agregado el 26/06/15 para que los usuarios puedan actualizar por si mismos sus datos
$index->route('GET|POST @usuario_actualizacion: /actualizacion', 
        'controladores\usuario\userActualizacion->display');
$index->route('GET|POST @usuario_actualizacion_cambio: /actualizacion/cambio', 
        'controladores\usuario\userActualizacion->actualizacionCambio');
    
$index->route('GET|POST @usuario_actualizacion_cambio: /actualizacion/usuario', 
        'controladores\usuario\userActualizacion->getUsuario');

// Esta es la forma en que la aplicación empieza
$index->run();
