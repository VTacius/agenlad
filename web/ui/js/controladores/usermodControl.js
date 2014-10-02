$(document).ready(function(){
    $("form").hide();
    $("#busqueda").show();
    $("#cargador").hide();
});

$('.btn-toggle').click(function(e) {
    $("#cargador").show();
    $(this).children('.btn').toggleClass('active btn-primary btn-default');  
    var datos = {
        textElemento: $(this).children(".active").text(),
        idElemento: $(this).attr('id'),
        usermod : $("#usermod").text()
    };
    modificarZimbra(datos);
    e.stopPropagation(); 
    e.preventDefault();
});

$("#userModForm #reset").click(function(e){
    e.stopPropagation(); 
    e.preventDefault();
    $("input").val("");
    crearSelectOption();
    $("form").hide();
    $("#busqueda").show();
    
});

$("#busqueda #enviar").click(function(e){
    e.stopPropagation(); 
    e.preventDefault();
    datos = {
            usuarioModificar: $("#usuarioModificar").val()
        };
    procesarDatos('/usermod/envio', datos, mostrarDatosBusqueda);
    $("#busqueda").hide();
    $("#mailModForm").show();
    $("#userModForm").show();
});

/**
 * Crea la consulta para buscar por los datos del usuario
 * @returns {undefined}
 */
//var buscarUsuario = function(){
//    $.ajax({
//        type: 'POST',
//        url: '/usermod/envio',
//        dataType: 'json',
//        data: {
//            usuarioModificar: $("#usuarioModificar").val()
//        },
//        success: mostrarDatosBusqueda,
//        error: errorOnResponse
//        
//    });
//};

$("#userModForm #enviar").click(function(e){
    e.stopPropagation(); 
    e.preventDefault();
    modificarUsuario();
});

$("#regresarInicio").click(function(e){
    e.stopPropagation(); 
    e.preventDefault();
    $("form").hide();
    $("#busqueda").show();
    $("input").val("");
    crearSelectOption();
});


/**
 * Crea ambas listas de selección con el resultado devuelto desde el servidor
 * @param {array} lista
 * @returns {undefined}
 */
var crearSelectOption = function(lista){
    $("#grupouser option").remove();
    $("#grupos option").remove();
    //TODO: Aunque no parece tirar error, ordenar este código para usarlo sin parametro lista
    $(lista).each(function(index, elemento){
        var valueGidNumber = "<option value=" + elemento.gidNumber + ">" + elemento.cn + "</option>";
        var valueCn = "<option value=" + elemento.cn + ">" + elemento.cn + "</option>";
        $("#grupouser").append(valueGidNumber);
        $("#grupos").append(valueCn);
    });
    
};

/**
 * Recogemos los datos del formulario.
 * Prestar atención a la forma en que lo hacemos para grupos y grupouser
 * @returns {obtenerDatos.contenido}
 */
var obtenerDatos = function(){
    var contenido = {
        phone : $("#phone").val(),
        cargo : $("#cargo").val(),
        oficina : $("#oficina").val(),
        usermod : $("#usermod").text(),
        nameuser : $("#nameuser").val(),
        apelluser : $("#apelluser").val(),
        grupouser : $("#grupouser option:selected").val(),
        localidad : $("#localidad").val(),
        "grupos[]" : $("#grupos").val()
    };
    return contenido;
};

var llenarControl = function(data, objeto){
    $("#" + objeto).val(data[objeto]);
};

/**
 * Llenamos el formulario de los datos actuales del usuario para que sean 
 * considerados para su modificacion
 * @param {array} data
 * @returns {undefined}
 */
var mostrarDatosBusqueda = function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
    console.log(data);
    $("b#usermod").text(data.usermod);
    elementos = ['cargo', 'oficina', 'nameuser', 'phone', 'apelluser', 'localidad'];
    $(elementos).each(function(item, elemento){
        llenarControl(data.datos, elemento);
    });
    crearSelectOption(data.datos.grupos);
    
    $("#grupouser option:contains('" + data.datos.grupouser + "')").attr('selected','selected');
    $(data.datos.gruposuser).each(function(index, elemento){
        $("#grupos option:contains('" + elemento + "')").attr('selected','selected');
    });
    
    if (data.datos.cuentastatus === "active"){
        $("#cuenta #apagado").text("Locked");
    }else{
        $("#cuenta #apagado").text(data.cuentastatus);
        // Dado que por defecto, twig lo envia como el desactivado
        $("#cuenta *").children().toggleClass('active btn-primary btn-default');
    }
    
    if (data.datos.buzonstatus === "enabled"){
        $("#buzon #apagado").text("Disabled");
    }else{
        $("#buzon #apagado").text(data.buzonstatus);
        // Dado que por defecto, twig lo envia como el desactivado
        $("#buzon *").children().toggleClass('active btn-primary btn-default');
    }

    
};



/**
 * Muestra los datos después con la respuesta que el servidor envía después
 * de modificar datos
 * @param {array} data
 * @returns {undefined}
 */
var mostrarDatosModificar = (function(data){
    mostrarErrorConexion(data);
    contenido = "";
    $(data.datos).each(function(index, elemento){
        contenido += elemento + "<br>";
    });
    $("#resultadoDiv").html(contenido);
    $("form").hide();
    $("#resultadoForm").show();
});

/**
 * Crea la consulta para cambiar los datos del usuario
 * @returns {undefined}
 */
var modificarUsuario = function(){
    var datos = obtenerDatos();
    $.ajax({
        type: 'POST',
        url: '/usermod/cambio',
        dataType: 'json',
        data: datos,
        success: mostrarDatosModificar,
        error: errorOnResponse
    });
};

var mostrarModificarZimbra = function(data){
    console.log(data);
    mostrarErrorConexion(data);
    $("#cargador").hide();
};

var modificarZimbra = function(datos){
    $.ajax({
        type: 'POST',
        url: '/usermod/zimbra',
        dataType: 'json',
        data: datos,
        success: mostrarModificarZimbra,
        error: errorOnResponse
    });
};