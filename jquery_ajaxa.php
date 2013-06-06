<html>
<head>
<title>Ejemplo de manejo de Ajax usando jquery</title>
	<script src="js/jquery.js"></script>
	<script type="text/JavaScript">
	function consultar(valor){
		$("#resultado").html("Cargando...");
		$.get("detalles.php",{filtro:valor},procesarEventos); 
	}
	
	function procesarEventos(datos){
		$("#resultado").html(datos);
	}
</script>
</head>
<body>
		<a href="#" id="detalle" name="detalle" onclick="javascript: consultar('gbena');">Detalles</a>
<div id="resultado"></div>
</body>
</html>
