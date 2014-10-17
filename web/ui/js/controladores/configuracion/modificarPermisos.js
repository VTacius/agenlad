$(document).ready(function(){
    
});

var envio_busqueda = function (req, resp) {
    $.getJSON("/confpermisos/busqueda/" + encodeURIComponent(req.term), resp);
};

var usuario_seleccionado = function(event, ui){
    console.log(ui.item.value);  
    $("#rol_usuario").show();
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
