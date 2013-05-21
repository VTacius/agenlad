<?php
	//ini_set('display_errors', '1');
	//ini_set('error_reporting','-1');
	require_once('conect.class.php');
	session_start();
	if (!(isset($_SESSION['luser']) && isset($_SESSION['lpasswd']))){
		header('Location: index.php');
	}

	$user = (isset($_SESSION['luser'])) ? $_SESSION['luser']:"false";
	$pass = (isset($_SESSION['lpasswd'])) ? $_SESSION['lpasswd']:'false';

	$list = new phpLDAP();	
	//$list_con = $list->conecLDAP("192.168.1.11","389");
	$list_con = $_SESSION["conect"];

	$list_bind = $list->enlaceLDAP($list_con,$user,$pass) or die ($list->errorLDAP);
	//$list_bind = $_SESSION["sesion"];

	$list_list = $list->listarLDAP($list_con,"ou=people,dc=xibalba,dc=com","uid=*") or die ($list->errorLDAP);
	
	$list_cont = $list->getLDAP($list_con,$list_list) or die ($GLOBALS['errorLDAP']);
	
	$list_tabla =	$list->tabDatosLDAP($list_cont);
	echo "<br>";
	echo "<table border=1>";
	echo $list_tabla;
	echo "</table>";
?>
