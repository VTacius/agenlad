<?php
	require_once('conect.php');
	$index_con = conecLDAP("192.168.1.11","389");
	
	$index_bind = enlaceLDAP($index_con,"cn=administrator,dc=xibalba,dc=com","pass2025") or die ($GLOBALS['errorLDAP'] );
	//print_r($index_bind);
		
	$index_list = listarLDAP($index_con,"ou=people,dc=xibalba,dc=com","uid=*") or die ($GLOBALS['errorLDAP']);
	
	$index_cont = getLDAP($index_con,$index_list) or die ($GLOBALS['errorLDAP']);
	
	$index_tabla =	tabDatosLDAP($index_cont);
	echo "<br>";
	echo "<table border=1>";
	echo $index_tabla;
	echo "</table>";

	$entry = ldap_first_entry($index_con, $index_list);

	$attrs = ldap_get_attributes($index_con, $entry);
?>
