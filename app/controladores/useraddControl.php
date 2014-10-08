<?php
namespace controladores;
class useraddControl extends \clases\sesion{
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "useradd";
    }
    
    public function display() {
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina); 
        echo $this->twig->render('useradd.html.twig', $this->parametros);       

    }
}
