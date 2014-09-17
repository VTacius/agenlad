$(document).ready(function(){
    $("#respuesta").hide();
});

$("#enviar").click(function(e){
    e.stopPropagation(); 
    e.preventDefault();
    datos();
});


function isEmpty(obj) {
    if (typeof obj === 'undefined' || obj === null || obj === '') return true;
    if (typeof obj === 'number' && isNaN(obj)) return true;
    if (obj instanceof Date && isNaN(Number(obj))) return true;
    return false;
}
        
/**
 * Llena la pantalla del usuario con los datos obtenidos del servidor
 * @param {json} data
 * @returns {undefined}
 */
var mostrarDatos = function(data){
    // Llenamos los datos
    if (!(data.nameuser==="{empty}" && data.buzonstatus==="{empty}")){
        $("#cuentastatus").text(data.cuentastatus);
        $("#buzonstatus").text(data.buzonstatus);
        $("#localidad").text(data.grupouser);
        $("#psswduser").text(data.psswduser);
        $("#grupouser").text(data.grupouser);
        $("#nameuser").text(data.nameuser);
        $("#oficina").text(data.oficina);
        // Una vez todo configurado, mostramos
        $("#respuesta").show();
    }else{
        $("#respuesta").hide();
    }
};

var datos = function(){
    $.ajax({
        type: 'POST',
        url: '/mostrarpass/datos',
        dataType: 'json',
        data: {
            usuarioCliente: $("#usuarioCliente").val()
        },
        success: mostrarDatos
    });
};