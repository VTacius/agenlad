<?php
	ini_set('default_charset', 'utf-8');
	$mensaje = (isset($_POST['mensaje']))?$_POST['mensaje']:"";
?>
<form name = "index" action = "login.php" method = "POST">
			<input type = text name = "luser" id = "luser"/>
			<input type = text name = "lpasswd" id = "lpasswd"/>
		<input type = submit value = "Enviar">
		<h2><?php print $mensaje ?></h2>
</form>
