$(document).ready(function(){
    $("dl").hide();
    $("#admon a").hide();
});

$("#enviar").click(function(e){
    e.stopPropagation(); 
    e.preventDefault();
    var datos =  {
        usuarioCliente: $("#usuarioCliente").val()
    };
    procesarDatos('/usershow/datos', datos, mostrarDatos);
//    $("#usermodTecnico").show();
});

/**
 * Dato el id de un elemento dd|dt, revisa si tiene datos para ver si los muestra
 * en pantalla
 * @param {object} datos
 * @param {string} objeto
 * @returns {undefined}
 */
function llenarControl(datos, objeto) {
    if (datos[objeto] === "{empty}") {
        $("#" + objeto).hide();
        $("dd#" + objeto).text("");
    }else{
        $("#" + objeto).show();
        $("dd#" + objeto).text(datos[objeto]);
    }
}

function llenarControlUbicacion(datos){
    llenarControl(datos.localidad);
    llenarControl(datos.oficina);
    if (datos.localidad === "{empty}" && datos.oficina === "{empty}") {
        console.log(datos.oficina);
        console.log(datos.localidad);
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
        $("#creacion").hide() 
    }
    if (estados.modificacion) {
        $("#modificacion")
                .show()
                .attr( 'href', "/usermod/" + usuario ); 
    }else{
        $("#modificacion").hide()
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
    elementos = ["psswduser", "nombrecompleto","psswduser","grupouser","mailuser","buzonstatus","cuentastatus"];
    $(elementos).each(function(i, e){
        llenarControl(data.datos, e);
    });
    llenarControlUbicacion(data.datos);
    mostrarEnlaces(data.datos.usermod, data.datos.enlaces);
     
    if (!(data.nameuser==="{empty}" && data.buzonstatus==="{empty}")){
        $("dl").show();
    }
};
