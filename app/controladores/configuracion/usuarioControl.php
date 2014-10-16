<?php
namespace controladores\configuracion;

class usuarioControl extends \clases\sesion{
    public function __construct() {
        parent::__construct();
        $this->pagina = 'confpermisos';
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
   
        public function display() {
            $this->comprobar($this->pagina);
            $this->parametros['pagina'] = $this->pagina; 
            $cmds = "select user, titulo, dominio from user inner join rol on user.rol=rol.rol";
            $resultado = $this->db->exec($cmds);
            $this->parametros['datos'] = $resultado;
            
            print $this->twig->render('configuracion/usuario.html.twig', $this->parametros);
            
        }
    
    
    
}

// Necesario para resetear la contraseña de alguien con permisos administrativos
$cmds_reset_password = "update user set firmas=:password_admin_ldap, firmaz='Zimbra2025_Lector', bandera_firmas=1, bandera_firmaz=1 where user=:user";
$args_reset_password = array('user' => 'alortiz', 'password_admin_ldap' => 'admin_ldap_hacienda');

// Necesario para convertir a un usuario cualquiera en administrador
$cmds_create_rol = "insert into user(user, rol, dominio, firmas, firmaz, bandera) values(:user, :rol, :dominio ,'admin_ldap_hacienda' ,'srv2025', 1)";
$args_create_rol = array('user' => "czapata", 'rol'=>'admon', 'dominio'=>'donaciones.gob.sv');

// Antes de convertir a un usuario cualquiera en administrador, agregue su dominio de la siguiente forma
$cmds_create_dominio = "insert into user(user, rol, dominio, firmas, firmaz, bandera_firmas, bandera_firmaz) values(:user, :rol, :dominio ,'admin_ldap_hacienda' ,'Zimbra2025_Lector', 1)";
$args_create_dominio = array('user' => "czapata", 'rol'=>'admon', 'dominio'=>'donaciones.gob.sv');

