$(document).ready(function(){
    $("dl").hide();
    $("#admon a").hide();
    var filtraje = {uid: 'usuario*'};
    busqueda(filtraje);
});

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
 * Dato el id de un elemento dd|dt, revisa si tiene datos para ver si los muestra
 * en pantalla
 * @param {object} datos
 * @param {string} objeto
 * @returns {undefined}
 */
function llenarControl(datos, objeto) {
    console.log(datos[objeto] );
    if (datos[objeto] === "{empty}") {
        $("dd#" + objeto).text("-");
    }else{
        $("#" + objeto).show();
        $("dd#" + objeto).text(datos[objeto]);
    }
}

function llenarControlUbicacion(datos){
    llenarControl(datos.localidad);
    llenarControl(datos.oficina);
    if (datos.localidad === "{empty}" && datos.oficina === "{empty}") {
        $("*#ubicacion").hide();
        $("b#oficina").text("");
        $("b#localidad").text("");
    }else{
        $("*#ubicacion").show();
        $("#oficina").text(datos.oficina);
        $("#localidad").text(datos.localidad);
    }
}

/**
 * Muestra los enlaces para administrador según lo devuelto por el usuario en cuestión
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
 * Llena la pantalla del usuario con los datos obtenidos del servidor
 * @param {json} data
 * @returns {undefined}
 */
var mostrarDatos = function(data){
    console.log(data);
    pmostrarError(data);
    pmostrarMensaje(data);
    
    // Llenamos los datos
    elementos = ["psswduser", "nombrecompleto","psswduser","grupouser","mailuser","buzonstatus","cuentastatus", 'pregunta', 'respuesta', 'fecha'];
    $(elementos).each(function(i, e){
        llenarControl(data.datos, e);
    });
    llenarControlUbicacion(data.datos);
    mostrarEnlaces(data.datos.usermod, data.datos.enlaces);
     
    if (!(data.nameuser==="{empty}" && data.buzonstatus==="{empty}")){
        $("dl").show();
    }
};

var acciones = function(usuario){
    var acciones = '<td>\n\
        <button type="button" id="editar_usuario" name="editar_usuario" class="btn btn-xs alert-success" value="'+usuario+'">\n\
            <span class="glyphicon glyphicon-edit"></span>Editar\n\
        </button>\n\
        <button type="button" id="borrar_usuario" name="borrar_usuario" class="btn btn-default btn-xs alert-danger" value="'+usuario+'">\n\
            <span class="glyphicon glyphicon-remove"></span>Eliminar\n\
        </button></td>';
    return acciones;
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
    $("#usuarioslst tr").remove();
    $(result.datos).each(
        function(item, elemento){
            $("#usuarioslst").append("<tr>" + elementoAttr(elemento.cn) +  elementoAttr(elemento.mail) + elementoAttr(elemento.title) + acciones(elemento.mail) + "</tr>");
        });
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
