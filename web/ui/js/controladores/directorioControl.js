$( 'document' ).ready(function() {
    $.ajax({
        url: "/directorio",
        success:function(result){
            $("#prueba").html(result);
  }});
});

