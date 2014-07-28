<?php
require_once ('./clases/utilidades.php');
$server = configuracion("server");
$puerto = configuracion("puerto");
$user = configuracion("user", "usuario");
$pass = configuracion("pass", "usuario");
$dominio = configuracion("dominio");

$login = new controlLDAP();
$login->conexion($server, $puerto);
$login->crearDN($user,$dominio);
$base = $login->crearBase("salud.gob.sv");
if ($login->enlace($pass)){
    $atributos = ['cn', 'mail', 'title'];
    $filtro = "uid=*";
	
    $login->datos($base, $filtro, $atributos, 100);
    $contenido = $login->arrayDatosLDAP($atributos);
    
    $menu = array(
        "index"=> "Inicio",
        "usuarios"=> "Usuarios",
        "equipos"=> "Computadoras"
    );

    $parametros = array(
        'empleados' => $contenido,
        'menu' => $menu
    );

    $template->display($parametros);
}else{
    $template = $twig->loadTemplate('index.html.twig');
    $parametros = array(
        'errorLDAP' => $login->mostrarERROR()
    );
    $template->display($parametros);
}


