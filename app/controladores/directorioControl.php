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
     * @param array $filtro Use los siguiente valores: ('cn','title','o', 'ou','mail')
     * @return array
     */
    private function usuarios_sync($filtro = array()) {
        $filtrado = "";
        $atributos = array('uid','cn','title','o', 'ou','mail');
        foreach ($atributos as $value) {
            if (array_key_exists($value, $filtro)) {
                $filtrado .= "($value=$filtro[$value])";
            }
        }
        $filtrador = empty($filtrado)? "(uid=*)" : $filtrado;
        return $this->ldap->getDatos($filtrador, $atributos, 1000);
    }
    
    /**
     * Devuelve usuarios
     * @param array $filtro Use los siguiente valores: ('cn','title','o', 'ou','mail')
     */
    public function usuarios_ajax() {
        print(json_encode($this->usuarios_sync($this->index->get('PARAMS'))));
    }
    
    /**
     * Método por defecto
     */
    public function display() {
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        foreach ($this->index->get('POST') as $key => $value) {
            print $key . ": " . $value . "\n<br>";
        }
        exit();
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina); 
        // Obtenemos los datos que hemos de enviar a la vista
        $filtro = array('uid'=>'alortiz');
        $usuarios = $this->usuarios_sync($filtro);    
        $this->parametros['pagina'] = $this->pagina;
        $this->parametros['datos'] = $usuarios;
        echo $this->twig->render('directorio.html.twig', $this->parametros);       
        
    }
}
