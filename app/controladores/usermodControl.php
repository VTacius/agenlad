<?php
namespace controladores;
class usermodControl extends \clases\sesion {
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "usermod";
        $this->parametros['pagina'] = $this->pagina;
        $this->configuracion = $this->getConfiguracionDominio();
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
            if 	(array_key_exists('cn', $value)){
                array_push($grupos, $value['cn']);
            }
        }
        return $grupos;
    }
    
    private function modificarGruposAdicionales($usuarioGrupos, $usuario, $claves){
        $resultado = array();
        $uid = $usuario->getUid();
        $gruposActuales = $this->listarGruposUsuarios($usuario->getDNBase(), $usuario->getUid());
        foreach ($usuarioGrupos as $value) {
            if(!in_array($value, $gruposActuales)){
                $valores['memberuid'] = $uid;
        	$grupu = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
        	$grupu->setCn($value);
                if (($retorno = $grupu->agregarAtributos($grupu->getDNEntrada(), $valores))) {
                    array_push($resultado, "Agregado $uid a ".  $grupu->getDNEntrada());
                }else{
                    array_push($resultado, "Error agregando $uid a ".  $grupu->getDNEntrada() . ": " . $grupu->mostrarERROR() );
                }
            }
        }
        foreach ($gruposActuales as $value) {
            if(!in_array($value, $usuarioGrupos)){
                $valores['memberuid'] = $uid;
        	$grupu = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
        	$grupu->setCn($value);
                
                if (($retorno = $grupu->removerAtributos($grupu->getDNEntrada(), $valores))) {
                    array_push($resultado, "Eliminado $uid de ". $grupu->getDNEntrada());
                }else{
                    array_push($resultado, "Error eliminando $uid en ".  $grupu->getDNEntrada() . ": " . $grupu->mostrarERROR() );
                }
                
            }
        }
        return $resultado;
    }
    
    private function moverEnGrupo($usuario, $usuarioGrupo){
//        $grupo = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
        
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $grupo->setGidNumber($usuarioGrupo);
        
        $ou = new \Modelos\organizationUnit($this->dn, $this->pswd);
        $ou->setOu($grupo->getCn());
        $ou->getEntrada();
        if($usuario->moverEntrada($usuario->getDNEntrada(), $ou->getDNEntrada())){
            $mensaje = "El usuario $usuario ahora esta bajo  " . $ou->getDNEntrada();
        }else{
            $mensaje = $this->mostrarERROR();
        }
        return $mensaje;
    }
    

        public function modificarUsuario(){
        $this->comprobar($this->pagina);         
        // Modificaciones de los grupos de usuario
        $usuarioGrupo = $this->index->get('POST.grupouser');
        $usuarioGrupos = $this->index->get('POST.grupos');
        // Modificaciones de los datos de usuario
        $usuarioCargo = $this->index->get('POST.cargo');
        $usuarioPhone = $this->index->get('POST.phone');
        $usuarioNombre = $this->index->get('POST.nameuser');
        $usuarioOficina = $this->index->get('POST.oficina');
        $usuarioApellido = $this->index->get('POST.apelluser');
        $usuarioModificar = $this->index->get('POST.usermod');
        $usuarioLocalidad = $this->index->get('POST.localidad');
        
        $claves = $this->getClaves();
        
        $resultado = array();
        
        // ¿Debe moverse el usuario a un objeto ou de grupo bajo la rama ou=Users?
        if ($this->configuracion['grupos_ou']) {
            $usuario = new \Modelos\userSamba($claves['dn'], $claves['pswd']);
            $usuario->setUid($usuarioModificar);
            $resultado['move_ou'] = $resultadoMover = $this->moverEnGrupo($usuario, $usuarioGrupo);
        }
        
        $usuario = new \Modelos\userSamba($claves['dn'], $claves['pswd']);
        $usuario->setUid($usuarioModificar);
        
        $usuario->setO($usuarioLocalidad);
        $usuario->setOu($usuarioOficina);
        $usuario->setTitle($usuarioCargo);
        $usuario->setGidNumber($usuarioGrupo);
        $usuario->configuraNombre($usuarioNombre, $usuarioApellido);
        $usuario->setTelephoneNumber($usuarioPhone);
        
        $resultado['actualizar_entrada'] = $usuario->actualizarEntrada();
        $resultado['modificar_grupos_adicionales'] = $this->modificarGruposAdicionales($usuarioGrupos, $usuario, $claves);
        
        //TODO: Empezar a crear la plantilla para esta, ya es hora y pasada de hecho
        foreach ($resultado as $value) {
            print_r($value);
            print "<br>";
        }
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
            'cargo' => $usuario->getTitle(),
            'grupos' => $this->listarGrupos($usuario->getDNBase()),
            'usermod' => $usuarioCliente,
            'oficina' => $usuario->getOu(),
            'nameuser' => $usuario->getGivenName(),
            'telefono' => $usuario->getTelephoneNumber(),
            'grupouser' => $grupo->getCn(),
            'apelluser' => $usuario->getSn(),
            'localidad' => $usuario->getO(),
            'gruposuser' => $this->listarGruposUsuarios($usuario->getDNBase(), $usuario->getUid()),
            'buzonstatus'=> $mailbox->getZimbraMailStatus(),
            'cuentastatus'=> $mailbox->getZimbraAccountStatus(),
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
