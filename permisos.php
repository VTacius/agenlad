<?php
$administradores = array(
    "directorio" => "Directorio Telefónico",
    "main"       => "Cambio de Contraseña",
    "usershow"   => "Ver Usuario",
    "usermod"    => "Modificar Usuario",
    "useradd"    => "Agregar Usuario"
);

$tecnicos = array(
    "directorio" => "Directorio Telefónico",
    "main"       => "Cambio de Contraseña",
    "usershow"   => "Ver Usuario"
);

$usuarios = array(
    "directorio" => "Directorio Telefónico",
    "main"       => "Cambio de Contraseña"
);

print "Administradores: " . serialize($administradores) . "\n";
print "Técnicos: " . serialize($tecnicos) . "\n";
print "Usuarios: " . serialize($usuarios) . "\n";
