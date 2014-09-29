<?php
ini_set('default_charset','UTF-8');
require_once('cifrado.php');
$firmas = "ldap_password_admin";
$password = "passito";
$hashito = new \clases\cifrado();
$hash = $hashito->encrypt($firmas, $password);
print "$hash\n";
$claro = $hashito->descifrada ( $hash, $password );
print "$claro\n";
