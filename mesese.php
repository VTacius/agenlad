<?php
	require_once('conect.class.php');
	require_once('config.php');	
	header('Content-Type: text/html; charset=ISO-8859-1');
	$filtro = $_GET['num'];

	$valores = array ('cn','loginshell','sn','mail');
	//Usando los valores que nos da el formulario, trocandolos a filtros válidos para nuestro árbol LDAP
	
	$pfiltro = "uid=$filtro";
	
	$detalles = new phpLDAP();	
	
	//Hasta ahora, mis intentos que la conexión y el enlace sean variables de sesión no han dado resultados
	$detalles_con = $detalles->conecLDAP($host,$port);

	$detalles_bind = $detalles->enlaceLDAP($detalles_con,'alortiz','pass2025',$base) or die ($detalles->errorLDAP);

	$detalles_detalles = $detalles->listarLDAP($detalles_con,$base,$pfiltro) or die ($detalles->errorLDAP);
	
	$detalles_cont = $detalles->getLDAP($detalles_con,$detalles_detalles) or die ($detalles->errorLDAP);
	
	$detalles_tabla =	$detalles->tabDatosLDAP($detalles_cont,$valores);

  echo $detalles_tabla;
