$(document).ready(function(){
    $("form").hide();
    $("#busqueda").show();
});

$('.btn-toggle').click(function(e) {
    $(this).children('.btn').toggleClass('active btn-primary btn-default');  
    console.log($(this).children(".active").text());
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
    buscarUsuario();
    $("#busqueda").hide();
    $("#mailModForm").show();
    $("#userModForm").show();
});

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

/**
 * Llenamos el formulario de los datos actuales del usuario para que sean 
 * considerados para su modificacion
 * @param {array} data
 * @returns {undefined}
 */
var mostrarDatosBusqueda = function(data){
    mostrarErrorConexion(data);
    $("b#usermod").text(data.usermod);
    $("#cargo").val(data.cargo);
    $("#oficina").val(data.oficina);
    $("#nameuser").val(data.nameuser);
    $("#phone").val(data.telefono);
    $("#apelluser").val(data.apelluser);
    $("#localidad").val(data.localidad);
    crearSelectOption(data.grupos);
    $("#grupouser option:contains('" + data.grupouser + "')").attr('selected','selected');
    $(data.gruposuser).each(function(index, elemento){
        $("#grupos option:contains('" + elemento + "')").attr('selected','selected');
    });
    
    if (data.cuentastatus == "active"){
        $("#cuenta #apagado").text("Inhabilitar");
    }else{
        $("#cuenta #apagado").text(data.cuentastatus);
    }
    
    if (data.buzonstatus === "enabled"){
        $("#buzon #apagado").text("Inhabilitar");
    }else{
        $("#buzon #apagado").text(data.cuentastatus);
    }

    
};

/**
 * Crea la consulta para buscar por los datos del usuario
 * @returns {undefined}
 */
var buscarUsuario = function(){
    $.ajax({
        type: 'POST',
        url: '/usermod/envio',
        dataType: 'json',
        data: {
            usuarioModificar: $("#usuarioModificar").val()
        },
        success: mostrarDatosBusqueda,
        error: errorOnResponse
        
    });
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
