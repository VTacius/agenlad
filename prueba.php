<?php 
	//Aderezamos con los archivos necesarios
	require_once('conect.class.php');
	require_once('config.php');	
	
	//Empieza el breve manejo de sesión
	session_start(); 
	if (!(isset($_SESSION['luser']) && isset($_SESSION['lpasswd']))){
		header('Location: index.php');
	}
	//Termina el breve manejo de sesión
	$user = (isset($_SESSION['luser'])) ? $_SESSION['luser']:"false";
	$pass = (isset($_SESSION['lpasswd'])) ? $_SESSION['lpasswd']:'false';
	$valores = array ('cn','objectclass','sn','mail');
	
	$modificar = new phpLDAP();
	
	$modificar_con = $modificar->conecLDAP($host,$port);
	
	$modificar_bind = $modificar->enlaceLDAP($modificar_con,$user,$pass,$base) or die ($modificar->errorLDAP );
		
	$modificar_list = $modificar->listarLDAP($modificar_con,$base,"uid=*") or die ($modificar->errorLDAP);
	
	$modificar_cont = $modificar->getLDAP($modificar_con,$modificar_list) or die ($GLOBALS['errorLDAP']);
	
	$modificar_tabla =	$modificar->arrayLargoDatosLDAP($modificar_cont,$valores);
	
	
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <title>Directorio Telefónico MINSAL</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Directorio Telefónico MINSAL">
        <meta name="author" content="Alexander Ortíz">
        <style type="text/css">
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
            .sidebar-nav {
                padding: 9px 0;
            }

            @media (max-width: 980px) {
            /* Enable use of floated navbar text */
            .navbar-text.pull-right {
                float: none;
                padding-left: 5px;
                padding-right: 5px;
            }
            }
    </style>
        <link href="css/bootstrap.css" rel="stylesheet">
    </head>
  <body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar">Esto es ?</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
            <a class="brand" href="index.php">Directorio Telefónico MINSAL</a>
          <div class="nav-collapse collapse">
            <p class="navbar-text pull-right">
              Usuario: <a href="modificar.php" class="navbar-link"><?php echo $user?></a>
            </p>
            <ul class="nav">
              <li class="active"><a href="ayuda.php">Ayuda</a></li>
              <li><a href="sobre.php">Sobre</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span2">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">Enlaces de interés</li>
              <li class="active"><a href="https://mail.salud.gob.sv">Correo</a></li>
            </ul>
          </div><!--/.well -->
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">Administracion</li>
              <li class="active"><a href="modificar.php">Cambiar datos</a></li>
              <li><a href="logout.php">Salir</a></li>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span10">
          <div class="brand">
            <h2 class=" small text-center">Directorio del Ministerio de Salud</h2>
          </div>
          <div class="row-fluid">
            <div class="span12">
              <h3></h3>
								<form name = "index" action = "prueba.php" class="form-horizontal " method = "POST">
								<fieldset>
								<legend>Datos del Usuario <?php echo $user?></legend>
									<div class="control-group">
										<label class="control-label" for="v0">Nombre</label>
								    <div class="controls">
											<input class="span7" type = text name="v0" value="<?php print $modificar_tabla[0]?>"/>
							    		<a href="#" onclick='javascript:window.location.replace("http://filo.xibalba.com/modificar.php?elem=0")'>Cambiar</a>
							    	</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="v1">E-Mail</label>
								    <div class="controls">
											<input class="span7" type = text name="v1" value="<?php print $modificar_tabla[1]?>"/>
							    		<a href="#" onclick='javascript:window.location.replace("http://filo.xibalba.com/modificar.php?elem=1")'>Cambiar</a>
							    	</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="v2">Nombre</label>
								    <div class="controls">
											<input class="span7" type = text name="v2" value="<?php print $modificar_tabla[2]?>"/>
							    		<a href="#" onclick='javascript:window.location.replace("http://filo.xibalba.com/modificar.php?elem=2")'>Cambiar</a>
							    	</div>
									</div>
									<div class="control-group">
										<label class="control-label" for="v3">Nombre</label>
								    <div class="controls">
											<input class="span7" type = text name="v3" value="<?php print $modificar_tabla[3]?>"/>
							    		<a href="#" onclick='javascript:window.location.replace("http://filo.xibalba.com/modificar.php?elem=3")'>Cambiar</a>
										</div>
									</div>
								</fieldset>
								</form>
								<h2><?php print $mensaje ?></h2>
						</div><!--/span-->
          </div><!--/row-->
        </div><!--/span-->
      </div><!--/row-->

      <hr>

      <footer>
        <p>© Ministerio de Salud 2013</p>
      </footer>

    </div><!--/.fluid-container-->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/bootstrap.js" type="text/javascript"></script>
    </body>
</html>
