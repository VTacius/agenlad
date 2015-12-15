$(document).ready(function(){
    $("#cargador").hide();
    $("#cargadorResponse").hide();
    $("#regresarInicio").hide();
    $("#creacion").hide();
});

/**
 * Llenamos todos los controles con los datos que nos ha devuelto sobre el usuario el servidor
 * @param {array} data
 * @returns {undefined}
 */
var llenarControles = function(data){
    /* Empiezo formulario con datos de usuario */
    var source = $('#usermod-template').html();
    var template = Handlebars.compile(source);
    var contenido = template(data);
    $('#usermodv').html(contenido);

    /* El atributo o (localidad) necesita nuestro desfalco para que se comporte como un select sin serlo, y acá esta como */
    if (!(isEmpty(data.datos.o))){
        $('#o').val(data.datos.o.label);
        $('#o').attr('data-o', data.datos.o.id);
        ouAutocomplementar(data.datos.o.id);
    }else{
        $('#o').val('');
    }
    /* Le agregamos la función de autocomplementar */ 
    oAutocomplementar();
    
    /* Por ahora dejaré la selección de controles en este parte, de hecho, creo que ya no es opción de la parte de vista,
        quizá del comportamiento de esta, así que igual y se esta bien por acá*/    
    $("#grupouser option:contains('" + data.datos.grupouser + "')").attr('selected','selected');
    $(data.datos.gruposuser).each(function(index, elemento){
        $("#grupos option:contains('" + elemento + "')").attr('selected','selected');
    });

    console.log(data.datos);
    if ( !isEmpty(data.datos.cuentastatus) ) {
        $("#mailModForm").show();
        $(".switch-toggle input").change(zimbraUserMod);
        if (data.datos.buzonstatus !== "enabled"){
            $("#buzon #apagadob").prop('checked', true);
            if (data.datos.buzonstatus !== "disabled"){
                $("#buzon [for=apagadob]").text(data.datos.buzonstatus);
            }
        }
        if (data.datos.cuentastatus  !== "active"){
            $("#cuenta #apagadoc").prop('checked', true);
            if (data.datos.cuentastatus !== "locked"){
                $("#cuenta [for=apagadoc]").text(data.datos.cuentastatus);
            }
        }
        $("#mailModForm #usermod").text(data.datos.usermod);
    } 
    
    $("#userModForm #enviar").click(enviarUserMod);
     
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
        $("#busqueda").hide();
        data.datos["correo"] = (data.datos.mailuser === "{empty}") ? 0 : 1;
        llenarControles(data);
    } 
};

/**
 * Se limita a mostrar la respuesta que el servidor regresa después de modificar usuarios
 * de modificar datos
 * @param {array} data
 * @returns {undefined}
 */
var mostrarDatosModificar = (function(data){
    pmostrarError(data);
    pmostrarMensaje(data);
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

/* Acciones de botones */
/**
 * Cuando se hace click en los botones de Estado de Buzon, se envia la peticion
 * para cambio de estado
 * @param {object} e
 */
var zimbraUserMod = function(e) {
    var idObjeto = $(this).prop('id');
    var texto = $('[for="' + idObjeto + '"').text();
    var datos = {
        textElemento: texto,
        idElemento: $(this).parent().parent().prop('id'),
        usermod : $("#usermod").text()
    };
    procesarDatos('/usermod/zimbra', datos, mostrarModificarZimbra );
};

/**
 * Una vez el formulario ha sido cargado con datos nos arrepentimos y no hacemos nada  
 * Se parece a regresarInicio
 * @param {object} e
 */
var resetUserMod = function(e){
    $("form").hide();
    $("input").val("");
    $("#busqueda").show();
};

/**
 * Enviamos los datos del formulario para modificacion del usuario
 * @param {object} e
 */
var enviarUserMod = function(e){
    e.stopPropagation(); 
    e.preventDefault();
    var datos = recogerDatos();
    procesarDatos('/usermod/cambio', datos, mostrarDatosModificar);
};


/* Asociación a controles */

/**
 * Enviamos el nombre de usuario que queremos modificar para que obtenga los 
 * datos del mismo y luego los muestre
 * @param {object} e
 */
$("#busqueda #enviar").click(function(e){
    datos = {
        usuarioModificar: $("#usuarioModificar").val()
    };
    procesarDatos('/usermod/envio', datos, mostrarDatosBusqueda, this);
    e.stopPropagation(); 
    e.preventDefault();
});

