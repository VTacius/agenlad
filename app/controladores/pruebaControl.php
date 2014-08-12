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
        $usuario->setUid('alortiz');
        print_r($usuario->getSn());
        print "<br>";
        print_r($usuario->getCn());
        print "<br>";
//        $usuario->setOu('Nuevo lugar');
//        $usuario->setO('Nueva oficina');
//        $usuario->setUserPassword('tracio');
        print_r($usuario->getO());
        print "<br>";
        print_r($usuario->getOu());
        print "<br>";
        $grupo = new \clases\grupo($this->dn, $this->pswd);
        $grupo->setGidNumber($usuario->getGidNumber());
        print_r($grupo->getCn());
        print "<br>";
        print_r($usuario->getSambaSID());
        print "<br>";
        print_r($usuario->getSambaAcctFlags());
        print "<br>";
        print_r($usuario->getSambaHomeDrive());
        print "<br>";
    }
}
