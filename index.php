<!DOCTYPE html>
<?php
	//Empieza el breve manejo de sesión
	session_start(); 
	if ((isset($_SESSION['luser']) && isset($_SESSION['lpasswd']))){
		header('Location: listado.php');
	}
	//Termina el breve manejo de sesión
?>
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <title>Entrar</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Directorio LDAP del Ministerio de Salud">
        <meta name="author" content="Alexander Ortìz">
        <link href="css/bootstrap.css" rel="stylesheet">
        <style type="text/css">
        body {
            padding-top: 45px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }
        .form-signin {
            max-width: 470px;
            padding: 19px 29px 29px;
            margin: 0 auto 20px;
            background-color: #fff;
            border: 1px solid #e5e5e5;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
                border-radius: 5px;
            -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
            -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
        }
        .form-signin .form-signin-heading,
        .form-signin .checkbox {
            margin-bottom: 10px;
        }
        .form-signin input[type="text"],
        .form-signin input[type="password"] {
            font-size: 16px;
            height: auto;
            margin-bottom: 15px;
            padding: 7px 9px;
        }
        </style>
    </head>
    <body>
    <div class="container">
        <form class="form-signin" action="login.php" method="POST" enctype="">
        <h2 class="form-signin-heading">Directorio Teléfonico MINSAL</h2>
        <h3 class="form-signin-heading">Entrar</h3>
        <input type="text" class="input-block-level" placeholder="Email address" name="luser">
        <input type="password" class="input-block-level" placeholder="Password" name="lpasswd">
        <!--<label class="checkbox">
          <input type="checkbox" value="remember-me"> Remember me
        </label>-->
        <button class="btn btn-large btn-primary" type="submit">Entrar</button>
      </form>

    </div> <!-- /container -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
</body>
</html>
