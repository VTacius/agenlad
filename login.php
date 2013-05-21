<?php
	//ini_set('display_errors', '1');
	//ini_set('error_reporting','-1');
	require_once('conect.class.php');
	require_once('config.php');
	$attr = new valores();
	$user = (isset($_POST['luser']))? $_POST['luser']:"false";
	$pass = (isset($_POST['lpasswd']))?$_POST['lpasswd']:'false';

	$login = new phpLDAP();	
	$login_con = $login->conecLDAP($attr->host,$attr->port);
		
	if ($login_bind = $login->enlaceLDAP($login_con,$user,$pass)){
		session_start();
		$_SESSION["luser"] = $user;
		$_SESSION["lpasswd"] = $pass;
		header('Location: modificar.php');
	}else{
		$_POST['mensaje'] = "Credenciales inválidas";
		header('Location: index.php');	
	} 
	
?>
