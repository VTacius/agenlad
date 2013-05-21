<?php
	require_once('conect.class.php');
	$clasel = new phpLDAP();
	
	$index_con = $clasel->conecLDAP("192.168.1.11","389");
	print_r($index_con);	
	$index_bind = $clasel->enlaceLDAP($index_con,"cn=administrator,dc=xibalba,dc=com","pass2025") or die ($GLOBALS['errorLDAP'] );
		
	$index_list = $clasel->listarLDAP($index_con,"ou=people,dc=xibalba,dc=com","uid=*") or die ($GLOBALS['errorLDAP']);
	
	$index_cont = $clasel->getLDAP($index_con,$index_list) or die ($GLOBALS['errorLDAP']);
	
	$index_tabla =	$clasel->tabDatosLDAP($index_cont);
	echo "<br>";
	echo "<table border=1>";
	echo $index_tabla;
	echo "</table>";
?>
