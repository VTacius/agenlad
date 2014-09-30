$(document).ready(function(){
    toggleUseradd(false);
    $("dl").hide();
});

$("#enviar").click(function(e){
    e.stopPropagation(); 
    e.preventDefault();
    datos();
});


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
    mostrarErrorConexion(data);
    // Llenamos los datos
    if (!(data.nameuser==="{empty}" && data.buzonstatus==="{empty}")){
        $("#cuentastatus").text(data.cuentastatus);
        $("#buzonstatus").text(data.buzonstatus);
        $("#localidad").text(data.localidad);
        $("#psswduser").text(data.psswduser);
        $("#grupouser").text(data.grupouser);
        $("#nameuser").text(data.nameuser);
        $("#oficina").text(data.oficina);
        $("#usermod").text(data.usermod);
        $("#mailuser").text(data.mailuser);
        // Modificamos el enlace
        $("#usermodTecnico").attr( 'href', "/usermod/" + $("#usuarioCliente").val() );
        // Una vez todo configurado, mostramos y ocultamos
        toggleUseradd(false);
        $("dl").show();
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
        success: mostrarDatos,
        error: errorOnResponse
    });
};
