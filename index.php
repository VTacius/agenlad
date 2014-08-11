<?php
/**
 * Esta es la forma en que iniciamos el framework. 
 * Parece estar bajo el patrón Singleton, por lo que esta es la variable que ha de comunicar a todos
 */
$index = require('lib/base.php');

/**
 * Ponemos a nuestro disposición todo nuestro código
 */
$index->set('AUTOLOAD','app/; lib/');

/**
 * Nos encargamos de convocar el poder de Twig
 */
require_once __DIR__ . '/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
$twig_loader = new Twig_Loader_Filesystem(__DIR__ . '/ui/plantillas');

$index->set('twig', 
    $twig = new Twig_Environment($twig_loader, array(
        'cache' => __DIR__ . '/tmp/cache',
        'auto_reload' => true,
    ))
);

//$index->set('ONERROR',
//    function($f3) {
//        // custom error handler code goes here
//        // use this if you want to display errors in a
//        // format consistent with your site's theme        
//        while (ob_get_level()){
//          ob_end_clean();
//        }
//        $twig = $f3->get('twig');
//        $parametros = array(
//            'status' => $f3->get('ERROR.status'),
//            'title'=>$f3->get('ERROR.title'),
//            'text'=>$f3->get('ERROR.text'),
//                
//        );
//        if ($f3->get('DEBUG') == 3){
//            $parametros['trace'] = $f3->get('ERROR.trace');
//        }
//        echo $twig->render('error.html.twig', $parametros);
//    }
//);

/**
 * Esta función Twig permite usar archivos estáticos desde una ubicación fija
 */
$function = new Twig_SimpleFunction('activos', function ($activos) {
    return '/ui/' . $activos;
});
$twig->addFunction($function);

/**
 * Leemos el fichero de configuración y configuramos las variables para todo el proyecto
 */
$index->config(__DIR__ . '/configuracion.ini');

/**
 * Veamos que tal le va con una conexión a nivel de aplicación
 * Supongo que abrirá una por ¿Cada equipo conectado?
 * En realidad, la suponen de tal forma para manejar sesiones
 */
$dbbase = $index->get('dbbase');
$dbserver = $index->get('dbserver');
$dsn = "mysql:host=$dbserver;port=3306;dbname=$dbbase";
$dbusuario = $index->get('dbusuario');
$dbpassword = $index->get('dbpassword');
$index->set('dbconexion',new DB\SQL($dsn, $dbusuario, $dbpassword));

/**
 * Configuramos las rutas
 * ADVERTENCIA: Deben ser absolutas cuando se haga referencia a ellas dentro de HTML
 */

// Rutas hacia directorio
$index->route('GET|POST @directorio: /directorio', 
        'controladores\directorioControl->display');
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
$index->route('GET|POST @main: /index', 
        'controladores\indexControl->display');
$index->route('GET|POST @main: /', 
        'controladores\indexControl->display');
$index->route('GET|POST @cambiologin: /index/cambio',
        'controladores\indexControl->cambioCredenciales');
//Rutas hacia técnico
$index->route('GET|POST @tecnico: /mostrarpass', 
        'controladores\tecnicoControl->display');
$index->route('GET|POST @tecnicoDatos: /mostrarpass/datos', 
        'controladores\tecnicoControl->datos');
// Estas son algunas rutas de pruebas, que espero que no sean muchas
$index->route('GET|POST @prueba_user: /pruebas', 
        'controladores\pruebaControl->display');
$index->run();
