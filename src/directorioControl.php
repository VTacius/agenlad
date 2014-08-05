<?php
/**
 * Controlador para directorio telefónico de la aplicacion
 *
 * @author alortiz
 */

class directorioControl extends clases\sesion {
    protected $server;
    protected $puerto;
    protected $user;
    protected $base;
    protected $pswd;
    
    function directorioControl(){
        // Consigue el objeto F3 en uso mediante el constructor de la clase padre
        clases\sesion::__construct();
        // Nombramos la página que hemos de producir
        $this->pagina = "directorio";
        // Conseguimos las variables que se encuentran en el el fichero de configuracion
        $this->server = $this->index->get('server');
        $this->puerto = $this->index->get('puerto');
        $this->base = $this->index->get('base');
        $this->user = $this->index->get('user');
        $this->pswd = $this->index->get('pswd');
        
    }
     
    private function datos () {
      $login = new ldap\controlLDAP();
      $login->conexion($this->server, $this->puerto);
      $login->crearDN($this->user,$this->base);
      $login->enlace($this->pswd);
      $atributos = array('cn','title','ou', 'mail');
      $filtro = "uid=*";
      $login->datos($login->ou, $filtro, $atributos, 500);
      return $login->arrayDatosLDAP($atributos);
    }
    
    function display() {
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina);
        // Obtenemos la variable twig que hemos guardado con la precarga de plantillas
        $twig = $this->index->get('twig');
        $usuarios = $this->datos();
        $parametros = array(
            'usuario' =>  $this->index->get('SESSION.user'),
            'menu' => $this->index->get('SESSION.permisos'),
            'usuarios'=> $usuarios,
            'pagina' => $this->pagina
        );
        echo $twig->render('directorio.html.twig', $parametros);       
        
    }
}
