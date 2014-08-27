<?php
namespace controladores;
/**
 * Controlador para directorio telefónico de la aplicacion
 *
 * @author alortiz
 */

class directorioControl extends \clases\sesion {
    private $ldap;
    
    function __construct(){
        // Consigue el objeto F3 en uso mediante el constructor de la clase padre
        parent::__construct();
        // Nombramos la página que hemos de producir
        $this->pagina = "directorio";
        // Objetos que hemos de usar
        $this->ldap = new \Modelos\controlLDAP($this->dn, $this->pswd);
    }
    
    
    /**
     * 
     * @return array
     */
    private function usuarios() {
        $atributos = array('cn','title','ou', 'mail');
        $filtro = "uid=*";
        return $this->ldap->getDatos($filtro, $atributos, 1000);
    }
    
    public function resultados() {
        $atributos = array('cn','title','ou', 'mail');
        $filtro = "uid=*";
        print(json_encode($this->ldap->getDatos($filtro, $atributos, 1000))) ;
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
        $usuarios = $this->usuarios();    
        $this->parametros['pagina'] = $this->pagina;
        $this->parametros['datos'] = $usuarios;
        echo $this->twig->render('directorio.html.twig', $this->parametros);       
        
    }
}
