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
    }
    
    
    public function datos(){
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina); 
        // Recuperamos los parametros que le son enviados
        $usuarioCliente = $this->input('usuarioCliente','Favor, escriba un username');
        // Recuperamos firmaz desde sesion
        $firmaz = $this->index->get('SESSION.firmaz');
        $hashito = new \clases\cifrado();
        $clavez = $hashito->descifrada($firmaz, $this->pswd);
        
        // Empezamos con un objeto usuario
        $usuario = new \Modelos\userSamba($this->dn, $this->pswd);
        $usuario->setUid($usuarioCliente);
        
        // Seguimos con el objeto Grupo
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $grupo->setGidNumber($usuario->getGidNumber());
        
        // Por último, el objeto mailbox
        $mailbox = new \Modelos\mailbox($clavez);
        $mailbox->setUid($usuarioCliente);
        
        // Configuramos los datos
        $datos = array(
            'psswduser' => $usuario->getuserPassword(),
            'grupouser' => $grupo->getCn(),
            'nameuser' => $usuario->getCn(),
            'localidad' => $usuario->getO(),
            'oficina' => $usuario->getOu(),
            'cuentastatus'=> $mailbox->getZimbraAccountStatus(),
            'buzonstatus'=> $mailbox->getZimbraMailStatus()
        );
        print json_encode($datos);
//        $this->parametros['datos'] = $datos;
//        
//        echo $this->twig->render('tecnico.html.twig', $this->parametros);
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
