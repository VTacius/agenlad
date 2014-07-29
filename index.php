<?php
require_once 'clases/utilidades.php';
require_once 'clases/sesion.php';
$template = $twig->loadTemplate('index.html.twig');
$parametros = array(
    'datos'=> $sesion['datos'][0]
);
$template->display($parametros);
