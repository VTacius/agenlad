<?php
namespace controladores;
class usermodControl extends \clases\sesion {
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "usermod";
        $this->parametros['pagina'] = $this->pagina;
    }
    
    private function listarGrupos($base){
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $search = array('cn'=>'*', 'gidNumber'=>'*');
        return $grupo->search($search, $base);
    }
    
    private function listarGruposUsuarios($base, $memberUid){
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $search = array('memberUid'=>$memberUid, 'cn'=> '*');
        $resultado = $grupo->search($search, $base);
        $grupos = array();
        foreach ($resultado as $value) {
            array_push($grupos, $value['cn']);
        }
        return $grupos;
    }
    
    private function modificarGruposAdicionales($usuarioGrupos, $usuario, $claves){
        $dnUser = $usuario->getDNEntrada();
        $grupo = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
        $grupo->setGidNumber($usuario->getGidNumber());
        
        $gruposActuales = $this->listarGruposUsuarios($usuario->getDNBase(), $usuario->getUid());
        foreach ($usuarioGrupos as $value) {
            if(!in_array($value, $gruposActuales)){
                $valores['memberuid'] = $dnUser;
                $grupo->agregarAtributos($dnUser, $valores);
            }
        }
        foreach ($gruposActuales as $value) {
            if(!in_array($value, $usuarioGrupos)){
                $valores['memberuid'] = $dnUser;
                $grupo->removerAtributos($dnUser, $valores);
            }
        }
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
        $usuario = new \Modelos\userSamba($claves['dn'], $claves['pswd']);
        $usuario->setUid($usuarioModificar);
        
        $usuario->setOu($usuarioOficina);
        $usuario->setO($usuarioLocalidad);
        $usuario->configuraNombre($usuarioNombre, $usuarioApellido);
        $usuario->setGidNumber($usuarioGrupo);
//        $resultado = $usuario->actualizarEntrada();
        $this->modificarGruposAdicionales($usuarioGrupos, $usuario, $claves);
    }


    public function mostrarUsuario(){
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
            'grupos' => $this->listarGrupos($usuario->getDNBase()),
            'usermod' => $usuarioCliente,
            'oficina' => $usuario->getOu(),
            'nameuser' => $usuario->getGivenName(),
            'grupouser' => $grupo->getCn(),
            'apelluser' => $usuario->getSn(),
            'localidad' => $usuario->getO(),
            'gruposuser' => $this->listarGruposUsuarios($usuario->getDNBase(), $usuario->getUid()),
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
