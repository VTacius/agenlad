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
    
    public function display() {
        $grupo = new \Modelos\userPosix('uid=alortiz,ou=Users,dc=hacienda,dc=gob,dc=sv','Figaro.12');
        
        $grupos = $grupo->getAll(array('cn','gidNumber'));
        foreach ($grupos as $value) {
            print_r($value);
            print "<br>";
                
        }
        
        $filtro = Array ( 'cn'=>'Alexander Ortiz', 'gidNumber' => 1009 );
        print "<br>" ;
        $busqueda = $grupo->search($filtro);
        foreach ($busqueda as $value) {
            print_r($value);
            print "<br><br>";        
        }
        
        $filtro = Array ( 'cn'=>'*', 'gidNumber' => 1009 );
        print "<br>" ;
        $busqueda = $grupo->search($filtro);
        foreach ($busqueda as $value) {
            print_r($value);
            print "<br><br>";        
        }
        
        
        
    }
}
