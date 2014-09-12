$(document).ready(function() {
    // Suponemos la mejor forma para variables locales al usar JQuery
    $.indexControl = new Object();
    $.indexControl.validacion = false;
    // Asegurarnos que las instrucciones estén ocultas
    $("#pswd_info").hide();
});


$('#passchangeprima').keyup(function(){
    $("#pswd_info").show();
    validar($("#passchangeprima").val());
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

$('#enviar').click(function(e){
    e.stopPropagation(); 
    e.preventDefault();
    if ($.indexControl.validacion){
        console.log("Parece que si la lee acá, ahora es True");
    } else {
        console.log("Parece que si la lee acá, ahora es False");
    }
});

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


