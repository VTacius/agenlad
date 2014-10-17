$(document).ready(function(){
    
});

var envio_busqueda = function (req, resp) {
    $.getJSON("/confpermisos/busqueda/" + encodeURIComponent(req.term), resp);
};

var configurarControles = function(dominio, rol){
    $("#rol_usuario_select [value='" + rol + "']").attr('selected','selected');
    $("#dominio_usuario").text(dominio);
};

var mostrarRolUsuario = function(datos){
    console.log(datos);
    configurarControles(datos[0]['dn'], datos[0]['rol']);
};

var usuario_seleccionado = function(event, ui){
    datos = {usuario: ui.item.value};
    procesarDatos ('/confpermisos/rol/', datos, mostrarRolUsuario);
    $("#rol_usuario").show();
    $("#busqueda_usuario_form #enviar").show();
};

var usuario_modificar = function(usuario){
    datos = {usuario: usuario};
    procesarDatos ('/confpermisos/rol/', datos, mostrarRolUsuario);
    $("#rol_usuario").show();
    $("#busqueda_usuario_form #enviar").show();
    
    $("#busqueda_usuario_input").val(usuario);
};

$("#busqueda_usuario_input").autocomplete({
    minLength: 2,
    source: envio_busqueda,
    select: usuario_seleccionado
});

$("#busqueda_usuario_form").submit(function(e){
    e.stopPropagation();
    e.preventDefault();
});

$("button[id=editar_usuario]").click(function(e){
    e.stopPropagation();
    e.preventDefault();
    console.log($(this).val());
    usuario_modificar($(this).val());
});

$("button[id=borrar_usuario]").click(function(e){
    e.stopPropagation();
    e.preventDefault();
    console.log("Eliminar " + $(this).val());
});
