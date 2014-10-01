<?php
namespace controladores;
/**
 * Description of tecnicoControl
 *
 * @author alortiz
 */
class usershowControl extends \clases\sesion {
    
    protected $error = array();
    protected $datos = array();
    protected $mensaje = array();
    
    public function __construct() {
        parent::__construct();
        // Nombramos la página que hemos de producir
        $this->pagina = "usershow";
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
    }
    
    /**
     * TODO: Hay uno bastante parecido en directorioControl
     * @param type $filter
     * @return type
     */
    private function busquedaUsuario($usuario) {
        $usuarios = new \Modelos\userPosix($this->dn, $this->pswd, 'central' );
        $atributos = array('cn');
        $filtro = array("cn"=>"NOT (root OR nobody)",'uid'=>$usuario); 
        $datos = $usuarios->search($filtro, $atributos, "dc=sv");
        if (empty($datos[0]['cn'])) {
            return false;
        }else{
            return true;
        }
    }
    
    protected function usuario($usuarioCliente){
        // Empezamos con un objeto usuario
        $usuario = new \Modelos\userSamba($this->dn, $this->pswd);
        $usuario->setUid($usuarioCliente);
        if ($usuario->getUid() === "{empty}") {
            if ($this->busquedaUsuario($usuarioCliente)) {
                $this->mensaje = array("codigo" => "warning", 'mensaje' => "Usuario $usuarioCliente no se encuentra bajo su administración");
            }else{
                $this->mensaje = array("codigo" => "warning", 'mensaje' => "Usuario $usuarioCliente no existe");
            }
            $correo = "{empty}";
        }else{
            $correo = $usuario->getMail();
        }
        $group = $usuario->getGidNumber();
        $this->datos['oficina'] = $usuario->getOu();
        $this->datos['nameuser'] = $usuario->getCn();
        $this->datos['psswduser'] = $usuario->getuserPassword();
        $this->datos['localidad'] = $usuario->getO();
        return array('correo'=>$correo, 'grupo'=>$group);
    }
    
    protected function grupo($group){
        // Seguimos con el objeto Grupo
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd );
        $grupo->setGidNumber($group);
        $this->datos['grupouser'] = $grupo->getCn();
        $this->error[] = $grupo->getErrorLdap();
    }  
    
    protected function mail($clavez, $correo){
        // Por último, el objeto mailbox
        $mailbox = new \Modelos\mailbox($clavez['dn'], $clavez['pswd']);
        $mailbox->cuenta($correo);
        
        // Configuramos los datos
        $this->datos['mailuser'] = $correo;
        $this->datos['buzonstatus']= $mailbox->getZimbraMailStatus();
        $this->datos['cuentastatus'] = $mailbox->getZimbraAccountStatus();
    }


    public function datos(){
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina); 
        // Recuperamos los parametros que le son enviados
        $usuarioCliente = $this->input('usuarioCliente','Favor, escriba un username');
        // Obtenemos las claves para acceder a Soap Zimbra
        $clavez = $this->getClavez();
        
        $usuario = $this->usuario($usuarioCliente);
        
        $this->grupo($usuario['grupo']);
        
        $this->mail($clavez, $usuario['correo']);
        
        $resultado = array(
                'error' => $this->error,
                'datos' => $this->datos,
                'mensaje'=> $this->mensaje
        );
        
        print json_encode($resultado);
    }
    
    /**
     * Controlador por defecto
     */
    public function display(){
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina); 
        
        // Obtenemos los datos que hemos de enviar a la vista
        echo $this->twig->render('usershow.html.twig', $this->parametros); 
    }
}
