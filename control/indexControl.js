$(function() {
  $(document).ready(function() {
    $("#pswd_info").hide();
    $("#advertencia").hide();
    $("#passchangeprima").val("");
    $("#passchangeconfirm").text("");
    $("#cerrarventana").hide();
  });

  var resultado = false;

  $("#passchangeconfirm").focus(function() {
    // Evita que no se pueda acceder al segundo boton si el primero no pasa la prueba de
    // complejidad
    if (resultado === false ) {
      $("#passchangeprima").focus();
    }
  });
  
  var comprobarContra = function() {
    // ¿Son iguales ambas contraseñas?
    var password = $("#passchangeprima").val();
    var confirma = $("#passchangeconfirm").val();
    verifica(password);
    if (password !== confirma) {
      $("#passchangeprima").val("");
      $("#passchangeconfirm").val("");
      $("#passchangeprima").focus();
      return false;
    } else {
      $("#advertencia").hide();
      return true;
    }
  };

  $("#passchangeconfirm").focusout(function() {
    if ($("#passchangeconfirm").val() !== "") {
     // comprobarContra();
    }
  });

  var verifica = function(confirma) {
    if (confirma.length < 8) {
      $('#length').removeClass('valid').addClass('invalid');
      var resultadolength = false;
    } else {
      var resultadolength = true;
      $('#length').removeClass('invalid').addClass('valid');
    }
    if (confirma.match(/(.*[A-Z]){1,10}/)) {
      var resultadocapital = true;
      $('#capital').removeClass('invalid').addClass('valid');
    } else {
      $('#capital').removeClass('valid').addClass('invalid');
      var resultadocapital = false;
    }
    if (confirma.match(/(.*[0-9]){1,10}/)) {
      var resultadonumber = true;
      $('#number').removeClass('invalid').addClass('valid');
    } else {
      $('#number').removeClass('valid').addClass('invalid');
      var resultadonumber = false;
    }
    if (confirma.match(/(.*[.|_|@|&|\+|!|\$|\*]){1,10}/)) {
      var resultadochar = true;
      $('#char').removeClass('invalid').addClass('valid');
    } else {
      $('#char').removeClass('valid').addClass('invalid');
      var resultadochar = false;
    }
    if (resultadolength && resultadocapital && resultadonumber && resultadochar) {
      resultado = true;
    } else {
      resultado = false;
    }
  };

  $("#passchangeprima").keyup(function() {
    var confirma = $("#passchangeprima").val();
    $("#pswd_info").show();
    if (confirma !== "") {
      verifica(confirma);
      
    } else {
      $("#pswd_info").hide();
      $("#advertencia").hide();
    }
  });

  $("#passchangeprima").focus(function() {
    $('#length').removeClass('valid').addClass('invalid');
    $('#capital').removeClass('valid').addClass('invalid');
    $('#number').removeClass('valid').addClass('invalid');
    $('#number').removeClass('valid').addClass('invalid');
  });

  $("#passchangeprima").blur(function() {
    var confirma = $("#passchangeprima").val();
    verifica(confirma);
    if (!resultado === false) {
      $("#pswd_info").hide();
    }
  });

  var cerrarVentana = function () {
    window.setTimeout(location.href="loginControl.php?msg=5",2000000);
  };
  
//  var procesarDatos = function () {
//    $.ajax({ 
//      type: 'POST',
//      url: 'indexControl.php',
//      data: {
//      accion: "cambiarpassword",
//              passchangeprima: $("#passchangeprima").val(),
//              passchangeconfirm: $("#passchangeconfirm").val()
//      },
//      success: function(data){
//        $("#advertencia").html('<li class="valid"><strong>' + data + '</strong></li>');
//        $("#passchangeprima").prop('disabled', true);
//        $("#passchangeconfirm").prop('disabled', true);
//        cerrarVentana();
//        }
//    });
//  };
  
  $("#enviar").click(function(e){ 
      e.stopPropagation();
      e.stopPropagation();
      if ( comprobarContra() ) {    
          verifica($("#passchangeprima").val());
          if (resultado){
            //Dejaremos esto en manos de PHP puro, por favor
              //procesarDatos();
              $("#advertencia").show();
          }else{
              $("#passchangeprima").focus();
              $("#advertencia").show();
              $("#advertencia").html('<li class="invalid"><strong>Su contraseña esta vacía</strong></li>');     
          }
      }else{
          $("#passchangeprima").focus();
          $("#advertencia").show();
          $("#advertencia").html('<li class="invalid"><strong>Las contraseñas no son iguales</strong></li>');
          
      }
  });

$( "cambiopass" ).submit(function( event ) {
  event.preventDefault();
});


});
