$(document).ready(function(){
    toggleUseradd(false);
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
 * Cambia #respuesta si que existe, es decir, si fue creada desde Twig 
 * en respuesta al rol del usuario
 * @param {boolean} cambio
 * @returns {undefined}
 */
function toggleUseradd(cambio){
    if ( $("#useraddTecnico").length > 0 ){
        if (cambio){
            $("#useraddTecnico").show();
        } else {
            $("#useraddTecnico").hide();
        }
    }
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
        $("#localidad").text(data.localidad);
        $("#psswduser").text(data.psswduser);
        $("#grupouser").text(data.grupouser);
        $("#nameuser").text(data.nameuser);
        $("#oficina").text(data.oficina);
        // Modificamos el enlace
        $("#usermodTecnico").attr( 'href', "/usermod/envio/" + $("#usuarioCliente").val() );
        // Una vez todo configurado, mostramos y ocultamos
        toggleUseradd(false);
        $("#respuesta").show();
    }else{
        $("#useraddTecnico").attr( 'href', "/useradd/" + $("#usuarioCliente").val() );
        toggleUseradd(true);
        $("#respuesta").hide();
    }
};

var datos = function(){
    $.ajax({
        type: 'POST',
        url: '/usershow/datos',
        dataType: 'json',
        data: {
            usuarioCliente: $("#usuarioCliente").val()
        },
        success: mostrarDatos
    });
};