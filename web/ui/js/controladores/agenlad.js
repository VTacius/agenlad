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
		$(data.errorLdap).each(function(index, elemento){
			$("#errorLdap").show();
			respuesta += elemento.titulo + ": " + elemento.mensaje + " ";
			$("#errorLdap").text(respuesta);
		});
		
	}
}
