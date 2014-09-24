<?php
$dn = "uid=alortiz,ou=Users,dc=salud,dc=gob,dc=sv";
$pattern = "(dc=(?P<componentes>[A-Za-z]+))";
$matches = array();
$dominio = "";
preg_match_all($pattern, $dn, $matches );
foreach ($matches['componentes'] as $componentes){
	$dominio .= $componentes . ".";
}
print rtrim($dominio, ".");

$cadena = "";

if (empty($cadena)){
	print "Esta vacìo";
}
