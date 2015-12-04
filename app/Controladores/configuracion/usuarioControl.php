<?php
namespace Controladores\configuracion;
/**
 * usuarioControl
 *
 * @author vtacius
 */
class usuarioControl extends \Clases\sesion{
    
    protected $error = array();
    protected $datos = array();
    protected $mensaje = array();
    
    public function __construct() {
        parent::__construct();
        $this->pagina = 'confpermisos';
        $this->db = $this->index->get('dbconexion');
    }
    
    /**
     * Esquema de permisos actual, a tomar como punto de referencia
     */
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
   
    public function configuracionRolUsuario(){
        $usuario = $this->index->get('POST.usuario'); 
        $dominio = $this->index->get('POST.dominio'); 
        $rol = $this->index->get('POST.rol'); 
        
        $cmds = 'insert into user(user, rol, dominio) values(:arguser,:argrol,:argdominio)';
        $args = array('arguser'=>$usuario, 'argrol'=>$rol, 'argdominio'=>$dominio);
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
    private function busquedaUsuariosLdap($usuario, $sufijo = "") {
        $usuarios = new \Modelos\userPosix($this->dn, $this->pswd, 'central' );
        $filtro = array("cn"=>"NOT (root OR nobody)", 'uid'=> $usuario . $sufijo);
        $datos = $usuarios->search($filtro, false, "dc=sv");
        return $datos;
    }
    
    protected function busquedaUsuarios($usuario){
        $datos = $this->busquedaUsuariosLdap($usuario, "*");
        $resultado = array();
        foreach ($datos as $user) {
            $resultado[] =  $user['uid'];
        }
        return $resultado;
    }
    
    /**
     * Recuerda que si el usuario no existe, el dominio esta vacío
     */
    public function datosRolUsuario(){
        $usuario =  $this->index->get('POST.usuario');  
        $datos = $this->busquedaUsuariosLdap($usuario);
        $dn = $datos[0]['dn'];
        $pattern = "(dc=(?P<componentes>[A-Za-z]+))";
        $matches = array();
        $dc = "";
        preg_match_all($pattern, $dn, $matches );
        foreach ($matches['componentes'] as $componentes){
                $dc .= $componentes . ".";
        }
        $dominio = rtrim($dc, ".");   
        
        $cmds = 'select rol from user where user=:arguser';
        $args = array('arguser'=> $usuario);
        $resultado = $this->db->exec($cmds, $args);
        $resultado[0]['dn'] = $dominio;
        print json_encode($resultado);
    }


    public function busqueda(){
        $termino =  $this->index->get('PARAMS.term');    
        $listado = $this->busquedaUsuarios($termino);
        print json_encode($listado);
    }
    
    public function display() {
        $this->comprobar($this->pagina);
        $this->parametros['pagina'] = $this->pagina; 
        $cmds = "select user, titulo, dominio from user inner join rol on user.rol=rol.rol";
        $resultado = $this->db->exec($cmds);
        $this->parametros['datos'] = $resultado;

        echo $this->twig->render('configuracion/usuario.html.twig', $this->parametros);

    }
      
}
