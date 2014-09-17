<?php
namespace controladores;
class usermodControl extends \clases\sesion {
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "usermod";
    }
    
    public function display(){
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina);     
        echo $this->twig->render('usermod.html.twig', $this->parametros);       
    
    }
}
