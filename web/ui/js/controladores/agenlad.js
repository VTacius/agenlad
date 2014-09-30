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

function errorOnResponse(data){
    console.log("Un error ha ocurrido");
    console.log(data.responseText);
}

function mostrarErrorConexion(data){
    var errores = ['errorLdap', 'errorGrupo', 'errorZimbra', 'mensajes'];
    var respuesta = "";
    $(errores).each(function(index, elemento){
        if (!isEmpty(data[elemento])) {
            respuesta += mostrarErrorLabel(data[elemento]);
        }
    });
    if (!isEmpty(respuesta)) {
        console.log(respuesta);
        $("#errorLdap").show();
    }
    $("#errorLdap").html(respuesta);
    
}

function mostrarErrorLabel(data){
    var respuesta = "";
    $(data).each(function(index, elemento){
        respuesta += elemento.titulo + ": " + elemento.mensaje + "<br> ";
    });
    return respuesta;
}
