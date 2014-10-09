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

$("#show_admin_zimbra_password a").click(function(e){
    console.log("Cambiemos a usuario zimbra");
    e.stopPropagation();
    e.preventDefault();
    $("#div_admin_zimbra_password").show();    
    $("#div_admin_zimbra_password").attr('hidden', false);    
    $("#admin_zimbra_password").show();    
    $("#show_admin_zimbra_password").hide();
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