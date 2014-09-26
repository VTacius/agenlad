$(document).ready(function() {
    // Suponemos la mejor forma para variables locales al usar JQuery
    $.indexControl = new Object();
    $.indexControl.validacion = false;
    // Asegurarnos que las instrucciones estén ocultas
    $("#pswd_info").hide();
    $("#msgadvertencia").hide();
});


$('#passchangeprima').keyup(function(){
    $("#pswd_info").show();
    $("#msgadvertencia").hide();
    validar($("#passchangeprima").val());
});


$('#passchangeprima').focusin(function(){
    $("#msgadvertencia").hide();
    console.log("En este momento debería desaparecer");
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
    } else {
        console.log("Parece que si la lee acá, ahora es False");
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
            $("#advertencia").text("La confirmación esta vacía");
            return false;
        }else{
            $("#msgadvertencia").show();
            $("#advertencia").text("Las confirmación no coincide");
            return false;
        }
    }else{
        $("#msgadvertencia").show();
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

/**
 * 
 * @returns {undefined}
 */
var procesarDatos = function () {
    $.ajax({ 
        type: 'POST',
        url: '/main/cambio',
        data: {
            passchangeprima: $("#passchangeprima").val(),
            passchangeconfirm: $("#passchangeconfirm").val()
        },
        success: function(data){
            console.log(data);
            $("#msgadvertencia").show();
            $("#advertencia").text(data);
//            $("#passchangeprima").prop('disabled', true);
//            $("#passchangeconfirm").prop('disabled', true);
//            if (!data.errorLdap==NULL){
//                setTimeout(
//                    function() {
//                        document.location.reload(true);
//                    }, 3500);
//            }
        },
        error: function(){
            $("#msgadvertencia").show();
            $("#advertencia").text("El procedimiento ha fallado por alguna razón");
        }
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


