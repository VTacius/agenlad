$(document).ready(function(){
    $("#mailModForm").hide();
    $("#userModForm").hide();
});

$('.btn-toggle').click(function(e) {
    $(this).children('.btn').toggleClass('active');  
    $(this).children('.btn').toggleClass('btn-primary');
    e.stopPropagation(); 
    e.preventDefault();
});

$("#busqueda #enviar").click(function(e){
//    console.log($("#buzonstatusBtn").children(".active").text());
//    console.log($("#cuentastatusBtn").children(".active").text());
    e.stopPropagation(); 
    e.preventDefault();
//    $("#busqueda").hide();
    datos();
    $("#mailModForm").show();
    $("#userModForm").show();
});


/**
 * Crea ambas listas de selección con el resultado devuelto desde el servidor
 * @param {array} lista
 * @returns {undefined}
 */
var crearSelectOption = function(lista){
    $(lista).each(function(index, elemento){
        var option = "<option value=" + elemento.gidNumber + ">" + elemento.cn + "</option>";
        $("#grupouser").append(option);
        $("#grupos").append(option);
    });
    
};

/**
 * Llenamos el formulario de los datos actuales del usuario para que sean 
 * considerados para su modificacion
 * @param {array} data
 * @returns {undefined}
 */
var mostrarDatos = function(data){
    $("#cargo").val(data.cargo);
    $("#oficina").val(data.oficina);
    $("#nameuser").val(data.nameuser);
    $("#telefono").val(data.telefono);
    $("#apelluser").val(data.apelluser);
    $("#localidad").val(data.localidad);
    crearSelectOption(data.grupos);
    $("#grupouser option:contains('" + data.grupouser + "')").attr('selected','selected');
    $(data.gruposuser).each(function(index, elemento){
        $("#grupos option:contains('" + elemento + "')").attr('selected','selected');
    });
    
    if (data.cuentastatus === "active"){
        $("#cuenta #apagado").text("Inhabilitar");
    }else{
        $("#cuenta #apagado").text(data.cuentastatus);
    }
    
};

var datos = function(){
    $.ajax({
        type: 'POST',
        url: '/usermod/envio',
        dataType: 'json',
        data: {
            usuarioModificar: $("#usuarioModificar").val()
        },
        success: mostrarDatos,
        error: function(){
            console.log("Algo malo ha sucedido");
        }
        
    });
};