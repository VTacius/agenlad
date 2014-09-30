<?php
namespace controladores;
/**
 * Description of tecnicoControl
 *
 * @author alortiz
 */
class usershowControl extends \clases\sesion {
  
    public function __construct() {
        parent::__construct();
        // Nombramos la página que hemos de producir
        $this->pagina = "usershow";
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
    }
    
    
    public function datos(){
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina); 
        // Recuperamos los parametros que le son enviados
        $usuarioCliente = $this->input('usuarioCliente','Favor, escriba un username');
        // Recuperamos firmaz desde sesion        
        $clavez = $this->getClavez();
        
        // Empezamos con un objeto usuario
        $usuario = new \Modelos\userSamba($this->dn, $this->pswd);
        $usuario->setUid($usuarioCliente);
        $correo = $usuario->getMail();
        
        // Seguimos con el objeto Grupo
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $grupo->setGidNumber($usuario->getGidNumber());
        
        // Por último, el objeto mailbox
        $mailbox = new \Modelos\mailbox($clavez['dn'], $clavez['pswd']);
        $mailbox->cuenta($correo);
        
        // Configuramos los datos
        $datos = array(
            'oficina' => $usuario->getOu(),
            'nameuser' => $usuario->getCn(),
            'mailuser' => $correo,
            'psswduser' => $usuario->getuserPassword(),
            'grupouser' => $grupo->getCn(),
            'localidad' => $usuario->getO(),
            'buzonstatus'=> $mailbox->getZimbraMailStatus(),
            'cuentastatus'=> $mailbox->getZimbraAccountStatus()
        );
        
        $errores = array_merge(
                array("errorLdap" => $usuario->getErrorLdap()),
                array("errorGrupo" => $grupo->getErrorLdap()),
                array("errorZimbra" => $mailbox->getErrorSoap())
        );
        
	$resultado = array_merge($datos, $errores);
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
