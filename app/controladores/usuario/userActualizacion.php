<?php
namespace controladores\usuario;
class userActualizacion extends \controladores\usuario\usermodControl { 
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "actualizacion";
        $this->error = array();
        $this->mensaje = array();
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
        // Añadimos una marca para saber que este usuario es bastante más personas de lo que pudiéramos suponer
        $usuarioAttr['usuarioDescripcion'] = "USERMODWEB";


        // Operaciones para samba
        $usuario = new \Modelos\userSamba($this->dn, $this->pswd);
        // Modificamos los atributos del usuario
        $this->modificarAttrUsuario($usuario, $usuarioAttr);

        // Operaciones para correo
        $correo = $usuario->getMail();
        
        $this->modificarUsuarioZimbra($correo, $usuarioAttr);
       
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
        // Empieza el procedimiento

        $this->usuario = $this->index->get('SESSION.user');
        // El siguiente método llena $this->datos, pero solo devuelve pocos datos
        $user = $this->usuario($this->usuario);
        $this->parametros['datos'] = $this->datos;
        echo $this->twig->render('usuario/useractualizacion.html.twig', $this->parametros);       
    }
}
