$(document).ready(function(){
    $.dominioNuevo = new Object();
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
    e.stopPropagation($.dominioNuevo.passwordSamba);
    e.preventDefault();
    mensaje_modal("Configuracion Contraseña Samba", set_password_samba);
});

$("#base").change(function(){
    base = $(this).val();
    $("#base_usuario").val("ou=Users," + base);
    $("#base_grupo").val("ou=Groups," + base);
});

$("#enviar").click(function(e){
    e.stopPropagation();
    e.preventDefault();
    datos = recogerDatos();
    datos.passwordZimbra = $.dominioNuevo.passwordZimbra;
    datos.passwordSamba = $.dominioNuevo.passwordSamba;
    procesarDatos('/confdominios/nuevo/crear', datos, resultado);
});

$("#dominio").change(function(){
    dominio = $(this).val();
    dc = dominio.split(".");
    base = "";
    $(dc).each(function(i,e){
        base += "dc=" + e + ",";
    });
    base = base.replace(/,$/,"");
    $("#base").val(base);
    $("#base_usuario").val("ou=Users," + base);
    $("#base_grupo").val("ou=Groups," + base);
});

var resultado = function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
};

var set_password_zimbra = function(){
    datos = recoger_datos_popup();
    if (datos !== false) {
        $("#dialogo_mensaje").hide();
        $.dominioNuevo.passwordZimbra = datos;
        cerrar_popup_modal();
    }
};

var set_password_samba = function(){
    console.log("A modificar para samba");
    datos = recoger_datos_popup();
    if (datos !== false) {
        $("#dialogo_mensaje").hide();
        $.dominioNuevo.passwordSamba = datos;
        cerrar_popup_modal();
    }
};

var recoger_datos_popup = function(){
    var password = $("#dialogo #password_set").val();
    var confirm = $("#dialogo #password_confirm").val();
    if (password === confirm) {
        $("#cargador").show();
        return password;
    }else{
        $("#dialogo_mensaje").show().text("Las contraseñas no coinciden");
        return false;
    }
};
var cerrar_popup_modal = function(){
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
                text: "Guardar Contraseña", 
                click: envio_datos
            }
        ]
    });
};