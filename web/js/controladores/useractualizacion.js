$(document).ready(function(){
    $("#comprobacion_usuario").hide();
    $.establecimientos = new Object(); 
    $.oficinas = new Object();
    $.carreras = new Object();
    $.ajax({
        url: "/js/data/establecimientos.json",
        dataType: 'json',
        success: (function(data){
            $.establecimientos = data;
        })
    });
    $.ajax({
        url: "/js/data/oficinas.json",
        dataType: 'json',
        success: (function(data){
            $.oficinas = data;
        })
    });
    $.ajax({
        url: "/js/data/carreras.json",
        dataType: 'json',
        success: (function(data){
            $.carreras = data;
            $.each($.carreras, function(i,e){
                $('#carrera').append($('<optgroup>', { 
                    label: e.nombre,
                    id: i
                }));
                $.each(e.lista, function(j,e){
                    $('#' + i).append($('<option>', { 
                        value:e,
                        text: e
                    }));
                });
            });
        })
    });
    $('#carrera').select2();
    // Empiezo el trabajo con la validación
    $.validator.setDefaults({
        debug: true,
        submitHandler: envio,
    });
    $.validator.addMethod('regexador', function(valor, elemento, regex){
        return regex.test(valor);
    }, "El contenido no es una cadena válida");
    $("#actualizacionDatos").validate({
        rules: {
            nombre: {
                regexador: /^(([A-Z][a-záéíóú]+\s?(de\s)*)){2,3}$/
            },
            apellido: {
                regexador: /^(([A-Z][a-záéíóú]+\s?(de\s)*)){2,3}$/
            },
            carrera: "required",
            st: "required",
            o: "required", 
            ou: "required",
            title: "required",
            pregunta: {
                email: true,
                minlength: 2
            },
            telephoneNumber:{
                regexador: /^((\-*[0-9]{4}){1,2}$|$)/
            },
            pregunta: {
                required: true,
                minlength: 5
            },
            respuesta: {
                required: true,
                minlength: 5
            },
        },
        messages: {
            nombre: "Revise la forma de sus nombres",
            apellido: "Revise la forma de sus apellidos ",
            username: {
                required: "Please enter a username",
                minlength: "Your username must consist of at least 2 characters"
            },
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long"
            },
            confirm_password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long",
                equalTo: "Please enter the same password as above"
            },
            email: "Please enter a valid email address",
            agree: "Please accept our policy"
        }

    });

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
};

var envio = function(e){
    //e.preventDefault();
    //e.stopPropagation();
    datos = recogerDatos();
    procesarDatos('/actualizacion/cambio', datos, mostrarDatos);
};

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
    $("#o optgroup").remove();
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
    $('#o').select2();
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
