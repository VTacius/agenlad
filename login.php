<?php
	//ini_set('display_errors', '1');
	//ini_set('error_reporting','-1');
	require_once('config.php');
	require_once('conect.class.php');
	$user = (isset($_POST['luser']))? $_POST['luser']:"false";
	$pass = (isset($_POST['lpasswd']))?$_POST['lpasswd']:'false';

	$login = new phpLDAP();	

	$login_con = $login->conecLDAP($host,$port);
		
	if ($login_bind = $login->enlaceLDAP($login_con,$user,$pass,$base)){
		session_start();
		$_SESSION["luser"] = $user;
		$_SESSION["lpasswd"] = $pass;
		header('Location: listado.php');
	}else{
		$_POST['mensaje'] = "Credenciales inválidas";
		header('Location: index.php');	
	} 
	
?>
