<html>
<head>
<title>Ejemplo de manejo de Ajax usando jquery</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/JavaScript">
function consultar(valor)
{
    $("#resultado").html("Cargando...");
    $.get("meses.php",{num:valor},procesarEventos); 
}

function procesarEventos(datos)
{
  $("#resultado").html(datos);
}
</script>
</head>
<body>
    <input type="text" id="valor" name="valor"> <input type="button" value="Consultar" onclick="javascript: consultar(valor.value);">
<div id="resultado"></div>
</body>
</html>