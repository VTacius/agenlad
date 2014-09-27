$(document).ready(function(){
    var filtraje = filtro();
    busqueda(filtraje);
    $.directorioControl = new Object();
    $.directorioControl.pulsaciones = 0;
});

/**
 * Si presiona enter, en lugar de enviar el formulario, ejecuta la busqueda con lo que tenga
 * @param {bind?} e
 */
$("input").keypress(function(e){
    if ( e.which === 13 ) {
        var filtraje = filtro();
        busqueda(filtraje);
        e.preventDefault();
    } 
});

/*
 * Una vez ha presionado la tecla más de tres veces, ejecuta la busqueda
 */
$("input").keyup(function(e){
    verificaVacio();
    verificaEspacio($("#uid").val());
    if (!( e.which === 0)){
   	 if ($.directorioControl.pulsaciones  >= 2) {
   	     var filtraje = filtro();
   	     busqueda(filtraje);
   	     $.directorioControl.pulsaciones = 0;
   	 }else{
   	     $.directorioControl.pulsaciones++;
   	 }
    }
});

/**
 * TODO: Algo me dice que esto no puede estar bien, dadas las condiciones de las demás funciones
 * @returns {undefined}
 */
var verificaVacio = function(){
    if (isEmpty($("#o").val()) && isEmpty($("#ou").val()) && isEmpty($("#uid").val())) {
        var filtraje = filtro();
        busqueda(filtraje);
    }
};

// TODO: Esto debería estar en el keyup del #uid, pero no sé que tal se llevara con dos metodos enlazados al mismo evento
var verificaEspacio = function(valor){
    if (valor.indexOf(" ")!==-1){
        var filtraje = filtro();
        busqueda(filtraje);
    }
}

var preFiltro = function(valor){
    if (!isEmpty(valor)) {
        return valor + "*";
    }else{
        return "*";
    }
};

var filtro = function(){
    var filtro = {
        'o': preFiltro($("#o").val()),
        'ou': preFiltro($("#ou").val()),
        'uid': preFiltro($("#uid").val())
    };
    return filtro;
};

var busqueda = function(filtro){
    console.log("Desde busqueda AJAX Request");
    console.log(filtro);
    $.ajax({
        url: "/directorio/busqueda",
        type: "POST",
        data: filtro,
        dataType: 'json',
        success: mostrar,
        error: function(data){
            console.log("Algo malo ha sucedido");
            console.log(data);
        }
    });
};

var elementoAttr = function(attr){
    if (!isEmpty(attr)) {
        return "<td>" + attr + "</td>";
    }else{
        return "<td> - </td>";
    }
    
}

var mostrar = function(result){
    console.log("Estoy recibiendo datos");
    console.log(result);
    console.log(result.datos);
    mostrarErrorLdap(result);
    $("#respuesta tr").remove();
    $(result.datos).each(
        function(item, elemento){
            // Creo que debido a lo que hace elementoAttr, ya no será necesario validar de esta forma
//            if ((!isEmpty(elemento.cn)) && (!isEmpty(elemento.mail))) {
                $("#respuesta").append("<tr>" + elementoAttr(elemento.cn) +  elementoAttr(elemento.mail) + elementoAttr(elemento.title) + elementoAttr(elemento.telephoneNumber) + "</tr>");
//            }
        });
};

