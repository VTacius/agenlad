$(document).ready(function(){
	$("#error").hide();
	$("#mensaje").hide();
});

/**
 * 
 * @param {var} obj
 * @returns {Boolean}
 */
function isEmpty(obj) {
    if (typeof obj === 'undefined' || obj === null || obj === '') return true;
    if (typeof obj === 'number' && isNaN(obj)) return true;
    if (typeof obj === 'object' && obj.length === 0) return true;
    if (obj instanceof Date && isNaN(Number(obj))) return true;
    return false;
}

function errorOnResponse(data){
    $("#advertencia").text("La aplicación ha fallado. Consulte con su técnico asociado");
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

function crearAlerta(codigo, contenido){
    var clase = "alert-" + codigo;
    alerta = '<div class="alert ' + clase +  ' alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span>        <span class="sr-only">Cerrar</span></button><strong>' + contenido + '</strong> </div>';
    return alerta;
}

function crearAlertaError(contenido){
    alerta = '<div class="alert alert-danger alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>'+ contenido + '</div>';
    return alerta;
}

function mostrarElementoError(error){
    var objetivo = "";
    $(error).each(function(i,j ){
        objetivo += '<strong>' + j.titulo + ":</strong> " + j.mensaje + "<br>";        
    });
    return crearAlertaError(objetivo);
}

function pmostrarError(data){
    console.log(data.error);
    var respuesta = "";
    if(!isEmpty(data.error)){
        $(data.error).each(function(index, error){
            respuesta += mostrarElementoError(error);
        });
        $("#error").show();
        $("#error").html(respuesta);
    }
};

function pmostrarMensaje(data){
    var respuesta = "";
    if(!isEmpty(data.mensaje)){
        $(data.mensaje).each(function(index, mensaje){
            if($.isNumeric(index)){
                respuesta += crearAlerta(mensaje.codigo, mensaje.mensaje);
            }else{
                respuesta += index + ": " + mensaje + "<br>";
            }
        });
        $("#mensaje").show();
        $("#mensaje").html(respuesta);
    }
};
