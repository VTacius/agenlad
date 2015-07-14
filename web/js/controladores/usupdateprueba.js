$(document).ready(function(){
    $.applyDataMask('input');
    $.validator.setDefaults({
        debug: true,
        submitHandler: envio,
    });

    /*
     * Creo el control para seleccionar fecha
     **/
    
    $( "#fecha" ).datepicker({
       changeMonth: true,
       changeYear: true,
       yearRange: "-100Y:-18Y"
    });

    $.validator.addMethod('nombre', function(valor, elemento){
        return /^(([A-Z][a-záéíóú]+\s?(de\s)*)){1,3}$/.test(valor);
    }, "Verifique su nombre");
    
    $.validator.addMethod('fecha', function(valor, elemento){
        return /^(((1|2|0*)[1-9])|(3[0-1]))\/((0*[1-9])|(1[0-2]))\/(1|2)[0-9]{3}$/.test(valor);
    }, "Verifique que la fecha sea válida");

    $('#actualizacionDatos').validate();

    var formatRepoSelection = function(repo) {
        return repo.full_name || repo.text;
    }

    var formatRepo = function (repo) {
        if (repo.loading) return repo.text;
        return repo.text;
        
    }

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
                  console.log(data);
                }
            })
        },
        select: function( event, ui ) {
            console.log(ui.item.id);
            $(this).attr('data-codigo', ui.item.id);
        },
    });

});

/*
 * Funciones varias para hacer un montón de cosas
 **/

var envio = function(){
    datos = recogerDatos();
    datos['o'] = $('#o').data('codigo');
    console.log(datos);
};


/*
 * No recuerdo como debería llamar a esto, pero empiezan
 **/
$('#actualizacionDatos').submit(function(e){
    e.preventDefault();
    e.stopPropagation();
});
