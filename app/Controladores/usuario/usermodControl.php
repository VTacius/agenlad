<?php

namespace App\Controladores\usuario;

use App\Acceso\ldapAccess;
use App\Modelos\userSamba;
use App\Modelos\userPosix;
use App\Modelos\grupoSamba;
use App\Clases\BaseControladorAuth;
use App\Clases\Cifrado;

class usermodControl extends BaseControladorAuth { 
    
    public function modificarDatosAdministrativos($usuario, $nit){
        /**
         *  
         * TODO: Te lo dejo porque creo que en cualquier momento querrán que lo implementes,
         * igual te lo copias de userActualizacion.php y ya estuvo
         * $fecha = (empty($fecha_nacimiento)) ? '12/02/1809' : $fecha_nacimiento;
         * $date = \DateTime::createFromFormat('d/m/Y', $fecha);
         * $dato_fecha = $date->format('Y/m/d');
         */
        $sentencia = array(
            'update' => 'update datos_administrativos set nit=:nit where usuario=:usuario',
            'insert' => 'insert into datos_administrativos(usuario,nit) values(:usuario,:nit)',
            'valores' => array('usuario'=> $usuario, 'nit'=>$nit)
        );
        $entrada = $this->obtenerDatosAdministrativos($usuario);
        try{
            $base = $this->index->get('dbconexion');
            if (count($entrada) > 0){
                $entrada = $base->exec($sentencia['update'], $sentencia['valores']);
                $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Actualizados los datos administrativos para usuario " .  $usuario);
            }else{
                $entrada = $base->exec($sentencia['insert'], $sentencia['valores']);
                $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Agregados los datos administrativos para usuario " .  $usuario);
            }
        }catch (\PDOException $e){
            // Lo pones en el mensaje de error a enviar al servidor
            $this->mensaje[] = array("codigo" => "danger", 'mensaje' => 'Error agregando datos administrativos. Revise los mensajes asociados');
            $this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: " . $e->getMessage() . " " . $dato_fecha . " " . $fecha);
            // $this->error[] = array('titulo' => "Error de aplicación", 'mensaje' => "Error manipulando base de datos: ");
        }
    }

