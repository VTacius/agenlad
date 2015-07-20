<?php
namespace controladores;
class helpers extends \clases\sesion { 
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "actualizacion";
        $this->error = array();
        $this->mensaje = array();
    }
    

    public function display() {
        print "Por implementar";
    }

    public function getEstablecimiento(){
        $this->comprobar($this->pagina); 
        $nombre = $this->index->get('POST.busqueda');
        $consulta = "select id, nombre as label from ctl_establecimiento where activo is True and nombre ilike ('%'|| :nombre || '%')";
        $args = array(':nombre'=>$nombre);
        try{
            $base = $this->index->get('dbconexion');
            $resultado = $base->exec($consulta, $args);
        }catch (\PDOException $e){
            // Lo pones en el mensaje de error a enviar al servidor
            $this->mensaje[] = array("codigo" => "danger", 'mensaje' => 'Error agregando datos administrativos. Revise los mensajes asociados');
            // $this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: " . $e->getMessage() );
            $this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: ");
        }
        print json_encode($resultado);
    }
    
    public function getOficinas(){
        $this->comprobar($this->pagina); 
        $nombre = $this->index->get('POST.busqueda');
        $estab = $this->index->get('POST.establecimiento');
        $consulta = "select dep.id, dep.nombre as label from ctl_dependencia_establecimiento as st inner join ctl_dependencia_tipo_dependencia as tipo on st.id_dependencia_tipo=tipo.id_dependencia inner join ctl_dependencia as dep on tipo.id_dependencia = dep.id  where st.id_establecimiento=:estab and dep.nombre ilike ('%'|| :nombre || '%')";
        $args = array(':nombre'=>$nombre, ':estab' => $estab);
        try{
            $base = $this->index->get('dbconexion');
            $resultado = $base->exec($consulta, $args);
        }catch (\PDOException $e){
            // Lo pones en el mensaje de error a enviar al servidor
            $this->mensaje[] = array("codigo" => "danger", 'mensaje' => 'Error agregando datos administrativos. Revise los mensajes asociados');
            // $this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: " . $e->getMessage() );
            $this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: ");
        }
        print json_encode($resultado);
    }

}
