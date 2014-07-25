<?php
	require_once('./conect.class.php');
    $index = phpLDAP();
	$index_con = conecLDAP("192.168.1.11","389");
	
	$index_bind = enlaceLDAP($index->conn,"cn=administrator","pass2025","dc=xibalba,dc=com" );
	$index_list = $index->listarLDAP($index_con,"ou=people,dc=xibalba,dc=com","uid=*");
    $index_cont = $index->getLDAP($index_con, $index_list);
    
  
	$valores = ['dn'];
	$index_tabla =	$index->tabDatosLDAP($index_cont);
	echo "<br>";
	echo "<table border=1>";
	echo $index_tabla;
	echo "</table>";
?>
