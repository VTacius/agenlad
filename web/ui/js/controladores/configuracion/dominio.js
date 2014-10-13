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

$("#reset_password_zimbra").click(function(){
    e.stopPropagation();
    e.preventDefault();
});

$("#show_admin_zimbra_password a").click(function(e){
    e.stopPropagation();
    e.preventDefault();
    $("#admin_zimbra_password").show();    
    $("#div_admin_zimbra_password").show();    
    $("#show_admin_zimbra_password").hide();
    $("#div_admin_zimbra_password").attr('hidden', false);    
});

var mostrarRespuesta = function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
};

var recogerDatos = function(){
    var contenido = {};
    $("input[type=text]").each(function(i,e){
        contenido[$(e).attr('name')] = $(e).val();
    });
    $("input[type=hidden]").each(function(i,e){
        contenido[$(e).attr('name')] = $(e).val();
    });
    $("input[type=radio]:checked").each(function(i,e){
        contenido[$(e).attr('name')] = $(e).val();
    });
    return contenido;
};