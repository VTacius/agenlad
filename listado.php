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
	$user = (isset($_SESSION['luser'])) ? $_SESSION['luser'] : false;
	$pass = (isset($_SESSION['lpasswd'])) ? $_SESSION['lpasswd'] : false;
	$busqueda = (isset($_POST['busqueda'])) ? $_POST['busqueda'] : false;
	$filtro = (isset($_POST['filtro'])) ? $_POST['filtro'] : null;

	//Especifique los atributos que cuyos valores quiere obtener por cada entrada
	//Se podrá disponer de ellos en ese orden
	$valores = array ('cn','loginshell','sn','mail');

	//Usando los valores que nos da el formulario, trocandolos a filtros válidos para nuestro árbol LDAP
	if ($busqueda == "departamento"){
		$v = "departmentNumber";
	}elseif($busqueda =="nombre"){
		$v = "cn";
	}
	//Creando el filtro. Bonito quedo este ternario
	$pfiltro = ($filtro!=null) ? $v . "=*" . $filtro . "*"  : 'uid=*';
	
	$list = new phpLDAP();	
	
	//Hasta ahora, mis intentos que la conexión y el enlace sean variables de sesión no han dado resultados
	$list_con = $list->conecLDAP($host,$port);

	$list_bind = $list->enlaceLDAP($list_con,$user,$pass,$base) or die ($list->errorLDAP);

	$list_list = $list->listarLDAP($list_con,$base,$pfiltro) or die ($list->errorLDAP);
	
	$list_cont = $list->getLDAP($list_con,$list_list) or die ($list->errorLDAP);
	
	$list_tabla =	$list->tabDatosLDAP($list_cont,$valores);
?>
<!DOCTYPE html>
<html lang="es">
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
            
              <form class="form-search text-center" action="listado.php" method="POST">
              <input class="span6" id="filtro" type="text" name="filtro">
              <select name="busqueda">
                    <option value="nombre">Nombre</option>
                    <option value="departamento">Departamento</option>
              </select>
              <button class="btn btn-medium" type="submit">Búsqueda</button> 
            </form>
          </div>
          <div class="row-fluid">
            <div class="span12">
              <h2>Usuarios</h2>
              <table class="table">
                  <thead>
                      <tr>
                          <th>Nombre</th>
                          <th>Dependencia</th>
                          <th>Correo</th>
                          <th>Teléfono</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php echo $list_tabla?>
                  </tbody>
              </table>
							<div id="resultado"></div>
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
		<script type="text/JavaScript">
			function consultar(valor){
				$("#resultado").html("Cargando...");
				$.get("detalles.php",{filtro:valor},procesarEventos); 
			}

			function procesarEventos(datos){
				$("#resultado").html(datos);
			}
		</script>

    </body>
</html>
