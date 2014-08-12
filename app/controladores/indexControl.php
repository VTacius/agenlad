<?php
namespace controladores;
/**
 * Controlador para el index de la aplicación, especificamente, donde hacemos cambios de contraseña
 *
 * @author alortiz
 */

class indexControl extends \clases\sesion {
    private $hashes;
    private $ldap;
    private $db;
    
    function __construct(){
        // Consigue el objeto F3 en uso mediante el constructor de la clase padre
        parent::__construct();
        // Nombramos la página que hemos de producir
        $this->pagina = "index";
        // Objetos que hemos de usar
        $this->db = $this->index->get('dbconexion');
        $this->hashes = new \clases\cifrado();
        $this->ldap = new \Modelos\controlLDAP($this->dn, $this->pswd);
        
        
  }
    
    /**
     * Auxiliar de cambioPassword y cambioPasswordAdmin
     * Cambiar las contraseñas en el directorio LDAP
     * @param string $password
     */
    private function cifrado($password){
        $credenciales = array();
        $credenciales['userPassword'] = $this->hashes->slappasswd($password);
        $credenciales['sambaNTPassword'] = $this->hashes->NTLMHash($password);
        $credenciales['sambaLMPassword'] = $this->hashes->LMhash($password);
        return $this->ldap->modEntrada($credenciales);
    }
    
    /**
     * Bifurcación de credenciales para usuarios normales
     * Cambia la contraseña en LDAP
     * @param string $password
     */
    private function cambioPassword($password){
        $this->cifrado($password);
    }
    
    /**
     * Auxiliar de cambioPasswordAdmin
     * Obtiene las firmas actuales de la base de datos para el usuario dado
     * @param string $usuario
     * @return array
     */
    private function obtenerFirma($usuario){
        $cmds = 'select firmas, firmaz from roles where user=:user';
        $args = array('user'=>$usuario);
        $resultado = $this->db->exec($cmds, $args);
        return $resultado;
    }
    
    /**
     * Auxiliar de cambioPasswordAdmin
     * Configura las firmas nuevas en la base de datos para el usuario dado
     * @param string $usuario
     * @param string $claves
     * @param string $clavez
     */
    private function configurarFirma($usuario, $claves, $clavez){
        $cmds = "UPDATE roles SET firmas=:firmas, firmaz=:firmaz where user=:user";
        $args = array('firmas'=>$claves,'firmaz'=>$clavez, 'user'=>$usuario);
        $this->db->exec($cmds, $args);
    }


    /**
     * Bifurcación de credenciales para usuarios normales
     * No sólo cambia la contraseña en LDAP, sino que configura sus firmaS en 
     * la base de datos
     * @param type $usuario
     * @param type $password
     */
    private function cambioPasswordAdmin($usuario, $password){
        if(($this->cifrado($password))){
            $input = $this->obtenerFirma($usuario);
            // Desciframos las firmas con la contraseña actual
            $firmas = $this->hashes->descifrada($input[0]['firmas'], $this->pswd);
            $firmaz = $this->hashes->descifrada($input[0]['firmaz'], $this->pswd);
            // Volvemos a cifrarlas con la contraseña nueva
            $claves = $this->hashes->encrypt($firmas, $password);
            $clavez = $this->hashes->encrypt($firmaz, $password);
            // Ahora, que actualice la firma en la base de datos con la nueva contraseña
            $this->configurarFirma($usuario, $claves, $clavez);
            $this->index->reroute('@login_finalMensaje(@mensaje=La contraseña ha sido cambiada con éxito)');
        }else{
            $this->index->reroute('@login_mensaje(@mensaje=Ha ocurrido un problema al cambiar las contraseñas)');
        }
    }


    /**
     * Auxiliar de cambioCredenciales
     * Escoge el método para cambiar contraseña en base al $rol
     * @param string $rol
     * @param string $usuario
     * @param string $password
     */
    protected function credenciales($rol, $usuario, $password){
        if ($rol=='usuario'){
            $this->cambioPassword($password);
        }else{
            $this->cambioPasswordAdmin($usuario, $password);
        }
    }

    /**
     * Auxiliar de cambioCredenciales
     * Verifica la complejidad de las contraseñas dadas
     * @param type $password
     * @return type
     */
    private function complejidad($password){
      return preg_match_all('.{8}', $password);
    }

    public function cambioCredenciales(){
        // Tenemos permiso para acceder a esta funcion contenida en este método
        $this->comprobar($this->pagina);
        $rol = $this->index->get('SESSION.rol');
        $usuario = $this->index->get('SESSION.user');
        $passchangeprima = $this->index->get('POST.passchangeprima');
        $passchangeconfirm = $this->index->get('POST.passchangeconfirm');
        if ($passchangeconfirm == $passchangeprima){
            $this->credenciales($rol, $usuario, $passchangeprima);
        }else{
            $this->parametros['mensajeError'] = "Error: Las contraseñas no son iguales";
            $this->display();
        }
    }
    
    /**
     * Método por defecto
     */
    public function display() {
//        $mensaje = isset($this->index->get('PARAMS.mensaje'))?$this->index->get('PARAMS.mensaje'):"";
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina); 
        
//        $this->parametros['datos'] = $mensaje;
        echo $this->twig->render('index.html.twig', $this->parametros);       
    }
}


