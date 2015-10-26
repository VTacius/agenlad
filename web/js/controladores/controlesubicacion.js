/**
Contrucción de controles
*/
$(document).ready(function(){
    $("#o" ).autocomplete({
        minLength: 2,
        source: function( request, response ) {
            $.ajax({
                url: "/helpers/establecimiento",
                type: 'POST',
                dataType: "json",
                data: {
                  busqueda: request.term
                },
                success: function( data ) {
                  response( data );
                }
            })
        },
        select: function( event, ui ) {
            $(this).attr('data-o', ui.item.id);
            ouAutocomplementar(ui.item.id);
        },
    });
});

var ouAutocomplementar = function(o){
    $("#ou" ).autocomplete({
        minLength: 2,
        source: function( request, response ) {
            $.ajax({
                url: "/helpers/oficina",
                type: 'POST',
                dataType: "json",
                data: {
                  busqueda: request.term,
                  establecimiento: o
                },
                success: function( data ) {
                  response( data );
                }
            })
        },
    });
};
