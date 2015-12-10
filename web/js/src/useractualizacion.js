$(document).ready(function(){
    $.dataUsuario = new Object();

    /* Obtenemos datos del usuario y usamos a mostrarDatosUsuarios para colocar estos valores en los controles */
    $.ajax({
        url: "/actualizacion/usuario",
        dataType: 'json',
        success: configurarDatosUsuarios,
    });

    /* Creo el control para seleccionar fecha */
    $( "#fecha" ).datepicker({
       changeMonth: true,
       changeYear: true,
       yearRange: "-100Y:-18Y"
    });
    
    /* Agrego métodos al validator para que comprueba los nombres tal como los queremos*/
    $.validator.addMethod('nombre', function(valor, elemento){
        return /^(([A-Z][a-záéíóú]+\s?(de\s)*)){1,3}$/.test(valor);
    }, "Verifique su nombre");
    
    $.validator.addMethod('fecha', function(valor, elemento){
        return /^(((1|2|0*)[1-9])|(3[0-1]))\/((0*[1-9])|(1[0-2]))\/(1|2)[0-9]{3}$/.test(valor);
    }, "Verifique que la fecha sea válida");
    
    /* El formulario ha de validarse, si es correcto ejecutará envío */
    $.validator.setDefaults({
        submitHandler: envio,
    });

    $('#actualizacionDatos').validate();
    
    /* Activamos el autocompletado en el control de establecimientos */
    oAutocomplementar();
});

/*
 * Funciones varias para hacer un montón de cosas
 **/

/* Procesa los datos */
var envio = function(){
    datos = recogerDatos();
    datos['o'] = $('#o').data('o');
    procesarDatos('/actualizacion/cambio', datos, mostrarDatos);
};

/* Auxiliar que muestra errores y mensajes gracias a las funciones predichas en agenlad.js */
var mostrarDatos = function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
};


/* Recibe los datos del usuario */
var configurarDatosUsuarios = function(data){
    $.dataUsuario = data;
    llenarControlesDatosUsuario($.dataUsuario);
}

/* Llena los controles con los datos recibidos.
 * Verifia que los datos recibidos no sean {empty}, valor por defecto,
 * caso contrario lo configurar vacío implícitamente
 * La revision de O es nada más un poco más compleja, pero poco
 * */
var llenarControlesDatosUsuario = function(data){
    var equivalencias = {'#nombre':'nameuser', '#apellido': 'apelluser', '#pregunta': 'pregunta', '#respuesta': 'respuesta', '#fecha': 'fecha','#ou': 'ou','#title': 'cargo', '#jvs': 'jvs', '#nit': 'nit', '#telephoneNumber': 'phone'};
    $.each(equivalencias, function(elem, indice){
        var valor = data[indice];
        if (!isEmpty(valor)){
            $(elem).val(valor);
        }else{
            $(elem).val('');
        }
    });

    /* El atributo o (localidad) necesita nuestro desfalco para que se comporte como un select sin serlo, y acá esta como, y neceita configurar 
     */
    if (!(isEmpty(data.o))){
        $('#o').val(data.o.label);
        $('#o').attr('data-o', data.o.id);
        ouAutocomplementar(data.o.id);
    }else{
        $('#o').val('');
    }
};


/*
 * Control de controles
 **/
$('#actualizacionDatos').submit(function(e){
    e.preventDefault();
});

$("#reset").click(function(e){
    e.preventDefault();
    $('#actualizacionDatos').validate().resetForm();
    llenarControlesDatosUsuario($.dataUsuario);
});

$('#hasJvs').change(function(e){
    if ($('#hasJvs').is(':checked')){
        $('#jvs').attr('disabled', false); 
    }else{
        $('#jvs').attr('disabled', true); 
    }
});
