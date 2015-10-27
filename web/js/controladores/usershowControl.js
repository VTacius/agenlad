$(document).ready(function(){
    var filtraje = {uid: '*'};
    procesarDatos('/directorio/busqueda', filtraje, mostrar);
});

/**
    Configuramos la acción de buscar los datos de tal usuario con Contraseña!
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
    // Llenamos los datos
    var source = $('#respuestaUsuario-template').html();
    var template = Handlebars.compile(source);
    var contenido = template(respuesta);

    pmostrarError(respuesta);
    pmostrarMensaje(respuesta);

    $("#respuestaUsuario dl").remove();
    $("#respuestaUsuario div").remove();
    $("#respuestaUsuario").append(contenido);
    
    /*
    if (!(data.nameuser==="{empty}" && data.buzonstatus==="{empty}")){
        $("dl").show();
    }*/

};

