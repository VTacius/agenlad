<?php
namespace controladores;

/**
 * Controlador para manejo de pruebas
 *
 * @author alortiz
 */
class pruebaControl extends \clases\sesion{
    
    protected function mostrarHtml($mensaje){
        if (is_array($mensaje)) {
            print_r($mensaje);
            print "<br>";
        }else{
            print "$mensaje <br>";
        }
    }
//    public function display(){
//        $usuario = new \Modelos\userSamba($this->dn, $this->pswd);
//        $usuario->setUid('alortiz');
//        $this->mostrarHtml($usuario->getSn());
//        $this->mostrarHtml($usuario->getCn());
////        $usuario->setOu('Nuevo lugar');
////        $usuario->setO('Nueva oficina');
////        $usuario->setUserPassword('tracio');
//        $this->mostrarHtml($usuario->getO());
//        $this->mostrarHtml($usuario->getOu());
//        print "Atributos samba <br>";
//        $this->mostrarHtml($usuario->getSambaSID());
//        $this->mostrarHtml($usuario->getSambaAcctFlags());
//        $this->mostrarHtml($usuario->getSambaHomeDrive());
//        print "Atributos desde Grupos: <br>";
//        $grupo = new \Modelos\grupoSamba($this->dn, $this->pswd);
//        $grupo->setGidNumber($usuario->getGidNumber());
//        $this->mostrarHtml($usuario->getCn());
//        print "Configurando setUidNumber en 1038 <br>";
//        $usuario->setUidNumber('1038');
//        $this->mostrarHtml($usuario->getSambaSID());
//        print "Verifico atributos zimbra: <br>";
//        $mailbox = new \Modelos\mailbox('Zimbra2025_Lector');
//        $mailbox->setUid('alortiz');
//        $this->mostrarHtml($mailbox->getZimbraAccountStatus());
//        echo $this->twig->render('pruebas.html.twig');
//    }
    
//   # public function display() {
//   #     $grupo = new \Modelos\userPosix('uid=alortiz,ou=http_access,ou=Users,dc=hacienda,dc=gob,dc=sv','Figaro.12', "central");
//   #     print "<br>getAll para userPosix";        
//   #     $grupos = $grupo->getAll(array('cn','gidNumber'));
//   #     print "<br>" ;
//   #     print_r($grupo->getErrorLdap());
//   #     print "<br>" ;
//   #     foreach ($grupos as $value) {
//   #         print_r($value);
//   #         print "<br>";
//   #             
//   #     }
//   #     
//   #     print "<br>search para userPosix";        
//   #     $filtro = Array ( 'cn'=>'Alexander Ortiz', 'gidNumber' => 1009 );
//   #     print "<br>" ;
//   #     $busqueda = $grupo->search($filtro);
//   #     print_r($grupo->getErrorLdap());
//   #     print "<br>" ;
//   #     
//   #     foreach ($busqueda as $value) {
//   #         print_r($value);
//   #         print "<br><br>";        
//   #     }
//   #     
//   #     print "<br>search para userPosix";        
//   #     $filtro = Array ( 'cn'=>'*', 'gidNumber' => 1009 );
//   #     print "<br>" ;
//   #     $busqueda = $grupo->search($filtro);
//   #     foreach ($busqueda as $value) {
//   #         print_r($value);
//   #         print "<br><br>";        
//   #     }
//   #     
//   #     
//   #     
//   # }
    public function display(){
        $administrador = "admin@salud.gob.sv";
	$contrasenia = "srv2025";

	$usuario = array(
            'sn' => "Guevara",
            'dn' => "uid=alortiz,ou=Users,dc=salud,dc=gob,dc=sv",
            'mail' => "virgini138@salud.gob.sv",
            'password' => 'virginia134',
            'givenName' => "Virginia"
        );

        $modificacionUsuario = array(
            'givenName' => "Virginia Esmeralda",
            'sn' => "Guevara Ochoa",
        );

        $mailbox = new \Modelos\mailbox("admin", "srv2025");
        $mailbox->cuenta("mcardenas");
	foreach($mailbox->getCuenta() as $index => $attr){
		print "$index: $attr <br>";
	}
	$mailbox->setZimbraAccountStatus("active");
	$mailbox->actualizarEntrada();
        print "<br><br>Devuelto el siguiente error<br>";
        print_r($mailbox->getErrorSoap());
        print "<br><br>Devuelto el siguiente mensaje<br>";
        $mailbox = new \Modelos\mailbox("admin", "srv2025");
        $mailbox->cuenta("mcardenas");
	foreach($mailbox->getCuenta() as $index => $attr){
		print "$index: $attr <br>";
	}
	
	#var_dump($mailbox->getLastResponse());

//        $login = new \Acceso\zimbraSoapAccess("10.10.20.102");
//        $login->login("admin@salud.gob.sv", "srv2025");
//        print "\n\n";
//        print_r($login->getErrorSoap());
//        print "\n\n";
//        $cuenta = $login->getAccount("virgini137", "full");
//        print "\n\n";
//        print_r($login->getErrorSoap());
//        print "\n\n";
//        print_r($login->getAttributeAccount($cuenta, "givenName"));
//        print_r($login->getErrorSoap());
//        print "\n\n";
//        $login->modificarMailbox("virgini137", $modificacionUsuario);
//        $cuentaMod = $login->getAccount("virgini137", "full");
//        print_r($login->getAttributeAccount($cuentaMod, "givenName"));
//        print "\n\n";
        //$login->modificarMailbox($usuario, $cambios);
        //$login->getMensaje();
        //$login->crearMailbox($usuario);
        //$login->getMensaje();
    }
}
