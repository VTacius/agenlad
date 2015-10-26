$(document).ready(function(){
    $("dl").hide();
    $("#admon a").hide();
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
    var template = $('#usuarioslst-template').html();
    Mustache.parse(template);
    var contenido = Mustache.render(template, respuesta);
    pmostrarError(respuesta);
    pmostrarMensaje(respuesta);
    $("#usuarioslst tr").remove();
    $("#usuarioslst").append(contenido);
};

/**
 * Llena la pantalla del usuario con los datos obtenidos del servidor al buscar el usuario 
 * con los datos
 * @param {json} data
 * @returns {undefined}
 */
var mostrarDatos = function(data){
    // Llenamos los datos
    elementos = ["psswduser", "nombrecompleto","psswduser","grupouser","mailuser","buzonstatus","cuentastatus", 'pregunta', 'respuesta', 'fecha'];
    $(elementos).each(function(i, e){
        console.log(e);
        llenarControl(data.datos, e);
    });
    llenarControlUbicacion(data.datos);
    mostrarEnlaces(data.datos.usermod, data.datos.enlaces);
     
    if (!(data.nameuser==="{empty}" && data.buzonstatus==="{empty}")){
        $("dl").show();
    }
};

/** * Muestra los enlaces para administrador según lo devuelto por el usuario en cuestión
 * @param {string} usuario
 * @param {array} estados
 * @returns {undefined}
 */
var mostrarEnlaces = function(usuario, estados){
    if (estados.creacion) {
        $("#creacion")
                .show()
                .attr( 'href', "/useradd/" + $("#usuarioCliente").val() );     
    }else{
        $("#creacion").hide();
    }
    if (estados.modificacion) {
        $("#modificacion")
                .show()
                .attr( 'href', "/usermod/" + usuario ); 
    }else{
        $("#modificacion").hide();
    }
};

/**
 * Dato el id de un elemento dd|dt, revisa si tiene datos para ver si los muestra
 * en pantalla
 * @param {object} datos
 * @param {string} objeto
 * @returns {undefined}
 */
function llenarControl(datos, objeto) {
    console.log(datos[objeto]);
    if (datos[objeto] === "{empty}") {
        $("dd#" + objeto).text("-");
    }else{
        $("#" + objeto).show();
        $("dd#" + objeto).text(datos[objeto]);
    }
}

/*
 * Los controles para ubicacion y oficina los llenamos despues
 **/
function llenarControlUbicacion(datos){
    if (isEmpty(datos.localidad)  && isEmpty(datos.oficina)) {
        $("*#ubicacion").hide();
        $("b#oficina").text("");
        $("b#localidad").text("");
    }else{
        $("*#ubicacion").show();
        $("#oficina").text(datos.oficina);
        if (!isEmpty(datos.localidad)){
            $("#localidad").text(datos.localidad.label);
        }else{
            $("#localidad").text("No disponible");
        }
    }
}
