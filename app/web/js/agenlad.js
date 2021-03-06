/* jquery */
//=include ./vendor/jquery.js
/* jquery-ui */
//=include ./vendor/core.js
//=include ./vendor/widget.js
//=include ./vendor/position.js
//=include ./vendor/menu.js
//=include ./vendor/autocomplete.js
//=include ./vendor/datepicker.js
/* bootstrap */
//=include ./vendor/dropdown.js
//=include ./vendor/collapse.js
//=include ./vendor/alert.js
/* Handlebars */
//=include ./vendor/handlebars.js
/* jquery-validate */
//=include ./vendor/jquery.validate.js
//=include ./vendor/messages_es.js
/* jQueryMask */
//=include ./vendor/jquery.mask.js
$(document).ready(function(){
    $.agenlad = new Object();
    $.ajax({
        url: "/js/data/establecimientos.json",
        dataType: 'json',
        success: establecimientos
    });
    
    /**
     * Nada con {empty} pasará 
     */
    Handlebars.registerHelper('contenido', function(texto) {
        var contenido = (texto === '{empty}') ? "" : texto;
        return new Handlebars.SafeString(contenido);
    });
    
    /**
     *  Contruye una frase con o y ou que sea semánticamente correcta
     */
    Handlebars.registerHelper('establecimiento', function(o, ou) {
        var contenido = "";
        var est = "";
        if (!isEmpty(o)){
            if($.type( o ) === 'string'){
                est = ($.isNumeric(o)) ? $.agenlad.establecimientos[o]: o ;
            }else if($.inArray('label', o)){
                est = o.label;
            }else{
                console.log("Este es un caso que no he tomado en cuenta");
                console.log(o);
            }
        }
        if (isEmpty(ou)){
            contenido = est;
        }else{
            contenido = ou + ' de ' + est;
        }
        return new Handlebars.SafeString(contenido);
    });
    oAutocomplementar();
});
/**
 * Contrucción de controles o con la función de autocomplementado
 */
var oAutocomplementar = function(){
    $("#o" ).autocomplete({
        minLength: 2,
        source: function( request, response ) {
            $.ajax({
                url: "/helpers/establecimiento",
                type: 'POST',
                dataType: "json",
                data: {
                  busqueda: request.term
                },
                success: function( data ) {
                  response( data );
                }
            })
        },
        select: function( event, ui ) {
            $(this).attr('data-o', ui.item.id);
            ouAutocomplementar(ui.item.id);
        },
    });
}

/**
 * Contrucción de controles ou con la función de autocomplementado
 * Debería ser llamado sólo por medio de oAutocomplementar
 */
var ouAutocomplementar = function(o){
    $("#ou" ).autocomplete({
        minLength: 2,
        source: function( request, response ) {
            $.ajax({
                url: "/helpers/oficina",
                type: 'POST',
                dataType: "json",
                data: {
                  busqueda: request.term,
                  establecimiento: o
                },
                success: function( data ) {
                  response( data );
                }
            })
        },
    });
};

/*
 * Forma una sola lista con los establecimientos, luego configura el elemento #o para que 
 * lo use en su autocompletado
 **/
var establecimientos = function(data){
    $.agenlad.establecimientos = [];
    $.each(data, function(i,e){
        $.agenlad.establecimientos[i] = e;
    });
};


/**
 * Comprueba si el parametro pasado es nulo de varias formas posibles
 * @param {var} obj
 * @returns {Boolean}
 */
function isEmpty(obj) {
    if (typeof obj === 'undefined' || obj === null || obj === '') return true;
    if (typeof obj === 'number' && isNaN(obj)) return true;
    if (typeof obj === 'object' && obj.length === 0) return true;
    if (obj instanceof Date && isNaN(Number(obj))) return true;
    if (obj == "{empty}"|| obj =="empty") return true;
    return false;
}

/**
 * Método a usar en procesarDatos cuando el servidor entre en estado de error
 * En realidad, se aconseja su uso para toda funcion de petición tipo ajax pueda manejar los errores que recibe
 * @param {array} data
 * @returns {undefined}
 */
function errorOnResponse(data){
    console.log(data.responseJSON);
    var source = $('#errorOnResponse-template').html();
    var mensaje = {'mensaje':[{'titulo': 'Fallo en la aplicacion', 'mensaje':'La aplicación presenta un problema muy grave. Contacte con un informático sobre este problema'}]};
    var template = Handlebars.compile(source);
    var contenido = template(mensaje.mensaje);
    $('#error').show();
    $('#error .alert-dismissible').remove();
    $("#error").append(contenido);
    $('#espera').hide();        
};

/**
 * Mediante el uso de Handlebars, hago uso del template #errorOnResponse-template para 
 * formar las cajas de control con lo que puedo crear los mensajes de error
 *
 */
function pmostrarError(data){
    if(!isEmpty(data.error)){
        console.log(data.error);
        var source = $('#errorOnResponse-template').html();
        var template = Handlebars.compile(source);
        var contenido = template(data.error);
        $('#error').show();
        $('#error .alert-dismissible').remove();
        $("#error").append(contenido);
        $('#espera').hide();        
    }
};

/**
 * Mediante el uso de Handlebars, formo las cajas de texto con los mensajes de error
 */

function pmostrarMensaje(data){
    if(!isEmpty(data.mensaje)){
        console.log(data.mensaje);
        var source = $('#errorOnResponse-template').html();
        var template = Handlebars.compile(source);
        var contenido = template(data.mensaje);
        $('#mensaje').show();
        $('#mensaje .alert-dismissible').remove();
        $("#mensaje").append(contenido);
        $('#espera').hide();        
        $('html, body').animate({scrollTop : 0},800);
    }
};

/*
 * Supongo que en algún momento, será "mediante el uso de Handlebars 
 */
function mostrarErrorConexion(data){
    console.log(data);
};

/**
 * Envia una petición del tipo POST en espera de datos json
 * Cuide que de no agregar () al nombre de funcion
 * Al empezar, muestra el cargador y se encarga de cerrarlo cuando la respuesta del servidor haya terminado
 * gracias al evento complete
 * @param {string} url
 * @param {array} datos
 * @param {callback} funcion
 * @returns {undefined}
 */
function procesarDatos (url, datos, funcion, e){
    /* console.log(e); */
    e || ( e = 'default' );
    if (!isEmpty(e.currentTarget)){
        if (typeof(e.currentTarget.type === 'button')){
            console.log(e.currentTarget.type);
        }
    }
    $('#espera').show()        
    $.ajax({ 
        type: 'POST',
        url: url,
        dataType: 'json',
        data: datos,
        success: funcion,
        error: errorOnResponse,
        complete: function(){
            $('#espera').hide()        
        }
    });
};

/**
 * Crea un array listo para enviar por medio de una consulta JSON al servidor en 
 * base al tipo de objeto que sea que encuentre en el formulario 
 * @returns {objetos}
 */
function recogerDatos (){
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
    $("select").each(function(i,e){
        var nombre = $(e).attr('name');
        contenido[nombre] = $("#" + nombre ).val();
    });
    return contenido;
};
