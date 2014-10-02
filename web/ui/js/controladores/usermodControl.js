$(document).ready(function(){
    $("form").hide();
    $("#busqueda").show();
    $("#cargador").hide();
    $("#espera").hide();
    $("#cargadorResponse").hide();
    $("#regresarInicio").hide();
    $("#creacion").hide();
});

/**
 * Cuando se hace click en los botones de Estado de Buzon, se envia la peticion
 * para cambio de estado
 * @param {object} e
 */
$('.btn-toggle').click(function(e) {
    $("#cargador").show();
    $(this).children('.btn').toggleClass('active btn-primary btn-default');  
    var datos = {
        textElemento: $(this).children(".active").text(),
        idElemento: $(this).attr('id'),
        usermod : $("#usermod").text()
    };
    procesarDatos('/usermod/zimbra', datos, mostrarModificarZimbra );
    e.stopPropagation(); 
    e.preventDefault();
});

/**
 * Una vez el formulario ha sido cargado con datos
 * nos arrepentimos y no hacemos nada  
 * Se parece a regresarInicio
 * @param {object} e
 */
$("#userModForm #reset").click(function(e){
    $("form").hide();
    $("input").val("");
    $("#busqueda").show();
    crearSelectOption();
    e.stopPropagation(); 
    e.preventDefault();
    
});

/**
 * Enviamos los datos del formulario para modificacion del usuario
 * @param {object} e
 */
$("#userModForm #enviar").click(function(e){
    $("#espera").show();
    var datos = obtenerDatos();
    console.log(datos);
    procesarDatos('/usermod/cambio', datos, mostrarDatosModificar);
    e.stopPropagation(); 
    e.preventDefault();
});

/**
 * Enviamos el nombre de usuario que queremos modificar para que obtenga los 
 * datos del mismo y luego los muestre
 * @param {object} e
 */
$("#busqueda #enviar").click(function(e){
    datos = {
            usuarioModificar: $("#usuarioModificar").val()
        };
    procesarDatos('/usermod/envio', datos, mostrarDatosBusqueda);
    e.stopPropagation(); 
    e.preventDefault();
});

/**
 * Una vez ha finalizado, da la opcion de volver a configurar otro usuario
 * Se parece a userModForm
 * @param {object} e
 */
$("#regresarInicio").click(function(e){
    $("form").hide();
    $("input").val("");
    $("#busqueda").show();
    crearSelectOption();
    e.stopPropagation(); 
    e.preventDefault();
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
        var valueCn = "<option value=\"" + elemento.cn + "\">" + elemento.cn + "</option>";
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
        localidad : $("#localidad").val(),
        grupouser : $("#grupouser option:selected").val(),
        grupos : $("#grupos").val() || []
    };
    return contenido;
};

/**
 * Usado por mostrarDatosBusqueda
 * @param {array} data
 * @param {string} objeto
 * @returns {undefined}
 */
var llenarControl = function(data, objeto){
    $("#" + objeto).val(data[objeto]);
};

/**
 * Llenemos todos los controles
 * @param {array} data
 * @returns {undefined}
 */
var llenarControles = function(data){
    $("b#usermod").text(data.datos.usermod);
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
 * Llenamos el formulario de los datos actuales del usuario para que sean 
 * considerados para su modificacion
 * @param {array} data
 * @returns {undefined}
 */
var mostrarDatosBusqueda = function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
    
    if (data.datos.enlaces.modificacion) {
        llenarControles(data);
        $("#busqueda").hide();
        $("#userModForm").show();
        if (!(data.datos.mailuser === "{empty}")) {
            $("#mailModForm").show();
            
        }
    }else if(data.datos.enlaces.creacion){
        $("#creacion").show();
        $("a#creacion")
            .show()
            .attr( 'href', "/useradd/" + $("#usuarioModificar").val() );
    }
    
};

/**
 * Muestra los datos después con la respuesta que el servidor envía después
 * de modificar datos
 * @param {array} data
 * @returns {undefined}
 */
var mostrarDatosModificar = (function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
    $('html, body').animate({scrollTop : 0},800);
    $("#espera").hide();
});

/**
 * Cambia el estado de los botones de estado de buzón después de realizar la 
 * peticion
 * @param {array} data
 * @returns {undefined}
 */
var mostrarModificarZimbra = function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
    $("#cargador").hide();
};