<?php
require_once 'herramientas/utilidades.php';
require_once 'herramientas/sesion.php';
$template = $twig->loadTemplate('index.html.twig');
$parametros = array(
    'menu' => $menu,
    'datos'=> $sesion['datos'][0],
    'pagina'=> $sesion['pagina']
);
$template->display($parametros);
