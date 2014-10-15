$(document).ready(function(){
    
});

$("#enviar").click(function(e){
    e.stopPropagation();
    e.preventDefault();
    datos = recogerDatos();
    procesarDatos('/confdominios/modificar', datos, mostrarRespuesta);
});

$("#reset").click(function(e){
    e.stopPropagation();
    e.preventDefault();
});

$("#reset_password_zimbra").click(function(e){
    $('#dialogo input[type=password]').val("");
    e.stopPropagation();
    e.preventDefault();
    mensaje_modal("Configuracion Contraseña Zimbra", set_password_zimbra);
});

$("#reset_password_samba").click(function(e){
    console.log("A modificar para samba");
    $('#dialogo input[type=password]').val("");
    e.stopPropagation();
    e.preventDefault();
    mensaje_modal("Configuracion Contraseña Samba", set_password_samba);
});

var mostrarRespuesta = function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
};

var mostrarRespuestaPassword = function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
    cerrar_popup_modal();
};

var set_password_zimbra = function(){
    datos = recoger_datos_popup();
    if (datos !== false) {
        $("#dialogo_mensaje").hide();
        procesarDatos('/confdominios/password/zimbra', datos, mostrarRespuestaPassword);
    }
};

var set_password_samba = function(){
    console.log("A modificar para samba");
    datos = recoger_datos_popup();
    if (datos !== false) {
        $("#dialogo_mensaje").hide();
        procesarDatos('/confdominios/password/samba', datos, mostrarRespuestaPassword);
    }
};

var recoger_datos_popup = function(){
    var password = $("#dialogo #password_set").val();
    var confirm = $("#dialogo #password_confirm").val();
    if (password === confirm) {
        $("#cargador").show();
        return {'dominio': $("#dominio").val(), 'password': password};
    }else{
        $("#dialogo_mensaje").show().text("Las contraseñas no coinciden");
        return false;
    }
};

var cerrar_popup_modal = function(){
    $("#cargador").hide();
    $("#dialogo_mensaje").hide();
    $("#dialogo").dialog( "close" );
};

var mensaje_modal = function(titulo, envio_datos){
    $("#dialogo").dialog({
        modal: true,
        title: titulo,
        draggable: false,
        resizable: false,
        width: 500,
        buttons: [ 
            { 
                text: "Cancelar", 
                click: cerrar_popup_modal
            },
            { 
                text: "Enviar", 
                click: envio_datos
            }
        ]
    });
    };