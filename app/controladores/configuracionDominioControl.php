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

//print "Administradores: " . serialize($administradores) . "\n";
//print "Técnicos: " . serialize($tecnicos) . "\n";
//print "Usuarios: " . serialize($usuarios) . "\n";

function configuracionDominio($dominio, $ip_server, $puerto, $dn_admin){
    $rdn = explode(".", $dominio);
    $dn = "";
    foreach ($rdn as $componente) {
        $dn .= "dc=$componente,";
    }
    $base = rtrim($dn, ",");

    $configuracion = array(
        'base' => $base,
        'puerto' => $puerto,
        'servidor' => $ip_server,
        'grupos_ou' => FALSE,
        'base_grupo' => 'ou=Groups,' . $base,
        'base_usuario' => 'ou=Users,' . $base,
        'dn_administrador' => $dn_admin
    );
    
    return serialize($configuracion);
}

print "Dominio Hacienda: " . configuracionDominio("hacienda.gob.sv", "192.168.2.10", "389", 'cn=admin,dc=hacienda,dc=gob,dc=sv') . "\n";
print "Dominio Donaciones: " . configuracionDominio("donaciones.gob.sv", "192.168.2.14", "389", 'cn=admin,dc=donaciones,dc=gob,dc=sv') . "\n";

// Necesario para resetear la contraseña de alguien con permisos administrativos
$cmds_reset_password = "update user set firmas=:password_admin_ldap, firmaz='Zimbra2025_Lector', bandera=1 where user=:user";
$args_reset_password = array('user' => 'alortiz', 'password_admin_ldap' => 'admin_ldap_hacienda');

// Necesario para convertir a un usuario cualquiera en administrador
$cmds_create_rol = "insert into user(user, rol, dominio, firmas, firmaz, bandera) values(:user, :rol, :dominio ,'admin_ldap_hacienda' ,'Zimbra2025_Lector', 1)";
$args_create_rol = array('user' => "czapata", 'rol'=>'admon', 'dominio'=>'donaciones.gob.sv');

// Antes de convertir a un usuario cualquiera en administrador, agregue su dominio de la siguiente forma
$cmds_create_dominio = "insert into user(user, rol, dominio, firmas, firmaz, bandera) values(:user, :rol, :dominio ,'admin_ldap_hacienda' ,'Zimbra2025_Lector', 1)";
$args_create_dominio = array('user' => "czapata", 'rol'=>'admon', 'dominio'=>'donaciones.gob.sv');

