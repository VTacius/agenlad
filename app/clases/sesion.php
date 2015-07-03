<?php
/**
 * Clase para el manejo de sesiones
 *
 * @author alortiz
 */
namespace clases;
abstract class sesion {
    /** @var string */
    protected $dn;
    /** @var string */
    protected $pswd;
    /** @var \Base::instance() */
    protected $index;
    /** @var string Nombre de la ACL con que se evalúa el controlador */
    protected $pagina;
    /** @var array Todos los datos de sesión necesarios*/
    protected $parametros;
    /** @var Twig_Environment*/
    protected $twig;

    /**
     * El constructor llama al objeto F3 en uso
     */
    public function __construct(){
        $this->index = \Base::instance();
        $db = $this->index->get('dbconexion');
        $sesion = new \DB\SQL\Session($db);
        // El que descienda de acá, usará twig
        $this->twig = $this->index->get('twig');
        // Traemos las variables de sesión necesarias
        // dn y pswd quedarán disponibles para el uso de las clases hijas
        $this->dn = $this->index->get('SESSION.dn');
        $this->pswd = $this->index->get('SESSION.pswd');
        // Solo los necesitamos para crear inicializar el array parametros
        $rol = $this->index->get('SESSION.rol');
        $user = $this->index->get('SESSION.user');
        $titulo = $this->index->get('SESSION.titulo');
        $permiso = $this->index->get('SESSION.permisos');
        $this->parametros = array(
            'rol' => $rol,
            'menu' => $permiso,
            'titulo'=> $titulo,
            'usuario' =>  $user
        );
    }
    
    /** 
     * Ahora se que en todos habrá un display,
     * y por tanto puedo usarlo desde acá
     */
    abstract public function display();
    
    /**
     * Verifica que $parametros haya llegado en POST
     * Parece que esta obsoleto y nadie usa este método
     * @param string $parametro
     * @param string $mensaje
     * @return string
     */
    protected function input($parametro, $mensaje){
        $valor = 'POST.' . $parametro;
        $valor = $this->index->get($valor);
        if (empty($valor)) {
            $this->parametros['mensajeError'] = "Error: $mensaje";
            $this->display();
        }else{
            return $valor;
        }
        
    }

    /**
     * Retorna el RDN y contraseña del usuario para manipular datos 
     * en zimbra
     * @return array('dn','pswd')
     */
    protected function getClavez(){
        // Recuperamos firmaz desde la base de datos
        $configUser = $this->getConfiguracionUsuario();
        // Conseguimos datos por defecto para que usuarios normales puedan configurar sus propios valores
        if (count($configUser)==0){
            $configUser = $this->getConfiguracionUsuario('usuario');
        }
        $firmaz = $configUser['firmaz'];
        
        $semilla = $this->index->get('semilla');
        $dominio = $configUser['dominio'];
        $dc =  explode(".", $dominio);
        $clave =  $semilla . $dc[0];
        
        // Desciframos firmaz con password de usuario
        $hashito = new \clases\cifrado();
        $clavez = $hashito->descifrada($firmaz, $clave);
         // Obtenemos el DN del administrador desde la base de datos
        $config = $this->getConfiguracionDominio();
        $adminDN = $config['admin_zimbra'];
        
        $resultado = array('dn'=>$adminDN, 'pswd'=>$clavez);
        return $resultado;
    }

    /**
     * Retorna el RDN y contraseña del usuario para manipular datos 
     * en samba
     * @return array('dn','pswd')
     * TODO: Modificar esta descripcion
     * TODO: Me esta llevando a pensar que màs que el dominio, vengo necesitando la clave
     */
    protected function getClaves(){
        // Recuperamos firmaz desde sesion y desciframos
        $hashito = new \clases\cifrado();
        $configUser = $this->getConfiguracionUsuario();
        $firmas = $configUser['firmas'];
        
        $semilla = $this->index->get('semilla');
        $dominio = $configUser['dominio'];
        $dc =  explode(".", $dominio);
        $clave =  $semilla . $dc[0];
        
        $claves = $hashito->descifrada($firmas, $clave);
        
        // Obtenemos el DN del administrador desde la base de datos
        $config = $this->getConfiguracionDominio();
        $adminDN = $config['dn_administrador'];
        
        $resultado = array('dn'=>$adminDN, 'pswd'=>$claves);
        return $resultado;
    }

    /**
     * TODO: Estamos duplicados con \Modelos\controlLDAP
     * Retorna un array con la configuración para el dominio para el cual tiene
     * permisos el usuarios que ha abierto la sesion
     * @return array
     */
    protected function getConfiguracionDominio(){
        $base = $this->index->get('dbconexion');
        $dominio = $this->index->get('SESSION.dominio');
        
        $cmds = "select attr from configuracion where dominio=:dominio";
        $args = array('dominio'=>$dominio);
        $resultado = $base->exec($cmds, $args);
        
        if ($base->count() > 0) {
            return unserialize($resultado[0]['attr']);
        }
    }
    
    /**
     * Obtiene datos del usuario de la base de datos relacionados con su rol
     * @return array
     */
    protected function getConfiguracionUsuario($usuario=""){
        $base = $this->index->get('dbconexion');
        $usuario = empty($usuario) ? $this->index->get('SESSION.user') : $usuario;
        
        $cmds = "select titulo, user.rol, permisos, credenciales.firmas, credenciales.firmaz, user.dominio
        from user join rol on user.rol=rol.rol join credenciales on user.dominio=credenciales.dominio  where user=:user";
        $args = array('user'=>$usuario);
        $resultado = $base->exec($cmds, $args);
        if(count($resultado)>0){
            return $resultado[0];
        } 
    }
    
    /**
     * Auxiliar de comprobar
     * Verifica que se tenga permisos para ingresar al controlador dado según los permisos asignados
     * @param type $pagina
     * @param type $permisos
     */
    private function permisos ($pagina, $permisos){
      if (array_search($pagina, $permisos) === false){
          $this->index->reroute('@login_mensaje(@mensaje=No tiene permiso)');
          exit();
      }
    }
    /**
     * Comprueba que la sesión este iniciada, y que tenga permisos para ingresar en ella
     * Caso contrario, que sigue con el flujo de la aplicación
     * @param string $pagina Página a comprobar
     */
    protected function comprobar($pagina){
        if ($this->index->exists('SESSION.permisos')){
            $permisos = $this->index->get('SESSION.permisos');
            $reglas = array();
            foreach($permisos as $v => $i){
                if (is_array($i)){
                    $reglas = array_merge($reglas, array_keys($i));
                }else{
                    $reglas[] = $v;
                }
            }
            $this->permisos($pagina, $reglas);
        }else{
            $this->index->reroute('@login_mensaje(@mensaje=Necesita autenticarse)');
            exit();
        }
    }
    
    /**
     * Se limita a verificar si la sesión ya esta abierta, en cuyo caso se reenvía 
     * a ruta en @main
     */
    protected function comprobarSesion(){
        if ($this->index->exists('SESSION.permisos')){
            $this->index->reroute('@main');
            exit();
        }
    }

}
