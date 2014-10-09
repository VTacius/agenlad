<?php
namespace controladores\configuracion;

class dominioControl extends \clases\sesion{
    
    protected $error = array();
    protected $datos = array();
    protected $mensaje = array();
    
    public function __construct() {
        parent::__construct();
        $this->parametros['pagina'] = 'confdominios';
        $this->pagina = 'confdominios';
        $this->db = $this->index->get('dbconexion');
    }
    
    public function configuracion() {
        $hacienda = $this->configuracionDominio("hacienda.gob.sv", 
                "192.168.2.10", 
                "389", 
                'cn=admin,dc=hacienda,dc=gob,dc=sv', 
                "admin@salud.gob.sv",
                TRUE);
        $donaciones = $this->configuracionDominio("donaciones.gob.sv", 
                "192.168.2.14", 
                "389", 
                'cn=admin,dc=donaciones,dc=gob,dc=sv', 
                "admin@salud.gob.sv",
                TRUE);
        $datos = array ($donaciones, $hacienda);
        $this->parametros['datos'] = $datos;
//        print $this->twig->render('configuracion/configuracionDominio.html.twig', $this->parametros);
    }
    
    public function crearDominio(){
        $base = $this->index->get('POST.base');
        $clave = $this->index->get('POST.clave');
        $puerto = $this->index->get('POST.puerto');
        $servidor = $this->index->get('POST.servidor');
        $grupos_ou = $this->index->get('POST.grupos_ou');
        $base_grupo = $this->index->get('POST.base_grupo');
        $descripcion = $this->index->get('POST.descripcion');
        $base_usuario = $this->index->get('POST.base_usuario');
        $admin_zimbra = $this->index->get('POST.admin_zimbra');
        $dn_administrador = $this->index->get('POST.dn_administrador');
        
        $attr = $this->configuracionDominio($base, $servidor, $puerto, $dn_administrador, $admin_zimbra, $grupos_ou);
        $cmds = "insert into configuracion(clave, dominio, descripcion, attr) values(:clave)";
    }
    
    public function modificarDominios(){
        $base = $this->index->get('POST.base');
        $clave = $this->index->get('POST.clave');
        $puerto = $this->index->get('POST.puerto');
        $servidor = $this->index->get('POST.servidor');
        $grupos_ou = $this->index->get('POST.grupos_ou');
        $admin_zimbra = $this->index->get('POST.admin_zimbra');
        $dn_administrador = $this->index->get('POST.dn_administrador');
        
        $sambaSID = $this->index->get('POST.sambaSID');
        $mail_domain = $this->index->get('POST.mail_domain');
        $netbiosName = $this->index->get('POST.netbiosName');
        
        $attr = $this->configuracionDominio($base, $servidor, $puerto, $dn_administrador, $admin_zimbra, $grupos_ou, $sambaSID, $mail_domain, $netbiosName);
        $cmds = "update configuracion set attr=:attr where clave=:clave";
        $args = array('attr'=> $attr, 'clave'=> $clave);
        $resultado = $this->db->exec($cmds, $args);
        
        if ($resultado) {
            $this->mensaje[] = array("codigo" => "success", 'mensaje' => 'Cambios realizados exitosamente');
        }else{
            $this->mensaje[] = array("codigo" => "warning", 'mensaje' => 'No se han realizado cambios');
        }
        
        $retorno = array(
                'error' => $this->error,
                'datos' => $this->datos,
                'mensaje'=> $this->mensaje
        );
        
        print json_encode($retorno);
    }
    
    protected function configuracionDominio($base, $ip_server, $puerto, $dn_admin_ldap, $admin_zimbra, $grupos_ou, $sambaSID, $mail_domain, $netbiosName){
//        $rdn = explode(".", $dominio);
//        $dn = "";
//        foreach ($rdn as $componente) {
//            $dn .= "dc=$componente,";
//        }
//        $base = rtrim($dn, ",");

        $configuracion = array(
            'base' => $base,
            'puerto' => $puerto,
            'sambaSID' => $sambaSID,
            'servidor' => $ip_server,
            'grupos_ou' => (bool)$grupos_ou,
            'base_grupo' => 'ou=Groups,' . $base,
            'base_usuario' => 'ou=Users,' . $base,
            'netbiosName' => $netbiosName,
            'mail_domain' => $mail_domain,
            'admin_zimbra' => $admin_zimbra,
            'dn_administrador' => $dn_admin_ldap
        );

        return serialize($configuracion);
    }
    
    public function mostrarDetalles(){
        $this->comprobar($this->pagina);
        $clave = $this->index->get('PARAMS.clave');
        $cmds = 'select clave, dominio, descripcion, attr from configuracion where clave=:clave';
        $args = array('clave'=>$clave);
        $rest = $this->db->exec($cmds, $args);
        $rest[0]['attr'] = unserialize($rest[0]['attr']);
        $this->parametros['datos'] = $rest[0];
        echo $this->twig->render('configuracion/dominioModificar.html.twig', $this->parametros);
    }
    
    public function display() {
        $this->comprobar($this->pagina);
        $cmds = 'select clave, dominio, descripcion, attr from configuracion';
        $resultado = $this->db->exec($cmds);
        foreach ($resultado as &$dominio) {
            $dominio['attr'] = unserialize($dominio['attr']);
        }
        $this->parametros['datos'] = $resultado;

        echo $this->twig->render('configuracion/dominio.html.twig', $this->parametros);

    }
    
}

