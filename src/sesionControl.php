<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of loginInicio
 *
 * @author alortiz
 */
class sesionControl {
    private $index;
    private $usuario;
    private $password;
    private $server;
    private $puerto;
    private $base;
    private $sesion;
    
    public function loginInicio($base){
        $this->index = $base;
        $this->usuario = $this->index->get('POST.user'); 
        $this->password = $this->index->get('POST.pswd'); 
        $this->server = $this->index->get('server');
        $this->puerto = $this->index->get('puerto');
        $this->base = $this->index->get('base');
        $this->sesion = new clases\crearSesion($this->server, $this->puerto, $this->usuario, $this->password, $this->base);
    }
    
    public function display($base){
        try {
            // La redirección a la página de inicio esta dentro de la clase
            $this->sesion->sesionar($this->usuario, $this->password, "10.10.50.60");
        } catch (Exception $e) {
          // Si algo de todo lo que puede fallar dentro de la clase falla, capturamos el
          // mensaje de error y se lo pasamos a la plantilla
            $plantilla = $this->index->get('twig');
            $parametros = array(
              'mensaje' => $e->getMessage()
            );
            echo $plantilla->render('login.html.twig', $parametros);
        }
    }
}
