$(document).ready(function(){
	$("#errorLdap").hide();
});

/**
Con la pena que no recuerdo a quién acreditar por esto
*/
function isEmpty(obj) {
    if (typeof obj === 'undefined' || obj === null || obj === '') return true;
    if (typeof obj === 'number' && isNaN(obj)) return true;
    if (obj instanceof Date && isNaN(Number(obj))) return true;
    return false;
}

function mostrarErrorLdap(data){
	if(!isEmpty(data.errorLdap)){
		var respuesta = "";
                $("#errorLdap").show();
		$(data.errorLdap).each(function(index, elemento){
			respuesta += elemento.titulo + ": " + elemento.mensaje + " ";
			$("#errorLdap").text(respuesta);
		});
		
	}
}
