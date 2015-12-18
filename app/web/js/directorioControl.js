$(document).ready(function(){
    var filtraje = filtro();
    procesarDatos("/directorio/busqueda", filtraje, tabularUsuarios, this);
    $.directorioControl = new Object();
    $.directorioControl.pulsaciones = 0;
    oAutocomplementar();
});

/**
 * TODO: En algún momento le cambias por los metodos descritos en usershowControl.js
 * Al borrar todos los datos, volvamos a llenar los datos
 * TODO: Algo me dice que esto no puede estar bien, dadas las condiciones de las
 *  demás funciones
 * @returns {undefined}
 */
var verificaVacio = function(e){
    if (isEmpty($("#o").val()) && isEmpty($("#ou").val()) && isEmpty($("#uid").val())) {
        var filtraje = filtro();
        procesarDatos("/directorio/busqueda", filtraje, tabularUsuarios,e);
    }
};

/**
 * TODO: En algún momento le cambias por los metodos descritos en usershowControl.js
 * Realiza una consulta cuando se ingresa un espacio, suponemos que es posible 
 * que el nombre de alguien se parezca al usuario de muchos
 * TODO: Esto debería estar en el keyup del #uid, pero no sÃ© que tal se llevara 
 * con dos metodos enlazados al mismo evento
 * @param {String} valor
 * @returns {undefined}
 */
var verificaEspacio = function(valor, e){
    if (valor.indexOf(" ")!==-1){
        var filtraje = filtro();
        procesarDatos("/directorio/busqueda", filtraje, tabularUsuarios, e);
    }
};

/**
 * TODO: En algún momento le cambias por los metodos descritos en usershowControl.js
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
 * TODO: En algún momento le cambias por los metodos descritos en usershowControl.js
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
    var source = $('#respuesta-template').html();
    var template = Handlebars.compile(source);
    respuesta.datos.sort(function(a, b){
        var aUid = a.cn;
        var bUid = b.cn;
        return ((aUid < bUid) ? -1 : ((aUid > bUid) ? 1: 0))
    });
    
    var contenido = template(respuesta);

    pmostrarError(respuesta);
    pmostrarMensaje(respuesta);
    $("#respuesta tr").remove();
    $("#respuesta").append(contenido);
};

/**
 * Si presiona enter, en lugar de enviar el formulario, ejecuta la busqueda con lo que tenga
 * @param {bind?} e
 */
$("input").keypress(function(e){
    if ( e.which === 13 ) {
        var filtraje = filtro();
        procesarDatos("/directorio/busqueda", filtraje, tabularUsuarios, e);
        e.preventDefault();
    } 
});

/**
 * Una vez ha presionado la tecla más de tres dos, ejecuta la busqueda
 * @param {type} e
 */
$("input").keyup(function(e){
    verificaVacio(e);
    verificaEspacio($("#uid").val(), e);
    if (!( e.which === 0)){
   	    if ($.directorioControl.pulsaciones  >= 2) {
   	        var filtraje = filtro();
            procesarDatos("/directorio/busqueda", filtraje, tabularUsuarios, e);
   	        $.directorioControl.pulsaciones = 0;
   	    }else{
   	        $.directorioControl.pulsaciones++;
   	    }
    }
});

