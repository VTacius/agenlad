<?php
namespace controladores;

/**
 * Controlador para manejo de pruebas
 *
 * @author alortiz
 */
class useraddPrueba extends \clases\sesion{
    public function __construct() {
        parent::__construct();
        $this->pagina = "usershow";
    }
    /**
     * Cuando los usuarios deban residir bajo la unidades organizativa del grupo 
     * TODO: Exactamente esto podría retonar un valor válido para usermodControl::moverEnGrupo
     * Habría que probar esto con hacienda
     * @param string $usuario
     * @return string
     */
    public function obtenerDnGrupoOu($gidNumber){
        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
        $grupo->setGidNumber($gidNumber);
        
        $ou = new \Modelos\organizationUnit($this->dn, $this->pswd);
        $ou->setOu($grupo->getCn());
        $ou->getEntrada();
        return $ou->getDNEntrada();
    }
    
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
    
    // La comprobación de uidNumber se realiza a nivel de dominio particular
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
    
    
    public function checkUid($listaUidUsuarios, $uid){
        return (!in_array($uid, $listaUidUsuarios));
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
            print "<br>Usuario $uid  Creado exitosamente<br>";
        }else{
            print "<br>Algo debió ocurrir y no nos dimos cuenta<br>";
        }
    }
    public function display(){
        $this->pagina = "main";
        $this->comprobar($this->pagina);       

        $lista = $this->listarAtributosUsuarios("uidNumber");
        $claves = $this->getClaves();       
        $valor = $this->index->get('POST.loginShell');
        print "Este es el valor de $valor";
        print_r($this->index->get('POST.loginShell'));
        // Los siguiente valores vienen desde el formulario. 
        // No tienen valor por defecto
//        $nombre = "Gabriela";
//        $apellido = "Henriquez Dimas";
//        // Pueden estar vacíos de hecho
//        $cargo = "Enfermera en Jefe";
//        $oficina = "Enfermería";
//        $localidad = "Dependencia Ejecutora";
//        // Los siguiente no pueden estar vacíos
//        // Necesitan algunas operaciones previas para configurarse
//        // para no confundirse con los locales
//        $gidNumber = '1005'; // Debe buscarse la correspondencia con un grupo real
//        // Pude tener valores por defecto
//        $loginShell = "/bin/bash"; // Es un select en el formulario
//        $sambaAcctFlags = "[U]"; //Es un select en el formulario, indica si esta activo o inactivo
        
        
        $nombre = "";
        $apellido = "Henriquez Dimas";
        // Pueden estar vacíos de hecho
        $cargo = "Enfermera en Jefe";
        $oficina = "Enfermería";
        $localidad = "Dependencia Ejecutora";
        // Los siguiente no pueden estar vacíos
        // Necesitan algunas operaciones previas para configurarse
        // para no confundirse con los locales
        $gidNumber = '1005'; // Debe buscarse la correspondencia con un grupo real
        // Pude tener valores por defecto
        $loginShell = "/bin/bash"; // Es un select en el formulario
        $sambaAcctFlags = "[U]"; //Es un select en el formulario, indica si esta activo o inactivo

        
        //Empezamos a configurar los valores del usuario de prueba
        $uid = "usuario" . rand(1,9) * date("Bis");
        
        $uidNumber = $this->getUidNumber($lista); // Necesita verificarse que sea único y mayor que un numero dado, 
        
//        $uid = $this->index->get('POST.uid'). 'pre' . rand(1,9) * date("Bis");
//        $nombre = $this->index->get('POST.nombre');
//        $apellido = $this->index->get('POST.apellido');
//        $cargo = $this->index->get('POST.title');
//        $oficina = $this->index->get('POST.ou');
//        $localidad = $this->index->get('POST.o');
//        $gidNumber = $this->index->get('POST.gidNumber');
//        $loginShell = $this->index->get('POST.loginShell');
//        $sambaAcctFlags = $this->index->get('POST.sambaAcctFlags');

        /**
         * Haremos esto para nuestra onda de crear dn
         */
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

//        print "<br><br>";
//        print "Creando un usuario Samba<br>";
//        $usuario = new \Pruebas\userSamba($claves['dn'], $claves['pswd']);
//        $usuario->setUid($user);
//        $usuario->configuraNombre($nombre, $apellido);
//        $usuario->configuraPassword($user);
//        
//        //Estos son atributos definitorios
//        $usuario->setO($localidad);
//        $usuario->setOu($oficina);
//        $usuario->setTitle($cargo);
//        
//        //Atributos Posix con cierto detalle en cuanto a su configuracion
//        $usuario->setUidNumber($uidNumber);
//        $usuario->setLoginShell($loginShell);
//        $usuario->setGidNumber($gidNumber);
//        
//        // Atributos administrativos Posix
//        $usuario->setSambaAcctFlags($sambaAcctFlags);
//        $usuario->setShadowLastChange('16144');
//        $usuario->setShadowMax('99999');
//        
//        //Atributos administrativos samba
//        $usuario->setSambaKickoffTime('2147483647');
//        $usuario->setSambaLogoffTime('2147483647');
//        $usuario->setSambaLogonTime('0');
//        
//        //Cuidado con estos administrativos samba respecto al password 
//        $usuario->setSambaPwdCanChange('0');
//        //Parece que con esto se refiere al hecho que no lo ha cambiado antes, 
//        //no todos los usuarios lo tienen configurado a decir verdad
//        $usuario->setSambaPwdLastSet('0');
//        //Este lo escojo al azar, porque no encuentro cero para nadie
//        $usuario->setSambaPwdMustChange('2147483647');
//        
//        $entrada = $usuario->crearEntrada();
//        $this->mostrarEntrada($entrada);
//        
//    $resultado = <<<MAFI
//dn: uid=alortizd,ou=Users,dc=donaciones,dc=gob,dc=sv
//
//objectClass:
//
//Array
//(
//    [0] => top
//    [1] => person
//    [2] => organizationalPerson
//    [3] => posixAccount
//    [4] => shadowAccount
//    [5] => inetOrgPerson
//    [6] => sambaSamAccount
//)
//
//uid: alortizd
//
//
//MAFI;
//    print "<br> No te preocupes, sólo te hacen falta más o menos estos atributos";
//    print $resultado;
    }    
}
