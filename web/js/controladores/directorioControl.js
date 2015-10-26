$(document).ready(function(){
    $("#espera").show();
    var filtraje = filtro();
    procesarDatos("/directorio/busqueda", filtraje, tabularUsuarios);
    $.directorioControl = new Object();
    $.directorioControl.pulsaciones = 0;
    
    $.directorioControl.establecimientos = [];
    $.directorioControl.lugares = [];
    /* Buscare en todos los establecimientos */
    $.ajax({
        url: "/js/data/establecimientos.json",
        dataType: 'json',
        success: establecimientos 
    });
});

/*
 * Forma una sola lista con los establecimientos, luego configura el elemento #o para que 
 * lo use en su autocompletado
 **/
var establecimientos = function(data){
    $.each(data, function(i,e){
        $.merge($.directorioControl.lugares, e);
        $.directorioControl.establecimientos[i] = e;
    });
    $( "#o" ).autocomplete({
        source: $.directorioControl.lugares
    });
};

/**
 * Si presiona enter, en lugar de enviar el formulario, ejecuta la busqueda con lo que tenga
 * @param {bind?} e
 */
$("input").keypress(function(e){
    if ( e.which === 13 ) {
        var filtraje = filtro();
        procesarDatos("/directorio/busqueda", filtraje, tabularUsuarios);
        e.preventDefault();
    } 
});

/**
 * Una vez ha presionado la tecla más de tres dos, ejecuta la busqueda
 * @param {type} e
 */
$("input").keyup(function(e){
    verificaVacio();
    verificaEspacio($("#uid").val());
    if (!( e.which === 0)){
   	    if ($.directorioControl.pulsaciones  >= 2) {
   	        var filtraje = filtro();
            procesarDatos("/directorio/busqueda", filtraje, tabularUsuarios);
   	        $.directorioControl.pulsaciones = 0;
   	    }else{
   	        $.directorioControl.pulsaciones++;
   	    }
    }
});

/**
 * Al borrar todos los datos, volvamos a llenar los datos
 * TODO: Algo me dice que esto no puede estar bien, dadas las condiciones de las
 *  demás funciones
 * @returns {undefined}
 */
var verificaVacio = function(){
    if (isEmpty($("#o").val()) && isEmpty($("#ou").val()) && isEmpty($("#uid").val())) {
        var filtraje = filtro();
        procesarDatos("/directorio/busqueda", filtraje, tabularUsuarios);
    }
};

/**
 * Realiza una consulta cuando se ingresa un espacio, suponemos que es posible 
 * que el nombre de alguien se parezca al usuario de muchos
 * TODO: Esto debería estar en el keyup del #uid, pero no sÃ© que tal se llevara 
 * con dos metodos enlazados al mismo evento
 * @param {String} valor
 * @returns {undefined}
 */
var verificaEspacio = function(valor){
    if (valor.indexOf(" ")!==-1){
        var filtraje = filtro();
        procesarDatos("/directorio/busqueda", filtraje, tabularUsuarios);
    }
};

/**
 * Ayuda a asignando un valor por defecto a los valores enviados al servidor
 * como filtro
 * @param {type} valor
 * @returns {String}
 */
var preFiltro = function(valor){
    if (!isEmpty(valor)) {
        return valor + "*";
    }else{
        return "*";
    }
};

/**
 * Forma los datos a ser enviados al servidor como filtro
 * @type type
 */
var filtro = function(){
    var filtro = {
        'uid': preFiltro($("#uid").val()),
        'o': preFiltro($("#o").val()),
        'ou': preFiltro($("#ou").val()),
    };
    return filtro;
};

/**
 * Sea cual sea la informacion que reciba, la muestra en pantalla
 * @param {array} respuesta
 * @returns {undefined}
 */
var tabularUsuarios = function(respuesta){
/* Agrego esta funciÃ³n a nuestro objeto json para que sea capaz de limpiar los atributos "empty", ademÃs de asignarle un
        establecimiento real en base al identificador númerico que algunos ya tienen asignados */
    respuesta.establecimiento = function(){
        if ($.isNumeric(this.o)){
            return $.directorioControl.establecimientos[this.o];
        }else{
            return this.o === "empty" ? "" : this.o
        }
    };
    var template = $('#respuesta-template').html();
    Mustache.parse(template);
    var contenido = Mustache.render(template, respuesta);
    pmostrarError(respuesta);
    pmostrarMensaje(respuesta);
    $("#respuesta tr").remove();
    $("#respuesta").append(contenido);
};

