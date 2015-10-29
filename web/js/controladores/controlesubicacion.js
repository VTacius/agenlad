$(document).ready(function(){
    oAutocomplementar();
});

/**
 * Contrucción de controles o con la función de autocomplementado
 */
var oAutocomplementar = function(){
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
}

/**
 * Contrucción de controles ou con la función de autocomplementado
 * Debería ser llamado sólo por medio de oAutocomplementar
 */
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
