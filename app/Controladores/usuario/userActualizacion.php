<?php
namespace Controladores\usuario;
class userActualizacion extends \Controladores\usuario\usermodControl { 
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "actualizacion";
        $this->error = array();
        $this->mensaje = array();
    }
    
    public function actualizarDatosAdministrativos($usuario, $pregunta, $respuesta, $jvs, $fecha_nacimiento, $nit){
        $fecha = (empty($fecha_nacimiento)) ? '12/02/1809' : $fecha_nacimiento;
        $date = \DateTime::createFromFormat('d/m/Y', $fecha);
        $dato_fecha = $date->format('Y/m/d');
        if (empty($jvs)){
            $sentencia = array(
                'update' => 'update datos_administrativos set pregunta=:pregunta, respuesta=:respuesta, fecha_nacimiento=:fecha_nacimiento, nit=:nit where usuario=:usuario',
                'insert' => 'insert into datos_administrativos(usuario, pregunta, respuesta, fecha_nacimiento, nit) values(:usuario, :pregunta, :respuesta, :fecha_nacimiento, :nit)',
                'valores' => array(':usuario'=> $usuario, ':pregunta'=>$pregunta, 'respuesta'=>$respuesta, ':fecha_nacimiento'=> $dato_fecha, ':nit'=>$nit)
            ); 
        }else{
            $sentencia = array(
                'update' => 'update datos_administrativos set pregunta=:pregunta, respuesta=:respuesta, fecha_nacimiento=:fecha_nacimiento, nit=:nit, jvs=:jvs where usuario=:usuario',
                'insert' => 'insert into datos_administrativos(usuario, pregunta, respuesta, fecha_nacimiento, nit, jvs) values(:usuario, :pregunta, :respuesta, :fecha_nacimiento, :nit, :jvs)',
                'valores' => array(':usuario'=> $usuario, ':pregunta'=>$pregunta, 'respuesta'=>$respuesta, ':jvs'=>$jvs, ':fecha_nacimiento'=> $dato_fecha, ':nit'=>$nit)
            );
        }
        $entrada = $this->obtenerDatosAdministrativos($usuario);
        try{
            $base = $this->index->get('dbconexion');
            if (count($entrada) > 0){
                $entrada = $base->exec($sentencia['update'], $sentencia['valores']);
                $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Actualizados los datos administrativos para usuario " .  $usuario);
            }else{
                $entrada = $base->exec($sentencia['insert'], $sentencia['valores']);
                $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Agregados los datos administrativos para usuario " .  $usuario);
            }
        }catch (\PDOException $e){
            // Lo pones en el mensaje de error a enviar al servidor
            $this->mensaje[] = array("codigo" => "danger", 'mensaje' => 'Error agregando datos administrativos. Revise los mensajes asociados');
            $this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: " . $e->getMessage() . " " . $dato_fecha . " " . $fecha);
            // $this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: ");
        }
    }

    public function actualizacionCambio(){ 
        $this->comprobar($this->pagina); 
        $usuarioAttr = array(
            'usuarioCargo' => $this->index->get('POST.title'),
            'usuarioPhone' => $this->index->get('POST.telephoneNumber'),
            'usuarioGrupo' => $this->index->get('POST.grupouser'),
            'usuarioNombre' => $this->index->get('POST.nombre'),
            'usuarioOficina' => $this->index->get('POST.ou'),
            'usuarioApellido' => $this->index->get('POST.apellido'),
            'usuarioModificar' => $this->index->get('SESSION.user'),
            'usuarioLocalidad' => $this->index->get('POST.o'),
            'usuarioNivel' => $this->index->get('POST.st')
        );

        $pregunta = $this->index->get('POST.pregunta');
        $respuesta = $this->index->get('POST.respuesta');
        $fecha = $this->index->get('POST.fecha');
        $jvs = $this->index->get('POST.jvs');
        $nit = $this->index->get('POST.nit');

        // Añadimos una marca para saber que este usuario es bastante más personas de lo que pudiéramos suponer
        $usuarioAttr['usuarioDescripcion'] = "USERMODWEB";

        // Operaciones para samba
        $usuario = new \Modelos\userSamba($this->dn, $this->pswd);
        $this->modificarAttrUsuario($usuario, $usuarioAttr);

        // Operaciones para correo
        $correo = $usuario->getMail();
        $this->modificarUsuarioZimbra($correo, $usuarioAttr);
        
        //Operaciones para distintos datos administrativos que meteremos en una base de datos, espero que a alguien le importen 
        $this->actualizarDatosAdministrativos($usuarioAttr['usuarioModificar'], $pregunta, $respuesta, $jvs, $fecha, $nit);

        $resultado = array(
            'mensaje' => $this->mensaje,
            'error' => $this->error
        );
        print json_encode($resultado);
    }
    
    public function getUsuario() {
        $this->comprobar($this->pagina); 
        // Esto es importante en la vista

        $this->usuario = $this->index->get('SESSION.user');
        // El siguiente método llena $this->datos, pero solo devuelve pocos datos
        $this->usuario($this->usuario);
        print json_encode($this->datos);
    }
    
    public function display() {
        $this->comprobar($this->pagina); 
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        echo $this->twig->render('usuario/useractualizacion.html.twig', $this->parametros);       
    }
}
