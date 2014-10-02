<?php
namespace controladores;
class usermodControl extends \controladores\usershowControl {
    
   
    protected $error = array();
    protected $datos = array();
    protected $mensaje = array();
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "usermod";
        $this->parametros['pagina'] = $this->pagina;
    }
    
    /**
     * Lista todos los grupos que existen dentro del dominio dado
     * @param string $base
     * @return array
     */
    private function listarGrupos($base = "A" ){
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
            if  (array_key_exists('cn', $value)){
                array_push($grupos, $value['cn']);
            }
        }
        return $grupos;
    }
    
    /**
     * Si $grupo (De la lista de grupos enviados desde el formulario) no existe 
     * en $gruposActuales (De la lista de actuales), entonces agregamos $uid a 
     * ese grupo
     * @param string $uid
     * @param string $grupo
     * @param array $gruposActuales
     * @param array $claves
     */
    private function anadirGruposAdicionales($uid, $grupo, $gruposActuales, $claves){
        if(!in_array($grupo, $gruposActuales)){
            $valores['memberuid'] = $uid;
            $grupu = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
            $grupu->setCn($grupo);
            if (($retorno = $grupu->agregarAtributos($grupu->getDNEntrada(), $valores))) {
                $this->mensajes[] =  "Agregado $uid a ".  $grupu->getDNEntrada();
            }else{
                $this->error[] = $grupu->getErrorLdap();
                $this->mensajes[] = "Error agregando $uid a ".  $grupu->getDNEntrada() . ". Revise los mensajes asociados ";
            }
        }
    }
    
    /**
     * Si $grupo (De la lista de grupos actuales) no existe en $usuarioGrupos 
     * (De la lista de grupos enviados desde el formulario), entonces removemos 
     * a $uid de ese grupo
     * @param string $uid
     * @param string $grupo
     * @param array $usuarioGrupos
     * @param array $claves
     */
    private function removerGruposAdicionales($uid, $grupo, $usuarioGrupos, $claves){
        if(!in_array($grupo, $usuarioGrupos)){
            $valores['memberuid'] = $uid;
            $grupu = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
            $grupu->setCn($grupo);

            if (($retorno = $grupu->removerAtributos($grupu->getDNEntrada(), $valores))) {
                $this->mensajes[] = "Eliminado $uid de ". $grupu->getDNEntrada();
            }else{
                $this->error[] = $grupu->getErrorLdap();
                $this->mensajes[] = "Error eliminando $uid en ".  $grupu->getDNEntrada() . ". Revise los mensajes asociados ";
            }
                
        }
    }

    /**
     * 
     * configurar como los nuevos grupos adicionales del usuario
     * @param array $usuarioGrupos Los grupos que el formulario nos envia
     * @param string $usuario
     * @param array $claves
     * @return type
     */
    private function modificarGruposAdicionales($usuarioGrupos, $usuario, $claves){
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
    
    /**
     * Actualizamos los atributos de $usuario con los datos contenidos en 
     * $usuarioAttr, que formamos con los datos obtenidos del formulario
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
        $configuracion = $this->getConfiguracionDominio();
        // ¿Debe moverse el usuario a un objeto ou de grupo bajo la rama ou=Users?
        
        
        if ($configuracion['grupos_ou']) {
            $this->moverEnGrupo($usuario, $usuarioAttr['usuarioGrupo']);
        }
        
        if ($usuario->actualizarEntrada()) {
            $this->mensajes[] = "Usuario {$usuarioAttr['usuarioModificar']} ha sido modificado con éxito";
        }else{
            $this->error[] = $usuario->getErrorLdap();
            $this->mensajes[] = "La modificación de atributos ha sufrido un error. Revise los mensajes asociados";
        }
    }
    
    private function modificarUsuarioZimbra($correo, $usuarioAttr){
        $clavez = $this->getClavez();
        $userZimbra = new \Modelos\mailbox($clavez['dn'], $clavez['pswd']);
        $userZimbra->cuenta($correo);
        $userZimbra->setOu($usuarioAttr['usuarioOficina']);
        $userZimbra->setTitle($usuarioAttr['usuarioCargo']);
        $userZimbra->setCompany($usuarioAttr['usuarioLocalidad']);
        $userZimbra->configuraNombre($usuarioAttr['usuarioNombre'], $usuarioAttr['usuarioApellido']);
        $userZimbra->setTelephoneNumber($usuarioAttr['usuarioPhone']);
        
        //TODO: Tenés que hacer que este metodo se parezca al de usuario
        $userZimbra->actualizarEntrada();
        $msg = $userZimbra->getLastResponse();
        
        // Obtenemos los mensajes
        $this->mensajes[] = empty($msg) ? "Los cambios para $correo han fallado": "Cambio exitoso para entrada en zimbra de $correo";
    }
    
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
        //Modificamos la entrada del usuario en Zimbra
        $correo = $usuario->getMail();
        $this->modificarUsuarioZimbra($correo, $usuarioAttr);
       
        $this->modificarGruposAdicionales($usuarioGrupos, $usuario, $claves);
        
        $resultado = array(
            'mensaje' => $this->mensajes,
            'error' => $this->error
        );
        
        print json_encode($resultado);
    }
    
    /**
     * Retorna datos para usuario al script 
     */
    public function mostrarUsuarioPost(){
        $this->comprobar($this->pagina);     
        $usuarioCliente = $this->index->get('POST.usuarioModificar');
        $this->mostrarUsuario($usuarioCliente);

        $resultado = array(
            'mensaje'=>$this->mensaje,
            'error'=>$this->error,
            'datos'=>$this->datos        
        );
        print json_encode($resultado);  
    }
    
    public function mostrarUsuarioGet(){
        $this->comprobar($this->pagina);
        $usuarioCliente = $this->index->get('PARAMS.usuarioModificar');
        $resultado = $this->mostrarUsuario($usuarioCliente);
        $this->parametros['datos'] = $resultado;
        
        echo $this->twig->render('usermod.html.twig', $this->parametros);       
    
    }
   
    /**
     * Los métodos aca usados provienen de usershowControl()
     * Es la forma más certera para ahorar toneladas de código
     * @param string $usuarioCliente
     * @return array
     */
    protected function mostrarUsuario($usuarioCliente){
        $this->comprobar($this->pagina); 
        
        // Obtenemos las claves para acceder a Soap Zimbra
        $clavez = $this->getClavez();
        
        $usuario = $this->usuario($usuarioCliente);
        
        $this->datos['grupos'] = $this->listarGrupos();
        
        $this->grupo($usuario['grupo']);
        
        $this->mail($clavez, $usuario['correo']);
        
    }


    private function modificarEstadoZimbra($clavez, $operacion, $estado, $usuario){
        $mailbox = new \Modelos\mailbox($clavez['dn'], $clavez['pswd']);
        $mailbox->cuenta($usuario);
        if ($operacion == 'cuentastatus') {
            $estatuto = (strtolower($estado)==="active") ? 'active': 'locked'; 
            $mailbox->setZimbraAccountStatus($estatuto);
        }elseif($operacion == 'buzonstatus'){
            $estatuto = (strtolower($estado)==="enabled") ? 'enabled': 'disabled'; 
            $mailbox->setZimbraMailStatus($estatuto);
        }
        // Realizamos la operacion
        $mailbox->actualizarEntrada();
        $msg = $mailbox->getLastResponse();
        
        // Obtenemos los mensajes
        $this->mensajes[] = empty($msg)? "La operacion ha fallado para $usuario": "Estatus cambiado a $estatuto para $usuario";
        $this->error[] = $mailbox->getErrorSoap();
    }
    
    public function modificarBuzon(){
        // Comprobamos permisos
        $this->comprobar($this->pagina); 
        
        // Obtenemos los datos
        $clavez = $this->getClavez();
        $usuario = $this->index->get('POST.usermod');
        $estado = $this->index->get('POST.textElemento');
        $operacion = $this->index->get('POST.idElemento');
        
        // Mandamos los datos donde puedan usarlos
        $this->modificarEstadoZimbra($clavez, $operacion, $estado, $usuario);
        
        $resultado = array_merge($this->error, array('datos'=> $this->mensajes) );
        // Recuerdo algo dejar un poco de tiempo a zimbra entre cada uso
        sleep(1);
        print json_encode($resultado);
    }
    
    public function display(){
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina);     
        echo $this->twig->render('usermod.html.twig', $this->parametros);       
    }
}
