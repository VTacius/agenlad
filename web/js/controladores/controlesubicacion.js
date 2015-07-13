$(document).ready(function(){
    /*
     * Obtenemos los datos en establecimientos.json, con los que formamos el elemento #st 
     **/
    $.ajax({
        url: "/js/data/establecimientos.json",
        dataType: 'json',
        success: configurarTipoEstablecimiento
    });
    
    /* 
     * Obtenemos los datos en oficias.json, pero no creamos el elemento #ou sino hasta que se haya escogido una opción en #o 
     * */
    $.ajax({
        url: "/js/data/oficinas.json",
        dataType: 'json',
        success: (function(data){
            $.oficinas = data;
        })
    });
});

/*
 * Configurara #st con los tipos de establecimiento disponibles
 **/
var configurarTipoEstablecimiento = function(data){
    $.establecimientos = data;
    $.each($.establecimientos[0], function(i,e){
        $('#st').append($('<option>', { 
            value: e,
            text: e
        }));
        
    });
};

/* Crea las opciones del select #o con los establecimientos que pertenecen a un tipo dado 
 * No se valida el que existe ese tipo de establecimientos, porque hemos supuesto esa comprobación
 * a la hora de crear el fichero
 **/
var configurarEstablecimiento = function(valor){
    establecimientos = $.establecimientos[1][valor];
    $("#o option").remove();
    $("#o").append("<option selected disabled>Escoja su establecimiento</option>");
    $.each(establecimientos, function(i,e){
        $("#o").append($('<option>', {
            value: e,
            text: e
        }));
    });
};

/* Crea las opciones del select #ou con las oficinas que pertenecen a un establecimiento dado 
 * Comprueba que existan oficinas para el establecimiento dado.
 * Faćilmente puede considerarse como redundante, pero el control puede cambiar de un select a un input
 * muchas veces desde la misma ejecución.
 **/
var configurarOficina = function(valor){
    $("#div_ou #ou").each(function(i,e){
        e.remove();
    });
    if (valor in $.oficinas) {
        oficinas = $.oficinas[valor];
        $("#div_ou").append('<select class="form-control input-sm col-xs-12" id="ou" name="ou">');
        $("#ou").append("<option selected disabled>Escoja oficina</option>");
        $.each(oficinas, function(index, value){
            $('#ou').append($('<option>', { 
                value: value,
                text : value
            }));
        });
    }else {
        $("#div_ou #ou").each(function(i,e){
            e.remove();
        });
        $("#div_ou").append('<input type="text" class="form-control input-sm" id="ou" name="ou" placeholder="Oficina" autocomplete="off">');
    }
};

/*
 * Sos tan gracioso al perder una función  
 * Buscamos el tipo de establecimiento para la localidad que el usuario posee, en caso de hallarse tal cosa
 * significa que el establecimiento que tiene configurado es válido, podemos configurar los 
 * controles para tipo, establecimiento y llenar adecuadamente oficinas donde las haya
 **/

var buscarTipoEstablecimiento = function(lugar){
    var tipo;
    $.each($.establecimientos[1], function(i,e){
        if (!($.inArray(lugar, e) === -1)){
            tipo = i;
            return false;
        } 
    });    
    return tipo; 
}

/** 
 * Cambios en el select "Tipo de establecimiento" 
 **/
$('#st').change(function(e){
    valor = $('#st').val();
    configurarEstablecimiento(valor); 
});

/* 
 * Cambios en el select "Tipo de establecimiento" 
 **/
$('#o').change(function(e){
    valor = $('#o').val();
    configurarOficina(valor); 
});
