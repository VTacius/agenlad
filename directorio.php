<?php
require_once ('./herramientas/utilidades.php');
require_once ('./herramientas/sesion.php');
$server = configuracion("server");
$puerto = configuracion("puerto");
$dominio = configuracion("dominio");
$user = $sesion['user'];
$pass = $sesion['pass'];

$login = new controlLDAP();
$login->conexion($server, $puerto);
$login->crearDN($user,$dominio);
$base = $login->crearBase($dominio);
// En todo caso, siempre habrás de usar la plantilla
$template = $twig->loadTemplate('directorio.html.twig');
// Estoy discutiendo si debo seguir haciendo esta comprobación
// Se supone que no puedo entrar a esta página si no tengo 
// las variables dentro de sesión, por tanto es redundante porque sabemos que si 
// lo va a procesar esas variables harán un bind exitoso
if ($login->enlace($pass)){
    $atributos = ['cn', 'mail', 'title'];
    $filtro = "uid=*";
	
    $login->datos($base, $filtro, $atributos, 100);
    $contenido = $login->arrayDatosLDAP($atributos);
    
    $parametros = array(
        'empleados' => $contenido,
        // Traemos $menu desde clase/sesion.php
        'menu' => $menu,
        'pagina'=> $sesion['pagina']
    );

    $template->display($parametros);
}else{
    $parametros = array(
        'errorLDAP' => $login->mostrarERROR()
    );
    $template->display($parametros);
}


