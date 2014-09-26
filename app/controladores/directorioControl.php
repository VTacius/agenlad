<?php
namespace controladores;
/**
 * Controlador para directorio telefónico de la aplicacion
 *
 * @author alortiz
 */

class directorioControl extends \clases\sesion {
    
    function __construct(){
        // Consigue el objeto F3 en uso mediante el constructor de la clase padre
        parent::__construct();
        // Nombramos la página que hemos de producir
        $this->pagina = "directorio";
        // Objetos que hemos de usar
    }
    
    
    /**
     * 
     * @param array $filtro Use los siguiente valores: ('cn','title','o', 'ou','mail')
     * @return array
     */
    private function busquedaUsuarios($filter = array()) {
        $usuarios = new \Modelos\userPosix($this->dn, $this->pswd, 'central' );
        $atributos = array('uid','cn','title','o', 'ou','mail', 'telephoneNumber');
        $filtro = empty($filter) ? array("cn"=>"NOT (root OR nobody)") : array_merge($filter, array("cn"=>"NOT (root OR nobody)"));
//        $filtro = array("cn"=>"NOT (root OR nobody)") ;
        $datos = $usuarios->search($filtro, $atributos, "dc=sv");
        $this->parametros['errorLdap'] = $usuarios->getErrorLdap();
        return $datos;   
    }
    
    /**
     * 
     */    
    public function mostrarUsuario(){
        $this->comprobar($this->pagina);
        // Usamos los valores enviados por POST para construir el filtro
        $parametros = $this->index->get('POST');
        $filtro = array();
        foreach ($parametros as $key => $value) {
            if (  !(empty($value) || $value == "*") ) {
                $filtro[$key] = $value;
            }
        }
        $datos['datos'] = $this->busquedaUsuarios($filtro);
        // TODO: Esta es oficialmente la manera en que debe formarse la respuesta hacia ajax
        $resultado = array_merge($datos, array('errorLdap'=> $this->parametros['errorLdap']));
        print json_encode($resultado);
    }
    
    /**
     * Método por defecto
     */
    public function display() {
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina); 
        // Obtenemos los datos que hemos de enviar a la vista
//        $this->parametros['datos'] = $this->busquedaUsuarios();
        echo $this->twig->render('directorio.html.twig', $this->parametros);        
    }
}
