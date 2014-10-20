<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controladores\configuracion;

/**
 * Description of inicializacion
 *
 * @author vtacius
 */
class inicializacion extends \clases\sesion {
    public function __construct() {
        parent::__construct();
    }
    
    
    protected function comprobarInicio($sentencia){
        $conexion = $this->index->get('dbconexion');
        $cmds_configuracion = $sentencia;
        $resultado = $conexion->exec($cmds_configuracion);
        
        return $resultado[0]['dominio'];
        
    }

    public function usuario(){
        $this->comprobarSesion();
        $objetos = array(
            'select count(user) as dominio from user');
        $inicio = 0;
        foreach ($objetos as $sentencia) {
            $inicio += $this->comprobarInicio($sentencia);
        }
        if ($inicio === 2){
            // Existen dos usuarios por defecto en la base de datos
            $this->parametros['menu'] = array(
                'inicializacion/'=>'Configuracion Dominio',
                'inicializacion/usuario'=>'Configuracion de Roles'
            );
            echo $this->twig->render('configuracion/usuario.html.twig', $this->parametros);
        }else {
            print "Ya existe al menos un usuario Admnistrador Global para este dominio";
        }
        
    }

    public function display() {
        $this->comprobarSesion();
        $objetos = array('select count(dominio) as dominio from configuracion',
                        'select count(dominio) as dominio from credenciales');
        $inicio = 0;
        
        foreach ($objetos as $sentencia) {
            $inicio += $this->comprobarInicio($sentencia);
        }
        if ($inicio === 0){
            // No hay datos dentro de este lugar
            echo $this->twig->render('configuracion/dominioNuevo.html.twig', $this->parametros);   
        }else {
            print "Ya existe al menos un dominio configurado";
        }
    }
    
}
