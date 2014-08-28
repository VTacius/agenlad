$( 'document' ).ready(function() {
    var filtraje = {"uid":"alortiz"};
    $.ajax({
        url: "/directorio",
        type: 'POST',
        data: filtraje,
        dataType: 'JSON',
        success: mostrar
  });
  
  
});

var mostrar = function(result){
    $(result).each(
            function(item, elemento){
                $("<tr><td>" + elemento.uid + "</td><td>" + elemento.cn + "</td><td>" + elemento.cn + "</td><td>" + elemento.cn + "</td></tr>").appendTo("#respuesta");
            });
    $("#prueba").html(result);
};

