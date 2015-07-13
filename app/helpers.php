<?php
namespace controladores\helpers;
class userActualizacion extends \controladores\sesion { 
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "actualizacion";
        $this->error = array();
        $this->mensaje = array();
    }
    

    public function getEstablecimiento(){
        $nombre = $this->index->get('POST.busqueda');
        $db = new \DB\SQL('pgsql:host=10.168.10.65;port=5432;dbname=maestros;user=admin_maestros;password=maestros');
        $consulta = "select id, nombre as text from ctl_establecimiento where activo is True and nombre ilike ('%'|| :nombre || '%')";
        $args = array(':nombre'=>$nombre);
        $resultado = $db->exec($consulta, $args);
        print json_encode($resultado);
    }

}
