$(document).ready(function(){
    toggleUseradd(false);
    $("dl").hide();
});

$("#enviar").click(function(e){
    e.stopPropagation(); 
    e.preventDefault();
    var datos =  {
        usuarioCliente: $("#usuarioCliente").val()
    };
    procesarDatos(datos);
});

/**
 * Cambia #respuesta si que existe, es decir, si fue creada desde Twig 
 * en respuesta al rol del usuario
 * @param {boolean} cambio
 * @returns {undefined}
 */
function toggleUseradd(cambio){
    if ( $("#useraddTecnico").length > 0 ){
        if (cambio){
            $("#useraddTecnico").show();
        } else {
            $("#useraddTecnico").hide();
        }
    }
}

/**
 * Dato el id de un elemento dd|dt, revisa si tiene datos para ver si los muestra
 * en pantalla
 * @param {object} datos
 * @param {string} objeto
 * @returns {undefined}
 */
function llenarControl(datos, objeto) {
    if (datos[objeto] === "{empty}") {
        $("#" + objeto).hide();
        $("dd#" + objeto).text("");
    }else{
        $("#" + objeto).show();
        $("dd#" + objeto).text(datos[objeto]);
    }
}

/**
 * Llena la pantalla del usuario con los datos obtenidos del servidor
 * @param {json} data
 * @returns {undefined}
 */
var mostrarDatos = function(data){
    llenarControl(data.datos, 'psswduser')
    
    llenarControl(data.datos, 'nameuser');
    llenarControl(data.datos, 'psswduser');
    llenarControl(data.datos, 'grupouser');
    llenarControl(data.datos, 'mailuser');
    llenarControl(data.datos, 'buzonstatus');
    llenarControl(data.datos, 'cuentastatus');
    
    llenarControl(data.datos, 'usermod');
  
    llenarControl(data.datos, 'localidad');
    llenarControl(data.datos, 'oficina');
    
    console.log(data);
    pmostrarError(data);
    pmostrarMensaje(data);
    // Llenamos los datos
    if (!(data.nameuser==="{empty}" && data.buzonstatus==="{empty}")){
        // Modificamos el enlace
        $("#usermodTecnico").attr( 'href', "/usermod/" + $("#usuarioCliente").val() );
        // Una vez todo configurado, mostramos y ocultamos
        toggleUseradd(false);
        $("dl").show();
    }else{
        $("#useraddTecnico").attr( 'href', "/useradd/" + $("#usuarioCliente").val() );
        toggleUseradd(true);
        $("#respuesta").hide();
    }
};

var procesarDatos = function(datos){
    $.ajax({
        type: 'POST',
        url: '/usershow/datos',
        dataType: 'json',
        data: datos,
        success: mostrarDatos,
        error: errorOnResponse
    });
};
