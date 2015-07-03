$(document).ready(function(){
    $("#comprobacion_usuario").hide();
    $.establecimientos = new Object(); 
    $.oficinas = new Object();
    $.carreras = new Object();
    $.ajax({
        url: "/ui/js/controladores/establecimientos.json",
        dataType: 'json',
        success: (function(data){
            $.establecimientos = data;
        })
    });
    $.ajax({
        url: "/ui/js/controladores/oficinas.json",
        dataType: 'json',
        success: (function(data){
            $.oficinas = data;
        })
    });
    $.ajax({
        url: "/ui/js/controladores/carreras.json",
        dataType: 'json',
        success: (function(data){
            $.carreras = data;
            $.each($.carreras, function(i,e){
                $('#carrera').append($('<optgroup>', { 
                    label: e.nombre,
                    id: i
                }));
                //console.log(e.lista);
                $.each(e.lista, function(j,e){
                    $('#' + i).append($('<option>', { 
                        value:e,
                        text: e
                    }));
                    //console.log(e);
                });
            });
        })
    });
    obtenerDatosUsuario();    
});

var obtenerDatosUsuario = function(){
    $.ajax({
        url: "/actualizacion/usuario",
        dataType: 'json',
        success: mostrarDatosUsuario,
    });
}

var mostrarDatosUsuario = function(data){
    $.each($.establecimientos, function(i, e){
        if($.inArray(data.localidad, e) === -1){
            console.log("Habrá que actualizar de veras los datos");
        }else{
            $("#st [value=" + i + "]").prop("selected", 1);
            configurarEstablecimiento(i); 
            $("#o [value='" + data.localidad + "']").prop("selected", 1);
            configurarOficina(data.localidad);
            $("#ou [value='" + data.oficina + "']").prop("selected", 1);
            return true;
        }
    });
}

$("#enviar").click(function(e){
    e.preventDefault();
    e.stopPropagation();
    datos = recogerDatos();
    procesarDatos('/actualizacion/cambio', datos, mostrarDatos);
});

$("#reset").click(function(e){
    e.preventDefault();
    e.stopPropagation();
    console.log("Todavía hay trabajo que hacer por acá");
});

/**
var configurarEstablecimiento = function(valor){
    establecimientos = $.establecimientos[valor];
    $("#o option").remove();
    $("#o").append("<option selected disabled>Escoja el Establecimiento</option>");
    $.each(establecimientos, function(index, value){
        $('#o').append($('<option>', { 
            value: value,
            text : value
        }));
    });
}
*/

var configurarEstablecimiento = function(valor){
    establecimientos = $.establecimientos[valor];
    $.each(establecimientos, function(i, e){
        $("#o").append($('<optgroup>', {
            label: i,
        }));
        $.each(e, function(j,k){
            $("[label='" + i + "']").append($('<option>', {
                value: k,
                text: k
            }));
        });
    });
            
}
var configurarOficina = function(valor){
    oficinas = $.oficinas[valor];
    if(typeof oficinas === 'undefined') {
        $("#div_ou #ou").each(function(i,e){
            e.remove();
        });
        $("#div_ou").append('<input type="text" class="form-control input-sm" id="ou" name="ou" placeholder="Oficina" autocomplete="off">');
    }else {
        $("#div_ou input").each(function(i,e){
            e.remove();
        });
        $("#div_ou").append('<select class="form-control input-sm col-xs-12" id="ou" name="ou">');
        $("#ou").append("<option selected disabled>Escoja oficina</option>");
        $.each(oficinas, function(index, value){
            $('#ou').append($('<option>', { 
                value: value,
                text : value
            }));
        });
    }
}

$('#st').change(function(e){
    valor = $('#st').val();
    configurarEstablecimiento(valor); 
});

$('#o').change(function(e){
    valor = $('#o').val();
    configurarOficina(valor); 
});


var mostrarDatos = function(data){
    console.log("Al menos estoy llegando?");
    console.log(data);
    pmostrarError(data);
    pmostrarMensaje(data);
};
