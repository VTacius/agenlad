<?php
/**
 * Clase para el manejo de sesiones
 *
 * @author alortiz
 */
namespace clases;
class sesion {
    protected $index;
    protected $pagina;

    /**
     * El constructor llama al objeto F3 en uso
     */
    public function __construct(){
        $this->index = \Base::instance();        
    }
    
    /**
     * Comprueba que la sesión este iniciada, 
     * y que tenga permisos para ingresar en ella
     * @param string $pagina Página a comprobar
     */
    public function comprobar($pagina){
        $db = $this->index->get('dbconexion');
        $sesion = new \DB\SQL\Session($db);
        if ($this->index->exists('SESSION.permisos')){
            $permisos = $this->index->get('SESSION.permisos');
            if (!array_key_exists($pagina, $permisos)){
                $this->index->reroute('@login_mensaje(@mensaje=No tiene permiso)');
                exit();
            }
        }else{
            $this->index->reroute('@login_mensaje(@mensaje=Necesita autenticarse)');
            exit();
        }
    }

}
