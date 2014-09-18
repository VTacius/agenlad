$('.btn-toggle').click(function() {
    $(this).children('.btn').toggleClass('active');  
    $(this).children('.btn').toggleClass('btn-primary');
});

$("#enviar").click(function(e){
    console.log($("#buzonstatusBtn").children(".active").text());
    console.log($("#cuentastatusBtn").children(".active").text());
//    e.stopPropagation(); 
//    e.preventDefault();
});


