$(document).ready(function(){
    $.ajax({
        url: "/js/data/establecimientos.json",
        dataType: 'json',
        success: configurarTipoEstablecimiento
    });
});

var configurarTipoEstablecimiento = function(data){
    $.establecimientos = data;
    $.each($.establecimientos[0], function(i,e){
        $('#st').append($('<option>', { 
            value: e,
            text: e
        }));
        
    });
}
