<?php
	//ini_set('display_errors', '1');
	//ini_set('error_reporting','-1');
	require_once('conect.class.php');
	require_once('config.php');	
	session_start();
	if (!(isset($_SESSION['luser']) && isset($_SESSION['lpasswd']))){
		header('Location: index.php');
	}

	$user = (isset($_SESSION['luser'])) ? $_SESSION['luser']:"false";
	$pass = (isset($_SESSION['lpasswd'])) ? $_SESSION['lpasswd']:'false';
	$valores = array ('cn','loginshell','sn','mail');

	$list = new phpLDAP();	
	//Hasta ahora, mis intentos que la conexión y el enlace sean variables de sesión no han dado resultados
	$list_con = $list->conecLDAP($host,$port);

	$list_bind = $list->enlaceLDAP($list_con,$user,$pass,$base) or die ($list->errorLDAP);

	$list_list = $list->listarLDAP($list_con,$base,"uid=*") or die ($list->errorLDAP);
	
	$list_cont = $list->getLDAP($list_con,$list_list) or die ($GLOBALS['errorLDAP']);
	
	$list_tabla =	$list->tabDatosLDAP($list_cont,$valores);
	echo "<br>";
	echo "<table border=1>";
	echo $list_tabla;
	echo "</table>";
?>
