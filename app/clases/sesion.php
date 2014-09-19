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
        // Recuperamos firmaz desde sesion
        $firmaz = $this->index->get('SESSION.firmaz');
        // Desciframos
        $hashito = new \clases\cifrado();
        $clavez = $hashito->descifrada($firmaz, $this->pswd);
        $rdnLDAP = $this->index->get('lectorzimbra');
        $resultado = array('dn'=>$rdnLDAP, 'pswd'=>$clavez);
        return $resultado;
    }

    /**
     * Retorna el RDN y contraseña del usuario para manipular datos 
     * en samba
     * @return array('dn','pswd')
     */
    protected function getClaves(){
        // Recuperamos firmaz desde sesion
        $firmas = $this->index->get('SESSION.firmas');
        // Desciframos
        $hashito = new \clases\cifrado();
        $claves = $hashito->descifrada($firmas, $this->pswd);
        $adminDN = $this->index->get('adminldap');
        $resultado = array('dn'=>$adminDN, 'pswd'=>$claves);
        return $resultado;
    }

    /**
     * Auxiliar de comprobar
     * Verifica que se tenga permisos para ingresar al controlador dado según los permisos asignados
     * @param type $pagina
     * @param type $permisos
     */
    private function permisos ($pagina, $permisos){
      if (!array_key_exists($pagina, $permisos)){
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
            $this->permisos($pagina, $permisos);
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
