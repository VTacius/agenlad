<?php
namespace Controladores\configuracion;
/**
 * inicializacion
 *
 * @author vtacius
 */
class inicializacion extends \Clases\sesion {
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
        $objetos = array('select count(dominio) as dominio from configuracion',
                        'select count(dominio) as dominio from credenciales');
        $inicio = 0;
        
        foreach ($objetos as $sentencia) {
            $inicio += $this->comprobarInicio($sentencia);
        }
        if ($inicio === 0){
            $this->parametros['menu'] = array(
                'inicializacion/'=>'Configuracion Dominio',
                'inicializacion/usuario'=>'Configuracion de Roles'
            );
            // No hay datos dentro de este lugar
            echo $this->twig->render('configuracion/dominioNuevo.html.twig', $this->parametros);   
        }else {
            print "Ya existe al menos un dominio configurado";
        }
    }
    
}