    /**
     * Lista todos los grupos que existen dentro del dominio dado
     * TODO: Hay una copia descarada en useraddControl
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
            $this->agregarEnGrupo($uid, $grupo, $claves);
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

            if ($grupu->removerAtributos($grupu->getDNEntrada(), $valores)) {
                $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Eliminado $uid de ". $grupu->getDNEntrada());
            }else{
                $this->error[] = $grupu->getErrorLdap();
                $this->mensaje[] = array("codigo" => "danger", 'mensaje' => "Error eliminando $uid en ".  $grupu->getDNEntrada() . ". Revise los mensajes asociados ");
            }
                
        }
    }

    private function agregarEnGrupo($uid, $grupo, $claves){
            $valores['memberuid'] = $uid;
            $grupu = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
            $grupu->setCn($grupo);

            if ($grupu->agregarAtributos($grupu->getDNEntrada(), $valores)) {
                $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Agregado $uid de ". $grupu->getDNEntrada());
            }else{
                $this->error[] = $grupu->getErrorLdap();
                $this->mensaje[] = array("codigo" => "danger", 'mensaje' => "Error agregando $uid en ".  $grupu->getDNEntrada() . ". Revise los mensajes asociados ");
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
        
        //Agrego el grupo principal a la lista
        $principal = new \Modelos\grupoSamba($claves['dn'], $claves['pswd']);
        $principal->setGidNumber($usuario->getGidNumber());
        $grupoPrincipal = $principal->getCn();
        if (array_search($grupoPrincipal, $usuarioGrupos) === false || array_search($grupoPrincipal, $gruposActuales) === false) {
            array_push($usuarioGrupos, $grupoPrincipal);
        }
        
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
     * TODO: Por allí anda una en la creacion de usuarios algo que podr{ias usar
     * @param userSamba $usuario
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
            $this->mensaje[] = array("codigo" => "success", 'mensaje' => "El usuario ahora esta bajo  " . $ou->getDNEntrada());
        }else{
            $this->error[] = $usuario->getErrorLdap();
            $this->mensaje[] = array("codigo" => "danger", 'mensaje' => "El movimiento de usuario ha sufrido un error. Revise los mensajes asociados");
        }
    }
    
    /**
     * Actualizamos los atributos de $usuario con los datos contenidos en 
     * $usuarioAttr, que formamos con los datos obtenidos del formulario
     * @param userSamba $usuario
     * @param string $usuarioAttr
     * @return string
     */
    protected function modificarAttrUsuario($usuario, $usuarioAttr){
        $usuario->setUid($usuarioAttr['usuarioModificar']);
        $usuario->setO($usuarioAttr['usuarioLocalidad']);
        $usuario->setOu($usuarioAttr['usuarioOficina']);
        $usuario->setTitle($usuarioAttr['usuarioCargo']);
        $usuario->setGidNumber($usuarioAttr['usuarioGrupo']);
        $usuario->configuraNombre($usuarioAttr['usuarioNombre'], $usuarioAttr['usuarioApellido']);
        $usuario->setTelephoneNumber($usuarioAttr['usuarioPhone']);

        // Agrego set condicionados a que ese atributo haya sido configurado, 
        // manteniendo compatibilidad con la parte más legacy de la aplicacion
        if ( array_key_exists('usuarioDescripcion', $usuarioAttr)){
            $usuario->setDescription($usuarioAttr['usuarioDescripcion']);
        }

        // Agrego set condicionado a atributo usuarioNivel
        if ( array_key_exists('usuarioNivel', $usuarioAttr)){
            $usuario->setSt($usuarioAttr['usuarioNivel']);
        }

        $configuracion = $this->getConfiguracionDominio();
        
        // ¿Debe moverse el usuario a un objeto ou de grupo bajo la rama ou=Users?        
        if ($configuracion['grupos_ou']) {
            $this->moverEnGrupo($usuario, $usuarioAttr['usuarioGrupo']);
        }
        
        if ($usuario->actualizarEntrada()) {
            $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Usuario {$usuarioAttr['usuarioModificar']} ha sido modificado con éxito");
        }else{
            $this->error[] = $usuario->getErrorLdap();
            $this->mensaje[] = array("codigo" => "danger", 'mensaje' => "La modificación de atributos ha sufrido un error. Revise los mensajes asociados");
        }
    }
    
    protected function modificarUsuarioZimbra($correo, $usuarioAttr){
        $clavez = $this->getClavez();
        $userZimbra = new \Modelos\mailbox($clavez['dn'], $clavez['pswd']);
        $userZimbra->cuenta($correo);
        $mensaje = $userZimbra->getErrorSoap(); 
        if ($userZimbra->getMail() !== "{empty}") {
            $userZimbra->setOu($usuarioAttr['usuarioOficina']);
            $userZimbra->setTitle($usuarioAttr['usuarioCargo']);
            $userZimbra->setCompany($usuarioAttr['usuarioLocalidad']);
            $userZimbra->configuraNombre($usuarioAttr['usuarioNombre'], $usuarioAttr['usuarioApellido']);
            $userZimbra->setTelephoneNumber($usuarioAttr['usuarioPhone']);

            // Agrego set condicionados a que ese atributo haya sido configurado, 
            // manteniendo compatibilidad con la parte más legacy de la aplicacion
            if ( array_key_exists('usuarioDescripcion', $usuarioAttr)){
                $userZimbra->setDescription($usuarioAttr['usuarioDescripcion']);
            }

            // Agrego set condicionado a atributo usuarioNivel
            if ( array_key_exists('usuarioNivel', $usuarioAttr)){
                $userZimbra->setSt($usuarioAttr['usuarioNivel']);
            }

            //TODO: Tenés que hacer que este metodo se parezca al de usuario
            //NOTA: Posiblemente no sea del todo necesario si te fijas en el otro de 
            //allá abajo en modificarEstado Zimbra
            $userZimbra->actualizarEntrada();
            $mensaje = $userZimbra->getErrorSoap();

            // Configuramos los mensajes de la operacion
            //TODO: Sigo pensando que debo cambiar esto
            if (empty($mensaje)) {
                $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Cambio exitoso para buzón de $correo");
            }else{
                $this->mensaje[] = array("codigo" => "danger", 'mensaje' => "Los cambios en el buzón para $correo han fallado");
                $this->error[] = "Un error ha ocurrido al modificar datos en el servidor de correo " . $mensaje;
            }
            // Necesitamos un pequeño delay, para evitar que accidentalmente se envíen modificaciones demasiado frecuentes sobre el mismo objeto
            sleep(1);
        }else{
            // Te agradecería que comentaras esto cuando estes en produccion
            $this->mensaje[] = array("codigo" => "warning", 'mensaje' => "No existe un buzón asociado a {$usuarioAttr['usuarioModificar']}");
            $this->error[] = array('mensaje' => "Un error ha ocurrido al modificar datos en el servidor de correo " . $mensaje);
        }
      
    }
    
