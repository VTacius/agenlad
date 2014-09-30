<?php
namespace controladores;
class usermodControl extends \clases\sesion {
    
    protected $error = array();
    protected $mensajes = array();
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "usermod";
        $this->parametros['pagina'] = $this->pagina;
        $this->configuracion = $this->getConfiguracionDominio();
    }
    
    /**
     * 
     * @param string $base
     * @return array
     */
    private function listarGrupos($base){
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $search = array('cn'=>'*');
        return $grupo->search($search, array('gidNumber'), $base);
    }
    
    /**
     * Lista los grupos a los que pertenece el usuario
     * @param string $base
     * @param string $memberUid
     * @return array
     */
    private function listarGruposUsuarios($base, $memberUid){
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $search = array('memberUid'=>$memberUid);	
        $resultado = $grupo->search($search, array('cn'), $base);
        $grupos = array();
        foreach ($resultado as $value) {
            if 	(array_key_exists('cn', $value)){
                array_push($grupos, $value['cn']);
            }
        }
        return $grupos;
    }
    
    private function anadirGruposAdicionales($uid, $grupo, $gruposActuales, $claves){
        if(!in_array($grupo, $gruposActuales)){
            $valores['memberuid'] = $uid;
            $grupu = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
            $grupu->setCn($grupo);
            if (($retorno = $grupu->agregarAtributos($grupu->getDNEntrada(), $valores))) {
                $this->mensajes[] =  "Agregado $uid a ".  $grupu->getDNEntrada();
            }else{
                $this->error[] = $grupu->getErrorLdap();
                return "Error agregando $uid a ".  $grupu->getDNEntrada() . ". Revise los mensajes asociados ";
            }
        }
    }
    
    private function removerGruposAdicionales($uid, $grupo, $usuarioGrupos, $claves){
        if(!in_array($grupo, $usuarioGrupos)){
            $valores['memberuid'] = $uid;
            $grupu = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
            $grupu->setCn($grupo);

            if (($retorno = $grupu->removerAtributos($grupu->getDNEntrada(), $valores))) {
                $this->mensajes[] = "Eliminado $uid de ". $grupu->getDNEntrada();
            }else{
                $this->error[] = $grupu->getErrorLdap();
                return "Error eliminando $uid en ".  $grupu->getDNEntrada() . ". Revise los mensajes asociados ";
            }
                
        }
    }

    /**
     * 
     * @param array $usuarioGrupos Los grupos que el formulario nos envia para
     * configurar como los nuevos grupos adicionales del usuario
     * @param string $usuario
     * @param array $claves
     * @return type
     */
    protected function modificarGruposAdicionales($usuarioGrupos, $usuario, $claves){
        $uid = $usuario->getUid();
        $gruposActuales = $this->listarGruposUsuarios($usuario->getDNBase(), $usuario->getUid());
        foreach ($usuarioGrupos as $grupo) {
            $this->anadirGruposAdicionales($usuario->getUid(), $grupo, $gruposActuales, $claves);
        }
        foreach ($gruposActuales as $grupo) {
            $this->removerGruposAdicionales($uid, $grupo, $usuarioGrupos, $claves);
        }
    }
    
    /**
     * Si el atributo grupos_ou se encuentra verficado como verdadero, se 
     * procede a mover al usuario a una nueva rama organizativa
     * @param \Modelos\userSamba $usuario
     * @param string $usuarioGrupo
     * @return string
     */
    private function moverEnGrupo($usuario, $usuarioGrupo){
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $grupo->setGidNumber($usuarioGrupo);
        
        $ou = new \Modelos\organizationUnit($this->dn, $this->pswd);
        $ou->setOu($grupo->getCn());
        $ou->getEntrada();
        if($usuario->moverEntrada($usuario->getDNEntrada(), $ou->getDNEntrada())){
            $this->mensajes[] = "El usuario $usuario ahora esta bajo  " . $ou->getDNEntrada();
        }else{
            $this->error[] = $usuario->getErrorLdap();
            $this->mensajes[] = "El movimiento de usuario ha sufrido un error. Revise los mensajes asociados";
        }
    }
    
    private function modificarCuentaZimbra($usuario){
        $mailbox = new \Modelos\mailbox($clavez['dn'], $clavez['pswd']);
        $mailbox->cuenta($usuario);
    }
    
    /**
     * Actualizamos los atributos del usuario
     * @param string $usuario
     * @param string $usuarioAttr
     * @return string
     */
    private function modificarAttrUsuario($usuario, $usuarioAttr){
        $usuario->setUid($usuarioAttr['usuarioModificar']);
        $usuario->setO($usuarioAttr['usuarioLocalidad']);
        $usuario->setOu($usuarioAttr['usuarioOficina']);
        $usuario->setTitle($usuarioAttr['usuarioCargo']);
        $usuario->setGidNumber($usuarioAttr['usuarioGrupo']);
        $usuario->configuraNombre($usuarioAttr['usuarioNombre'], $usuarioAttr['usuarioApellido']);
        $usuario->setTelephoneNumber($usuarioAttr['usuarioPhone']);
        // ¿Debe moverse el usuario a un objeto ou de grupo bajo la rama ou=Users?
        if ($this->configuracion['grupos_ou']) {
            $this->moverEnGrupo($usuario, $usuarioAttr['usuarioGrupo']);
        }
        
        if ($usuario->actualizarEntrada()) {
            $this->mensajes[] = "Usuario {$usuarioAttr['usuarioModificar']} ha sido modificado con éxito";
        }else{
            $this->error[] = $usuario->getErrorLdap();
            $this->mensajes[] = "La modificación de atributos ha sufrido un error. Revise los mensajes asociados";
        }
    }
    
    public function modificarUsuario(){
        $this->comprobar($this->pagina);         
        // Modificaciones de los grupos de usuario
        $usuarioGrupos = $this->index->get('POST.grupos');
        // Modificaciones de los datos de usuario
        $usuarioAttr = array(
            'usuarioCargo' => $this->index->get('POST.cargo'),
            'usuarioPhone' => $this->index->get('POST.phone'),
            'usuarioGrupo' => $this->index->get('POST.grupouser'),
            'usuarioNombre' => $this->index->get('POST.nameuser'),
            'usuarioOficina' => $this->index->get('POST.oficina'),
            'usuarioApellido' => $this->index->get('POST.apelluser'),
            'usuarioModificar' => $this->index->get('POST.usermod'),
            'usuarioLocalidad' => $this->index->get('POST.localidad')
        );
        
        $claves = $this->getClaves();       
        $usuario = new \Modelos\userSamba($claves['dn'], $claves['pswd']);
        // Modificamos los atributos del usuario
        $this->modificarAttrUsuario($usuario, $usuarioAttr);       
        
        $this->modificarCuentaZimbra($usuarioAttr['usuarioModificar']);
        
        $this->modificarGruposAdicionales($usuarioGrupos, $usuario, $claves);
        
        $resultado = array_merge($this->error, array('datos'=> $this->mensajes) );
        
        print json_encode($resultado);
    }

    public function mostrarUsuarioPost(){
        $this->comprobar($this->pagina);     

        $usuarioCliente = $this->index->get('POST.usuarioModificar');
	$resultado = $this->mostrarUsuario($usuarioCliente);
        print json_encode($resultado);
	
    }
    
    public function mostrarUsuarioGet(){
        $this->comprobar($this->pagina);
        $usuarioCliente = $this->index->get('PARAMS.usuarioModificar');
	$resultado = $this->mostrarUsuario($usuarioCliente);
        $this->parametros['datos'] = $resultado;
        
        echo $this->twig->render('usermod.html.twig', $this->parametros);       
	
    }

    private function mostrarUsuario($usuarioCliente){
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
        $mailbox->cuenta($usuarioCliente);
        
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
        
        $errores = array_merge(
                array("errorLdap" => $usuario->getErrorLdap()),
                array("errorGrupo" => $grupo->getErrorLdap()),
                array("errorZimbra" => $mailbox->getErrorSoap())
        );
       
	return array_merge($datos, $errores);
    }
    
    public function display(){
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina);     
        echo $this->twig->render('usermod.html.twig', $this->parametros);       
    }
}
