<?php
namespace controladores;
/**
 * Controlador para revision de datos de usuario
 * 
 * @version 0.2
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
     * TODO: Una copia descarada en usermodControl
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
    
    /**
     * Devuelve los datos de usuario, y envia mensajes de error según el usuario
     * exista o no, en que ámbito administrativo existe y otros
     * @param string $usuarioCliente
     * @return array
     */
    protected function usuario($usuarioCliente){
        // Empezamos con un objeto usuario
        $usuario = new \Modelos\userSamba($this->dn, $this->pswd);
        $usuario->setUid($usuarioCliente);
        // TODO: Hay uno bastante parecido en directorioControl
         // TODO: Una copia descarada en usermodControl
        if ($usuario->getEntrada()['dn'] === "{empty}") {
            // Por las nuevas formas en objetosLdap
            $usuario->setUid('{empty}');
            if ($this->busquedaUsuario($usuarioCliente)) {
                $this->mensaje = array("codigo" => "warning", 'mensaje' => "Usuario $usuarioCliente no se encuentra bajo su administración");
                $this->datos['enlaces'] = array('creacion'=>false, "modificacion"=>false);
            }else{
                $this->mensaje = array("codigo" => "warning", 'mensaje' => "Usuario $usuarioCliente no existe");
                $this->datos['enlaces'] = array('creacion'=>true, "modificacion"=>false);
            }
            $correo = "{empty}";
        }else{
            $this->datos['enlaces'] = array('creacion'=>false, "modificacion"=>true);
            $correo = $usuario->getMail();
        }
        $group = $usuario->getGidNumber();
        $this->datos['usermod'] = $usuario->getUid();
        $this->datos['oficina'] = $usuario->getOu();
        $this->datos['nombrecompleto'] = $usuario->getCn();
        $this->datos['nameuser'] = $usuario->getGivenName();
        $this->datos['apelluser'] = $usuario->getSn();
        $this->datos['cargo'] = $usuario->getTitle();
        $this->datos['phone'] = $usuario->getTelephoneNumber();
        $this->datos['psswduser'] = $usuario->getuserPassword();
        $this->datos['localidad'] = $usuario->getO();
        return array('correo'=>$correo, 'grupo'=>$group);
    }
    
    /**
     * Obtiene el grupo al cual pertenece el usuario
     * @param type $group
     */
    protected function grupo($group){
        // Seguimos con el objeto Grupo
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd );
        $grupo->setGidNumber($group);
        $this->datos['grupouser'] = $grupo->getCn();
    }  
    
    /**
     * Obtiene los datos del buzón de correos para el usuario
     * Espera a que $correo sea {empty} para invalidar toda la busqueda si acaso
     * el usuario en cuestion no debe ser modificado por tal administrador
     * @param array $clavez
     * @param string $correo
     */
    protected function mail($clavez, $correo){
        // Por último, el objeto mailbox
        $mailbox = new \Modelos\mailbox($clavez['dn'], $clavez['pswd']);
        $mailbox->cuenta($correo);
        
        // Configuramos los datos
        $this->datos['mailuser'] = $mailbox->getMail();
        $this->datos['buzonstatus']= $mailbox->getZimbraMailStatus();
        $this->datos['cuentastatus'] = $mailbox->getZimbraAccountStatus();
    }
    
    /**
     * Responde a la ruta en /usermod/cambio
     */
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