    /**
     * Usada por modificarBuzon()
     * @param array $clavez
     * @param string $operacion cuentastatus, buzonstatus
     * @param string $estado
     * @param string $usuario
     */
    private function modificarEstadoZimbra($clavez, $operacion, $estado, $usuario){
        $mailbox = new \Modelos\mailbox($clavez['dn'], $clavez['pswd']);
        $mailbox->cuenta($usuario);
        if ($operacion == 'cuenta') {
            $estatuto = (strtolower($estado)==="active") ? 'active': 'locked'; 
            $mailbox->setZimbraAccountStatus($estatuto);
        }elseif($operacion == 'buzon'){
            $estatuto = (strtolower($estado)==="enabled") ? 'enabled': 'disabled'; 
            $mailbox->setZimbraMailStatus($estatuto);
        }
        // Realizamos la operacion
        $mailbox->actualizarEntrada();
        $mensaje = $mailbox->getErrorSoap();
        
        // Obtenemos los mensajes
        if (empty($mensaje)) {
            $this->mensaje[] = array("codigo" => "success", 'mensaje' => "Estatus cambiado a $estatuto para $usuario");
        }else{
            $this->mensaje[] = array("codigo" => "danger", 'mensaje' => "La operacion ha fallado para $usuario");
        }
    }
    
    /**
     * Punto de entrada para la ruta /usermod/zimbra
     */
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

        $resultado = array(
            'mensaje'=>$this->mensaje,
            'error'=>$this->error,
            'datos'=>$this->datos        
        );
        // Recuerdo algo dejar un poco de tiempo a zimbra entre cada uso
        sleep(1);
        print json_encode($resultado);
    }
   
    private function requerir(){
        return Array(
            'title' => Array('validaciones' => []),
            'telephoneNumber' => Array('validaciones' => []),
            'gidNumber' => Array('validaciones' => []),
            'givenName' => Array('validaciones' => []),
            'ou' => Array('validaciones' => []),
            'sn' => Array('validaciones' => []),
            'uid' => Array('validaciones' => []),
            'o' => Array('validaciones' => []),
        );
    }

    public function modificarUsuario($index){
        
        try {
            $requerimientos = $this->requerir();
            $datos = $this->parsearDatosPeticion($requerimientos, $index['BODY']);
        } catch (\Exception $e){
            $index->error(400);
        }
        $datos['description'] = "USERMODWEBADMIN";
        
        list($parametros, $credenciales) = $this->obtenerParametros($ldapParams);
        $cifrador = new Cifrado();
        $conexion = new ldapAccess($parametros, $credenciales);
        $usuario = new userSamba($conexion, $cifrador);
        
        print_r($datos);
        /*
        // Modificamos los atributos del usuario
        $this->modificarAttrUsuario($usuario, $datos);
        
        // Modificaciones de los grupos de usuario
        $userGrupos = $this->index->get('POST.grupos');
        $usuarioGrupos = empty($userGrupos) ? array() : $userGrupos;  
        
        $nit = $this->index->get('POST.nit');
              
        //Modificamos la entrada del usuario en Zimbra si es que acaso existe
        $correo = $usuario->getMail();
        
        $this->modificarUsuarioZimbra($correo, $datos);
       
        $this->modificarGruposAdicionales($usuarioGrupos, $usuario, $claves);

        $this->modificarDatosAdministrativos($datos['usuarioModificar'], $nit);
        
        $resultado = array(
            'mensaje' => $this->mensaje,
            'error' => $this->error
        );
        */
        
    }
    
}
