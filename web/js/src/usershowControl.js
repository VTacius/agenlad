$(document).ready(function(){
    var filtraje = {uid: '*'};
    procesarDatos('/directorio/busqueda', filtraje, mostrar);
    $.directorioControl = new Object();
    $.directorioControl.pulsaciones = 0;
    var campos = {uid: "#usuarioCliente"};
    $.directorioControl.campos = campos;
});

/**
 * Creamos la tabla con algunos usuarios, tabla que si bien incluye hasta unos botones bien bonitos 
 * por el momento más bien están nada más como un adorno
 * @param {array} respuesta
 * @returns {undefined}
 */
var mostrar = function(respuesta){
    var source = $('#usuarioslst-template').html();
    var template = Handlebars.compile(source);
    var contenido = template(respuesta);
    
    pmostrarError(respuesta);
    pmostrarMensaje(respuesta);
    
    $("#usuarioslst tr").remove();
    $("#usuarioslst").append(contenido);
};

/**
 * Llena la pantalla del usuario con los datos obtenidos del servidor al buscar el usuario 
 * con los datos
 * @param {json} respuesta
 * @returns {undefined}
 */
var mostrarDatos = function(respuesta){
    if (!(respuesta.datos.nameuser==="{empty}" && respuesta.datos.buzonstatus==="{empty}")){
        // Llenamos los datos
        var source = $('#respuestaUsuario-template').html();
        var template = Handlebars.compile(source);
        var contenido = template(respuesta);

        pmostrarError(respuesta);
        pmostrarMensaje(respuesta);

        $("#respuestaUsuario dl").remove();
        $("#respuestaUsuario div").remove();
        $("#respuestaUsuario").append(contenido);
    } else{
        $("#respuestaUsuario dl").remove();
        $("#respuestaUsuario div").remove();
        pmostrarError(respuesta);
        pmostrarMensaje(respuesta);
    }

};

/**
 * Forma los datos a ser enviados al servidor como filtro
 * Cuidamos de enviar un valor por defecto en forma de comodín cuando no haya valores
 * @type type
 */
var filtro = function(){
    var filtro = {};
    $.each($.directorioControl.campos, function(campo, control){
        var contenido = $(control).val();
        var prefiltro = isEmpty(contenido) ? '*' : contenido + '*';
        filtro[campo] = prefiltro;
    });
    return filtro;
};

/**
 * Realizamos la busqueda en dos ocasiones:
 * + Hemos hecho retroceso hasta dejar vacío el control
 * + Verificamos que no estemos hablando de {{keyCode==0}}, en ese caso verificamos que 
 *   hayamos dado ya dos pulsaciones (Esto es, teniendo un mínimo de dos caracteres)
 *   ejecutamos la búsqueda
 * @param {type} e
 */
$("#usuarioCliente").keyup(function(e){
    var contenido = $($.directorioControl.campos.uid).val();
    if (isEmpty(contenido)){
        var filtraje = filtro();
        procesarDatos("/directorio/busqueda", filtraje, mostrar, e);
   	    $.directorioControl.pulsaciones = 0;
        $("#respuestaUsuario dl").remove();
        $("#respuestaUsuario div").remove();
    }else if (!( e.which === 0)){
   	    if ($.directorioControl.pulsaciones  >= 2) {
   	        var filtraje = filtro();
            procesarDatos("/directorio/busqueda", filtraje, mostrar, e);
   	        $.directorioControl.pulsaciones = 0;
   	    }else{
   	        $.directorioControl.pulsaciones++;
   	    }
    }
});

/**
 * Configuramos la acción de buscar los datos de tal usuario con Contraseña!
 */
$("#enviar").click(function(e){
    e.stopPropagation(); 
    e.preventDefault();
    var datos =  {
        usuarioCliente: $("#usuarioCliente").val()
    };
    procesarDatos('/usershow/datos', datos, mostrarDatos);
    $("#usermodTecnico").show();
});

