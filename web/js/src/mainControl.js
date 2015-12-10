$(document).ready(function() {
    // Suponemos la mejor forma para variables locales al usar JQuery
    $.indexControl = {};
    $.indexControl.validacion = false;
    // Asegurarnos que las instrucciones y alertas estén ocultas
    $("#pswd_info").hide();
    $("input").val("");
});

$('#passchangeprima').keyup(function(){
    $("#pswd_info").show();
    $("#msgadvertencia").hide();
    validar($("#passchangeprima").val());
});

$('#passchangeprima').focusin(function(){
    $("#msgadvertencia").hide();
});

$('#passchangeprima').blur(function(e){
    e.stopPropagation(); 
    if (!$.indexControl.validacion){
        $("#passchangeprima").focus();
    }
});

$('#passchangeconfirm').focus(function(e){
    e.stopPropagation();
    if ($.indexControl.validacion){
         $("#pswd_info").hide();
    }
});

$('#passchangeconfirm').focusin(function(){
    $("#msgadvertencia").hide();
});

$('#enviar').click(function(e){
    e.stopPropagation(); 
    e.preventDefault();
    if (comprobarPassword()){
        procesarDatos();
    } 
});

/**
 * Comprueba 
 * passchangeprima cumple los requisitos
 * passchangeprima y confirmacion no están vacías, aunque esto es redundante
 * passchangeprima y confirmacion son iguales
 * @returns {Boolean}
 */
var comprobarPassword = function(){
    var password = $("#passchangeprima").val();
    var confirmacion = $("#passchangeconfirm").val();
    if ($.indexControl.validacion){
        if (password === confirmacion){
            return true;
        }else if (password === "" || confirmacion=== ""){
            $("#msgadvertencia").show();
            $("#msgadvertencia").addClass("alert-danger");
            $("#advertencia").text("La confirmación esta vacía");
            return false;
        }else{
            $("#msgadvertencia").show();
            $("#msgadvertencia").addClass("alert-danger");
            $("#advertencia").text("La confirmación no coincide");
            return false;
        }
    }else{
        $("#msgadvertencia").show();
        $("#msgadvertencia").addClass("alert-danger");
        $("#advertencia").text("Por favor, introduzca valores válidos");
        return false;
    }
};

/**
 * Auxiliar de validar: ¿Cumple texto con regex? 
 * Y maneja el estado del objeto HTML asociado
 * @param {String} texto
 * @param {html} objeto
 * @param {regex-expresion} regex
 * @returns {Boolean}
 */
var confirmar = function(texto, objeto, regex){
    if (texto.match(regex)) {
        $(objeto).removeClass('invalid').addClass('valid');
        return true;
    } else {
        $(objeto).removeClass('valid').addClass('invalid');
        return false;
    }
};

var mostrarResultado = function(data){
    mostrarErrorConexion(data);
    pmostrarError(data);
    pmostrarMensaje(data);
    if(isEmpty(data.error)){
        setTimeout(function() {
            document.location.reload(true);
        }, 3500);
    }
};

/**
 * 
 * @returns {undefined}
 */
var procesarDatos = function () {
    $.ajax({ 
        type: 'POST',
        url: '/main/cambio',
        dataType: "json",
        data: {
            passchangeprima: $("#passchangeprima").val(),
            passchangeconfirm: $("#passchangeconfirm").val()
        },
        success: mostrarResultado,
        error: errorOnResponse     
    });
};

/**
 * Usamos confirmar para verificar que la cadena cumple los requisitos
 * @param {string} confirma
 * @returns {undefined}
 */
var validar = function(confirma) {  
    var resultadolength = confirmar(confirma, '#length', /(.){8,}/);
    var resultadocapital = confirmar(confirma, '#capital', /(.*[A-Z]){1,10}/);
    var resultadonumber = confirmar(confirma, '#number', /(.*[0-9]){1,10}/);
    var resultadochar = confirmar(confirma, '#char', /(.*[\.|_|@|&|\+|!|\$|\*]){1,10}/ );
    $.indexControl.validacion = resultadolength && resultadocapital && resultadonumber && resultadochar;
};


