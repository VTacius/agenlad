<?php
namespace controladores;
class useraddControl extends \clases\sesion{
    
    public function __construct() {
        parent::__construct();
        $this->pagina = "useradd";
        $this->error = array();
        $this->mensaje = array();
    }
    
    /**
     * Cuando los usuarios deban residir bajo la unidades organizativa del grupo 
     * TODO: Exactamente esto podría retonar un valor válido para usermodControl::moverEnGrupo
     * Habría que probar esto con hacienda
     * @param string $usuario
     * @return string
     */
    public function obtenerDnGrupoOu($gidNumber){
        // TODO: Dentro de las pruebas, si obtiene el grupo correcto
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $grupo->setGidNumber($gidNumber);

        $ou = new \Modelos\organizationUnit($this->dn, $this->pswd);
        $ou->setOu($grupo->getCn());
        $ou->getEntrada();
        return $ou->getDNEntrada();
    }
    
    /**
     * Busca un atributo para todos los usuarios en la base ldap Central
     * @param type $atributo
     * @return type
     */
    private function listarAtributosUsuarios($atributo){
        $usuarios = new \Modelos\userPosix($this->dn, $this->pswd, 'central' );
        $filtro =array($atributo => '*');
        $datos = $usuarios->search($filtro);
        $lista = array();
        foreach ($datos as $valor) {
            $lista[] = $valor[$atributo];
        }
        return $lista; 
    }
    
    /**
     * TODO: Copiado descaradamente de usermodControl
     * @param type $base
     * @return type
     */
    private function listarGrupos($base = "A" ){
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $search = array('cn'=>'*');
        return $grupo->search($search, array('gidNumber'), $base);
    }
    
    /**
     * ¿Existe el pretendido nuevo usuario dentro de LDAP?
     * @param string $user
     * @return boolean
     */
    private function checkSamba($user){
        $usuario = new \Modelos\userPosix($this->dn, $this->pswd, 'central' );
        $usuario->setUid($user);
        if ($usuario->getGidNumber() === "{empty}"){
            return true;
        }else{
            return false;
        }
    }

    /**
     *  ¿Existe el pretendido nuevo usuario dentro de Zimbra?
     * TODO: Que busque en Zimbra también
     * @param type $user
     * @return boolean
     */
    private function checkZimbra($user){
        return true;
    }

    protected function checharUsuario($user){
        
        if ($this->checkSamba($user) && $this->checkZimbra($user)) {
            $retorno =  TRUE;
        }else{
            $retorno = FALSE;
        }
        return $retorno;
    }
    
    /**
     * El formulario ejecuta esto para comprobar que el usuario es nuevo
     */
    public function checkUid(){
        $user = $this->index->get('POST.uid');
        if ($this->checharUsuario($user)) {
            $mensaje = "Usuario Disponible";
            $clase = true;
        }else{
            $mensaje = "Usuario en uso";
            $clase = false;
        }
        print json_encode(array('mensaje'=>$mensaje, 'clase'=>$clase));
    }

    protected function getUidNumber($listaUidNumberUsuarios){
        sort($listaUidNumberUsuarios);
        $lastIndex = sizeof($listaUidNumberUsuarios) - 2;
        $lastElemento = $listaUidNumberUsuarios[$lastIndex];
        $elemento = $lastElemento + 1;
//        for ($i=1000; $i<=$lastElemento; $i++){
//            if (in_array($elemento, $listaUidUsuarios)) {
//                print "<br>Elemento: $i";
//            }else{
//                print "<br>$i es nuevo, creo";
//            }
//        }
        return (string)$elemento;
    }
    
