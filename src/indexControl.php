<?php
/**
 * Controlador para el index de la aplicación, especificamente, donde hacemos cambios de contraseña
 *
 * @author alortiz
 */

class indexControl extends clases\sesion {
    
    function indexControl(){
        // Consigue el objeto F3 en uso mediante el constructor de la clase padre
        clases\sesion::__construct();
        // Nombramos la página que hemos de producir
        $this->pagina = "index";
    }
    
    public function display() {
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina);
        $plantilla = $this->index->get('twig');
        $parametros = array(
            'usuario' =>  $this->index->get('SESSION.user'),
            'menu' => $this->index->get('SESSION.permisos'),
            'pagina' => $this->pagina
        );
        echo $plantilla->render('index.html.twig',$parametros);
    }
}


