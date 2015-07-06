$(document).ready(function(){
    var filtraje = filtro();
    busqueda(filtraje);
    $.directorioControl = new Object();
    $.directorioControl.pulsaciones = 0;
});

/**
 * Si presiona enter, en lugar de enviar el formulario, ejecuta la busqueda con lo que tenga
 * @param {bind?} e
 */
$("input").keypress(function(e){
    if ( e.which === 13 ) {
        var filtraje = filtro();
        busqueda(filtraje);
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
   	     busqueda(filtraje);
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
        busqueda(filtraje);
    }
};

/**
 * Realiza una consulta cuando se ingresa un espacio, suponemos que es posible 
 * que el nombre de alguien se parezca al usuario de muchos
 * TODO: Esto debería estar en el keyup del #uid, pero no sé que tal se llevara 
 * con dos metodos enlazados al mismo evento
 * @param {String} valor
 * @returns {undefined}
 */
var verificaEspacio = function(valor){
    if (valor.indexOf(" ")!==-1){
        var filtraje = filtro();
        busqueda(filtraje);
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
        'o': preFiltro($("#o").val()),
        'ou': preFiltro($("#ou").val()),
        'uid': preFiltro($("#uid").val())
    };
    return filtro;
};

var busqueda = function(filtro){
    $.ajax({
        url: "/directorio/busqueda",
        type: "POST",
        data: filtro,
        dataType: 'json',
        success: mostrar,
        error: function(data){
            console.log("Algo malo ha sucedido");
            console.log(data.responseText);
        }
    });
};

/**
 * Para un string vacío, devuelve un -
 * @param {String} attr
 * @returns {String}
 */
var elementoAttr = function(attr){
    if (!isEmpty(attr)) {
        return "<td>" + attr + "</td>";
    }else{
        return "<td> - </td>";
    }
    
};

/**
 * Sea cual sea la informacion que reciba, la muestra en pantalla
 * @param {array} result
 * @returns {undefined}
 */
var mostrar = function(result){
    pmostrarError(result);
    pmostrarMensaje(result);
    $("#respuesta tr").remove();
    $(result.datos).each(
        function(item, elemento){
            $("#respuesta").append("<tr>" + elementoAttr(elemento.cn) +  elementoAttr(elemento.mail) + elementoAttr(elemento.title) + elementoAttr(elemento.telephoneNumber) + "</tr>");
        });
};