    protected function configurarNuevoUsuario($dn, $claves, $uid, $nombre, 
            $apellido, $localidad, $oficina, $cargo, $uidNumber, $loginShell,
            $gidNumber,$sambaAcctFlags){
        $usuario = new \Modelos\userSamba($claves['dn'], 'lector_ldap_hacienda');
//        $usuario = new \Modelos\userSamba($claves['dn'], $claves['pswd']);
        $usuario->setUid($uid);
        $usuario->configuraNombre($nombre, $apellido);
        $usuario->configuraPassword($uid);
        
        //Estos son atributos definitorios
        $usuario->setO($localidad);
        $usuario->setOu($oficina);
        $usuario->setTitle($cargo);
        
        //Atributos Posix con cierto detalle en cuanto a su configuracion
        $usuario->setUidNumber($uidNumber);
        $usuario->setLoginShell($loginShell);
        $usuario->setGidNumber($gidNumber);
        
        // Atributos administrativos Posix
        $usuario->setSambaAcctFlags($sambaAcctFlags);
        $usuario->setShadowLastChange('16144');
        $usuario->setShadowMax('99999');
        
        //Atributos administrativos samba
        $usuario->setSambaKickoffTime('2147483647');
        $usuario->setSambaLogoffTime('2147483647');
        $usuario->setSambaLogonTime('0');
        
        //Cuidado con estos administrativos samba respecto al password 
        $usuario->setSambaPwdCanChange('0');
        //Parece que con esto se refiere al hecho que no lo ha cambiado antes, 
        //no todos los usuarios lo tienen configurado a decir verdad
        $usuario->setSambaPwdLastSet('0');
        //Este lo escojo al azar, porque no encuentro cero para nadie
        $usuario->setSambaPwdMustChange('2147483647');
        
        if ($usuario->crearEntrada($dn)) {
            $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Agregado usuario $uid");
        }else{
            $this->error[] = $usuario->getErrorLdap();
            $this->mensaje[] = array("codigo" => "danger", 'mensaje' => "Error agregando al usuario $uid. Revise el mensaje de error asociado abajo");
        }
        
    }
    
    public function creacionUsuario(){
        $this->pagina = "main";
        $this->comprobar($this->pagina); 
        // Los datos enviados desde el formulario de creacion
        $uid = $this->index->get('POST.uid');
        // SI los dos a continuacion están vacions, al menos que pueda usar los de respaldo
        $nombre = $this->index->get('POST.nombre');
        $pre_apellido = $this->index->get('POST.apellido');
        $apellido = (empty($nombre) && empty($pre_apellido))? $uid : $pre_apellido;
        $cargo = $this->index->get('POST.title');
        $oficina = $this->index->get('POST.ou');
        $localidad = $this->index->get('POST.o');
        $gidNumber = $this->index->get('POST.gidNumber');
        $loginShell = $this->index->get('POST.loginShell');
        
        $listaUidNumberUsuarios = $this->listarAtributosUsuarios("uidNumber");
        
        $uidNumber = $this->getUidNumber($listaUidNumberUsuarios);
        $sambaAcctFlags = $this->index->get('POST.sambaAcctFlags');
        // Las credenciales de administrador para su base LDAP
        $claves = $this->getClaves();
        // El dn de la nueva entrada
        $configuracion = $this->getConfiguracionDominio();
        // ¿Debe moverse el usuario a un objeto ou de grupo bajo la rama ou=Users?        
        if ($configuracion['grupos_ou']) {
            $dn = "uid=$uid,{$this->obtenerDnGrupoOu($gidNumber)}";
        }else{
            $dn = "uid=$uid,{$configuracion['base_usuario']}";
            
        }
        $this->configurarNuevoUsuario($dn, $claves, $uid, $nombre, 
            $apellido, $localidad, $oficina, $cargo, $uidNumber, $loginShell,
            $gidNumber,$sambaAcctFlags);
        $resultado = array(
            'mensaje' => $this->mensaje,
            'error' => $this->error
        );
        print json_encode($resultado);
    }
    
    public function display() {
        $this->pagina = "main";
        $this->comprobar($this->pagina); 
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina);
        $this->parametros['datos']['grupos'] = $this->listarGrupos();
        echo $this->twig->render('useradd.html.twig', $this->parametros);       

    }
}
