$(document).ready(function(){
    $("#comprobacion_usuario").hide();
});

$("#disponibilidad").click(function(e){
    e.preventDefault();
    e.stopPropagation();
    var datos = {'uid': $("#uid").val()}; 
    procesarDatos ('/useradd/checkuid', datos, mostrarDisponibilidad);
});

$("#enviar").click(function(e){
    e.preventDefault();
    e.stopPropagation();
    datos = recogerDatos();
    procesarDatos('/useradd/creacion', datos, mostrarDatos);
});

var mostrarDisponibilidad = function(data){
    if(data.clase) {
        $("#comprobacion_usuario").children('p').removeClass('alert-danger').addClass('alert-success');
        $("#comprobacion_usuario").children('p').children('b').text(data.mensaje);
        $("#comprobacion_usuario").children('p').children('span').removeClass('glyphicon-remove').addClass('glyphicon-ok');
    } else {
        $("#comprobacion_usuario").children('p').removeClass('alert-success').addClass('alert-danger');
        $("#comprobacion_usuario").children('p').children('b').text(data.mensaje);
        $("#comprobacion_usuario").children('p').children('span').removeClass('glyphicon-ok').addClass('glyphicon-remove');
    }
    $("#comprobacion_usuario").show();
};

var mostrarDatos = function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
};
