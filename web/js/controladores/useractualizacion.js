$(document).ready(function(){
    $("#comprobacion_usuario").hide();
    $.establecimientos = new Object(); 
    $.oficinas = new Object();
    $.carreras = new Object();

    /* Obtenemos datos del usuario y usamos a mostrarDatosUsuarios para colocar estos valores en los controles */
    $.ajax({
        url: "/actualizacion/usuario",
        dataType: 'json',
        success: mostrarDatosUsuario,
    });

    /* Inicializo validator, con entre otras cosas, el método que ha de tratar el envío de la información cuando sea verdadero */
    $.validator.setDefaults({
        debug: true,
        submitHandler: envio,
    });

    /*
     * Creo el control para seleccionar fecha
     **/
     $( "#fecha" ).datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "-100Y:-18Y"
     });

    $.validator.addMethod('regexador', function(valor, elemento, regex){
        return regex.test(valor);
    }, "El contenido no es una cadena válida");
    

});

/* Con los datos del usuario, establecemos los controles según los datos válidos que el usuario tenga ya configurados */
var mostrarDatosUsuario = function(data){
    tipo = buscarTipoEstablecimiento(data.localidad);
    if (!isEmpty(tipo)){
        $('#st option[value="' + tipo + '"]').attr('selected', true);
        configurarEstablecimiento(tipo);
        $('#o option[value="' + data.localidad + '"]').attr('selected', true);
        configurarOficina(data.localidad);
        $('#ou option[value="' + data.oficina + '"]').attr('selected', true);
    }
};

/* Auxiliar que muestra errores y mensajes gracias a las funciones predichas en agenlad.js */
var mostrarDatos = function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
};

/* El envío de datos del formulario para su procesamiento y posterior mostraje se realiza 
 * en este lugar gracias a las funciones que hacemos en agenlad.js
 **/
var envio = function(e){
    datos = recogerDatos();
    console.log(datos);
    procesarDatos('/actualizacion/cambio', datos, mostrarDatos);
};


