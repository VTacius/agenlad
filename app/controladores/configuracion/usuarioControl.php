<?php
namespace controladores\configuracion;

class usuarioControl extends \clases\sesion{
    public function __construct() {
        parent::__construct();
        $this->parametros['pagina'] = 'directorio';
        $this->db = $this->index->get('dbconexion');
    }
    
    public function configuracionRoles(){
        $super_administrador = array(
            "directorio"    => "Directorio Telefónico",
            "main"          => "Cambio de Contraseña",
            'usuario'       => array(
                "usershow"   => "Ver Usuario",
                "usermod"    => "Modificar Usuario",
                "useradd"    => "Agregar Usuario"),
            'configuracion' => array(
                'confdominios'  => "Dominios",
                'confpermisos'  => 'Permisos'
            )
        );
        $administradores = array(
            "directorio" => "Directorio Telefónico",
            "main"       => "Cambio de Contraseña",
            'usuario'       => array(
                "usershow"   => "Ver Usuario",
                "usermod"    => "Modificar Usuario",
                "useradd"    => "Agregar Usuario")
        );

        $tecnicos = array(
            "directorio" => "Directorio Telefónico",
            "main"       => "Cambio de Contraseña",
            'usuario'       => array("usershow"   => "Ver Usuario")
        );

        $usuarios = array(
            "directorio" => "Directorio Telefónico",
            "main"       => "Cambio de Contraseña"
        );       
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
        public function display() {
            $qusers = "select user, rol, dominio, firmas, firmaz, bandera from user";
            $qconfig = 'select clave, dominio, descripcion, attr from configuracion';
            $qresultado = $this->db->exec($qconfig);
            foreach ($qresultado as &$dominio) {
                $dominio['attr'] = unserialize($dominio['attr']);
            }
            $this->parametros['datos'] = $qresultado;
            
            $this->parametros['menue'] = array(
            "directorio"    => "Directorio Telefónico",
            "main"          => "Cambio de Contraseña",
            'usuario'       => array(
                'usershow'   => "Ver Usuario",
                'usermod'    => "Modificar Usuario",
                'useradd'    => "Agregar Usuario"),
            'configuracion' => array(
                'confdominios'  => "Dominios",
                'confpermisos'  => 'Permisos'
            ),
        );
            
            print $this->twig->render('configuracion/usuario.html.twig', $this->parametros);
            
        }
    
    protected function configuracionDominio($dominio, $ip_server, $puerto, $dn_admin_ldap, $admin_zimbra, $grupos_ou){
        $rdn = explode(".", $dominio);
        $dn = "";
        foreach ($rdn as $componente) {
            $dn .= "dc=$componente,";
        }
        $base = rtrim($dn, ",");

        $configuracion = array(
            'base' => $base,
            'puerto' => $puerto,
            'servidor' => $ip_server,
            'grupos_ou' => $grupos_ou,
            'base_grupo' => 'ou=Groups,' . $base,
            'base_usuario' => 'ou=Users,' . $base,
            'admin_zimbra' => $admin_zimbra,
            'dn_administrador' => $dn_admin_ldap
        );

        return serialize($configuracion);
    }
    
}

// Necesario para resetear la contraseña de alguien con permisos administrativos
$cmds_reset_password = "update user set firmas=:password_admin_ldap, firmaz='Zimbra2025_Lector', bandera=1 where user=:user";
$args_reset_password = array('user' => 'alortiz', 'password_admin_ldap' => 'admin_ldap_hacienda');

// Necesario para convertir a un usuario cualquiera en administrador
$cmds_create_rol = "insert into user(user, rol, dominio, firmas, firmaz, bandera) values(:user, :rol, :dominio ,'admin_ldap_hacienda' ,'srv2025', 1)";
$args_create_rol = array('user' => "czapata", 'rol'=>'admon', 'dominio'=>'donaciones.gob.sv');

// Antes de convertir a un usuario cualquiera en administrador, agregue su dominio de la siguiente forma
$cmds_create_dominio = "insert into user(user, rol, dominio, firmas, firmaz, bandera) values(:user, :rol, :dominio ,'admin_ldap_hacienda' ,'Zimbra2025_Lector', 1)";
$args_create_dominio = array('user' => "czapata", 'rol'=>'admon', 'dominio'=>'donaciones.gob.sv');

