<?php
namespace controladores;

/**
 * Controlador para manejo de pruebas
 *
 * @author alortiz
 */
class pruebaControl extends \clases\sesion{
    public function display(){
        $usuario = new \clases\user($this->dn, $this->pswd);
        $usuario->setSn("Pineda");
        print_r($usuario->getSn());
        print_r($usuario->getCn());
        $usuario->setOu('Nuevo lugar');
        $usuario->setO('Nueva oficina');
        print_r($usuario->getO());
        print_r($usuario->getOu());
    }
}
