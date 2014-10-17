<?php
// Rellene la siguiente configuracion para el dominio
$dominio = "salud.gob.sv";
$descripcion = "Oficinas Centrales del Ministerio de Salud"; // Opcional
$server = "10.10.20.49";
$puerto = "389";
$sambaSID = "S-1-5-21-371878337-141820978-2368272707";
$grupos_ou = 0; // Podrá cambiarlo después con más detenimiento
$netbiosName = "DIRECTORIO";
$mail_domain = "salud.gob.sv";
$admin_zimbra = "admin@salud.gob.sv";
$dn_admin_ldap = 'cn=admin,dc=salud,dc=gob,dc=sv';

// Rellene los siguientes datos para el usuarios admin_global
$user =  "alortiz";
$rol = "admin_general"; // Solo tiene sentido escoger entre admon, tecnico y admin_general
$dominio = "salud.gob.sv";
$firmas = "W2s@lud_PDC";
$firmaz = "srv2025";

// Esta funcion es un prospecto de como a un usuario se le cambia el rol a uno administrativo
// Es decir, esta funcion aplica cuando no había una entrada en rol para este dicho usuario
function configuracionRolUsuario($user, $rol, $dominio, $firmas, $firmaz){
    $cmds = "insert into user(user, rol, dominio, firmas, bandera_firmas, firmaz, bandera_firmaz) values ('$user', '$rol', '$dominio', '$firmas', 1, '$firmaz', 1)";
    return $cmds;
}

// Esta funcion se encarga de crear la configuracion
function configuracionDominio($dominio, $ip_server, $puerto, $dn_admin_ldap, $admin_zimbra, $grupos_ou, $sambaSID, $mail_domain, $netbiosName){
        $rdn = explode(".", $dominio);
        $dn = "";
        foreach ($rdn as $componente) {
            $dn .= "dc=$componente,";
        }
        $base = rtrim($dn, ",");

        $configuracion = array(
            'base' => $base,
            'puerto' => $puerto,
            'sambaSID' => $sambaSID,
            'servidor' => $ip_server,
            'grupos_ou' => (bool)$grupos_ou,
            'base_grupo' => 'ou=Groups,' . $base,
            'base_usuario' => 'ou=Users,' . $base,
            'netbiosName' => $netbiosName,
            'mail_domain' => $mail_domain,
            'admin_zimbra' => $admin_zimbra,
            'dn_administrador' => $dn_admin_ldap
        );

        return serialize($configuracion);
    }

// Creamos la configuracion segun lo especificado arriba
$attr = configuracionDominio(
    $dominio,
    $server,
    $puerto,
    $dn_admin_ldap,
    $admin_zimbra,
    $grupos_ou,
    $sambaSID,
    $mail_domain,
    $netbiosName
);

// Se recomienda que clave sea el primer componente de dominio
$dc = explode(".",$dominio);
$clave = $dc[0];

$administrador = configuracionRolUsuario($user, $rol, $dominio, $firmas, $firmaz); 
// Este es el resultado que agregamos a la base de datos
print "\nEjecutar manualmente esta setencia SQL para configurar Dominio\n";
print "insert into configuracion values('$clave','$dominio', '$descripcion','$attr');\n\n";

print "Ejecutar sentencia SQL manualmente para configurar usuario admin_general\n";
print  "$administrador\n\n";
