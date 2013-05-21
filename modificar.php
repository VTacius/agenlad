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
	
	$modificar = new phpLDAP();
	
	$modificar_con = $modificar->conecLDAP($host,$port);
	
	$modificar_bind = $modificar->enlaceLDAP($modificar_con,$user,$pass,$base) or die ($modificar->errorLDAP );
		
	$modificar_list = $modificar->listarLDAP($modificar_con,$base,"uid=$user") or die ($modificar->errorLDAP);
	
	$modificar_cont = $modificar->getLDAP($modificar_con,$modificar_list) or die ($GLOBALS['errorLDAP']);
	
	$modificar_tabla =	$modificar->arrayDatosLDAP($modificar_cont,$valores);

?>

<form name = "index" action = "login.php" method = "POST">
			<input type = text name = "prima" id = "prima" value="<?php print $modificar_tabla[0]?>"/>
			<input type = text name = "prima" id = "prima" value="<?php print $modificar_tabla[1]?>"/>
			<input type = text name = "seconda" id = "seconda" value="<?php print $modificar_tabla[2]?>"/>
			<input type = text name = "seconda" id = "seconda" value="<?php print $modificar_tabla[3]?>"/><a href="#" onclick='javascript:window.location.replace("http://filo.xibalba.com/modificar.php?elem=27")'>Cambiar</a>
		<input type = submit value = "Enviar">
		<h2><?php print $mensaje ?></h2>
</form>
