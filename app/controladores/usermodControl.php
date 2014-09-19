<?php
namespace controladores;
class usermodControl extends \clases\sesion {
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "usermod";
    }
    
    private function listar_Grupos($base){
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $search = array('cn'=>'*', 'gidNumber'=>'*');
        return $grupo->search($search, $base);
    }
    
    public function modificarUsuario(){
        $this->comprobar($this->pagina); 
        $usuarioGrupo = $this->index->get('POST.grupouser');
        $usuarioGrupos = $this->index->get('POST.grupos');
        $usuarioNombre = $this->index->get('POST.nameuser');
        $usuarioOficina = $this->index->get('POST.oficina');
        $usuarioApellido = $this->index->get('POST.apelluser');
        $usuarioModificar = $this->index->get('POST.usermod');
        $usuarioLocalidad = $this->index->get('POST.localidad');
        
        $claves = $this->getClaves();
        print_r($claves);
        $usuario = new \Modelos\userSamba($claves['dn'], $claves['pswd']);
        $usuario->setUid($usuarioModificar);
        print "Esta es la entrada tal como la obtenemos<br>";
        $usuario->getEntrada();
        print "<br>";
        $usuario->setOu($usuarioOficina);
        $usuario->setO($usuarioLocalidad);
        $usuario->configuraNombre($usuarioNombre, $usuarioApellido);
        $usuario->setGidNumber($usuarioGrupo);
        print "Esto es despues de un par de set<br><br>";
        $usuario->getEntrada();
        print "<br>";
    }


    public function mostrarUsuario(){
        $this->parametros['pagina'] = $this->pagina;
        $this->comprobar($this->pagina);     
        
        // Recuperamos los parametros que le son enviados mediante POST o GET
        // No vengas de listillo a querer usar un ternario porque va a fallar
        $usuarioCliente = $this->index->get('PARAMS.usuarioModificar');
        if ($usuarioCliente == ""){
            $usuarioCliente = $this->index->get('POST.usuarioModificar');
        }
        
        // Recuperamos firmaz desde sesion
        
        $clavez = $this->getClavez();
        
        // Empezamos con un objeto usuario
        $usuario = new \Modelos\userSamba($this->dn, $this->pswd);
        $usuario->setUid($usuarioCliente);
       
        // Seguimos con el objeto Grupo
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $grupo->setGidNumber($usuario->getGidNumber());
        
        // Por último, el objeto mailbox
        $mailbox = new \Modelos\mailbox($clavez['dn'], $clavez['pswd']);
        $mailbox->setUid($usuarioCliente);
        
        // Configuramos los datos
        $datos = array(
            'grupos' => $this->listar_Grupos($usuario->getDNBase()),
            'usermod' => $usuarioCliente,
            'oficina' => $usuario->getOu(),
            'nameuser' => $usuario->getGivenName(),
            'grupouser' => $grupo->getCn(),
            'apelluser' => $usuario->getSn(),
            'localidad' => $usuario->getO(),
            'buzonstatus'=> $mailbox->getZimbraMailStatus(),
            'cuentastatus'=> $mailbox->getZimbraAccountStatus()
        );        
        
        $this->parametros['datos'] = $datos;
        
        echo $this->twig->render('usermod.form.twig', $this->parametros);       
    }
    
    public function display(){
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina);     
        echo $this->twig->render('usermod.html.twig', $this->parametros);       
    }
}
