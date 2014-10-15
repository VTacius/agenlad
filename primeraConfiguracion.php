<?php

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

$dc = explode(".",$dominio);
$clave = $dc[0];

print "insert into configuracion values('$clave','$dominio', '$descripcion','$attr');\n";
