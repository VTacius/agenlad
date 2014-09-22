<?php
namespace controladores;

class configuracionDominioControl {
    
}

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

$dominio = "donaciones.gob.sv";

$rdn = explode(".", $dominio);
$dn = "";
foreach ($rdn as $componente) {
    $dn .= "dc=$componente,";
}
$dn = rtrim($dn, ",");


$configuracion = array(
    'base' => $dn,
    'base_usuario' => 'ou=Users,' . $dn,
    'base_grupo' => 'ou=Groups,' . $dn,
    'grupos_ou' => FALSE
);

$config = serialize($configuracion);

print "\n'$config'\n\n";

// Necesario para resetear la contraseña de alguien con permisos administrativos
$cmds_reset_password = "update user set firmas='admin_ldap_hacienda', firmaz='Zimbra2025_Lector', bandera=1 where user=:user";
$args_reset_password = array('user' => 'alortiz');

// Necesario para convertir a un usuario cualquiera en administrador
$cmds_create_rol = "insert into user(user, rol, dominio, firmas, firmaz, bandera) values(:user, :rol, :dominio ,'admin_ldap_hacienda' ,'Zimbra2025_Lector', 1)";
$args_create_rol = array('user' => "czapata", 'rol'=>'admon', 'dominio'=>'donaciones.gob.sv');

// Antes de convertir a un usuario cualquiera en administrador, agregue su dominio de la siguiente forma
$cmds_create_dominio = "insert into user(user, rol, dominio, firmas, firmaz, bandera) values(:user, :rol, :dominio ,'admin_ldap_hacienda' ,'Zimbra2025_Lector', 1)";
$args_create_dominio = array('user' => "czapata", 'rol'=>'admon', 'dominio'=>'donaciones.gob.sv');

