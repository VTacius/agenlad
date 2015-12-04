<?php
namespace Controladores\configuracion;
/**
 * dominioControl
 *
 * @author vtacius
 */
class dominioControl extends \Clases\sesion{
    
    protected $error = array();
    protected $datos = array();
    protected $mensaje = array();
    
    public function __construct() {
        parent::__construct();
        $this->parametros['pagina'] = 'confdominios';
        $this->pagina = 'confdominios';
        $this->db = $this->index->get('dbconexion');
    }
    
    /**
     * ¿Se han configurado los parametros de este dominio?
     * ¿Existen credenciales asociadas a este dominio en la base de datos?
     * Devuelve True si el dominio no existe
     * @param string $dominio
     * @return boolean
     */
    protected function verificaDominioExiste($dominio){
        $cmds = 'select clave from configuracion where dominio=:argclave';
        $args = array('argclave'=>$dominio);
        $resultado = $this->db->exec($cmds, $args);
        $configuracion = $this->db->count();
        
        $cmdz = 'select dominio from credenciales where dominio=:argdominio';
        $argz = array('argdominio'=>$dominio);
        $result = $this->db->exec($cmdz, $argz);
        $credenciales = $this->db->count();
        
        if (($configuracion + $credenciales ) == 0) {
            return true;
        }  else {
            $this->mensaje[] = array('codigo' => 'danger', 'mensaje' => 'Ese dominio ya existe');
            return false;
        }
    }
    
    /**
     * Actualiza $objeto (firmas|firmaz), cifrando con $dominio y la semila general 
     * del proyecto que reside en la configuración a $password
     * @param string $objeto
     * @param string $dominio
     * @param string $password
     * @return Boolean?
     */
    protected function actualizarCredenciales($objeto, $dominio, $password){
        $semilla = $this->index->get('semilla');
        $dc =  explode(".", $dominio);
        $clave =  $semilla . $dc[0];
        $hashito = new \Clases\cifrado();
        $marcado = $hashito->encrypt($password, $clave);
        $cmds = 'update credenciales set '.$objeto.' = :arg'.$objeto.' where dominio = :argdominio';
        $args = array('argdominio'=>$dominio, 'arg'.$objeto.''=>$marcado);
        return $this->db->exec($cmds, $args);
    }

    protected function nuevasCredencialesDominio($dominio, $password){
        $cmds = 'insert into credenciales(dominio) values(:argdominio)';
        $args = array('argdominio'=>$dominio);
        $this->db->exec($cmds, $args);
        $resultado = $this->actualizarCredenciales('firmas', $dominio, $password);
        $resultado = $this->actualizarCredenciales('firmaz', $dominio, $password);
    }

    /**
     * Pertenece a la ruta //
     * Se encarga de agregar el dominio en la base de datos: 
     * Agrega la entrada en tabla de configuracion 
     * y en la tabla credenciales 
     */
    public function crearDominio(){
        $base = $this->index->get('POST.base');
        $puerto = $this->index->get('POST.puerto');
        $dominio = $this->index->get('POST.dominio');
        $servidor = $this->index->get('POST.servidor');
        $grupos_ou = $this->index->get('POST.grupos_ou');
        $base_grupo = $this->index->get('POST.base_grupo');
        $descripcion = $this->index->get('POST.descripcion');
        $base_usuario = $this->index->get('POST.base_usuario');
        $admin_zimbra = $this->index->get('POST.admin_zimbra');
        $dn_administrador = $this->index->get('POST.dn_administrador');
        
        $sambaSID = $this->index->get('POST.sambaSID');
        $mail_domain = $this->index->get('POST.mail_domain');
        $netbiosName = $this->index->get('POST.netbiosName');
        
        $firmas = $this->index->get('POST.passwordZimbra'); 
        $firmaz = $this->index->get('POST.passwordSamba'); 
        
        
        // Se recomienda que clave sea el primer componente de dominio
        $dc = explode(".",$dominio);
        $clave = $dc[0];
        
        if($this->verificaDominioExiste($dominio)){
            $attr = $this->configuracionDominio($base, $servidor, $puerto, $dn_administrador, $admin_zimbra, $grupos_ou, $sambaSID, $mail_domain, $netbiosName);
            $cmds = "insert into configuracion(clave, dominio, descripcion, attr) values(:argclave, :argdominio, :argdescripcion, :argattr)";
            $args = array('argclave'=>$clave, 'argdominio'=>$dominio, 'argdescripcion'=>$descripcion, 'argattr'=>$attr);
            
            $this->nuevasCredencialesDominio($dominio, $firmas);
            
            $resultado = $this->db->exec($cmds, $args);
            
            if ($resultado) {
                $this->mensaje[] = array("codigo" => "success", 'mensaje' => 'Cambios realizados exitosamente');
            }else{
                 $this->mensaje[] = array("codigo" => "warning", 'mensaje' => 'No se han realizado cambios');
            }
        }
        
        $retorno = array(
                'error' => $this->error,
                'datos' => $this->datos,
                'mensaje'=> $this->mensaje
        );
        
        print json_encode($retorno);
    }
    
    public function setPasswordSamba(){
        $dominio = $this->index->get('POST.dominio');
        $password = $this->index->get('POST.password');
        $resultado = $this->actualizarCredenciales('firmas', $dominio, $password);
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
    
    public function setPasswordZimbra(){
        $dominio = $this->index->get('POST.dominio');
        $password = $this->index->get('POST.password');
        $resultado = $this->actualizarCredenciales('firmaz', $dominio, $password);
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
        $cmds = "update configuracion set attr=:atributo where clave=:clave";
        $args = array('atributo'=> $attr, 'clave'=> $clave);
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
    /**
     * Serializa un array con todos los datos del dominio
     * @param string $base
     * @param string $ip_server
     * @param string $puerto
     * @param string $dn_admin_ldap
     * @param string $admin_zimbra
     * @param string $grupos_ou
     * @param string $sambaSID
     * @param string $mail_domain
     * @param string $netbiosName
     * @return Serialize String
     */
    protected function configuracionDominio($base, $ip_server, $puerto, $dn_admin_ldap, $admin_zimbra, $grupos_ou, $sambaSID, $mail_domain, $netbiosName){

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
    
    /**
     * Muestra los datos para el dominio dado
     */
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
    
    public function mostrarNuevoDominio(){
        $this->comprobar($this->pagina);
        echo $this->twig->render('configuracion/dominioNuevo.html.twig', $this->parametros);
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

