<?php
namespace controladores;
/**
 * Controlador para el index de la aplicación, en nuestro caso, el lugar donde 
 * cambiamos contraseñas
 * 
 * @version 0.1
 * @author alortiz
 */

class mainControl extends \clases\sesion {
    private $hashes;
    private $usuario;
    
    protected $error = array();
    protected $mensaje = array();
    
    function __construct(){
        parent::__construct();
        // Nombramos la página que hemos de producir
        $this->pagina = "main";
        // Objetos que hemos de usar
        $this->db = $this->index->get('dbconexion');
        $this->hashes = new \clases\cifrado();  
  }
    
    /**
     * Cambiar las contraseñas en el directorio LDAP
     * @param string $password
     */
    private function changeLdapPassword($usuario, $password){
        $this->usuario = new \Modelos\userPosix($this->dn, $this->pswd);
        $this->usuario->setUid($usuario);
        $this->usuario->configuraPassword($password);
        if ($this->usuario->actualizarEntrada()) {
            $this->mensaje[] = array("codigo" => "success", 'mensaje'=> "Contraseña cambiada exitosamente");
            return true;
        }else{
            $this->error[] = $this->usuario->getErrorLdap();
            $this->mensaje[] = array("codigo" => "danger", 'mensaje' => "Ha ocurrido un error al cambiar las contraseñas");
            return false;
        }
        
    }
    
    /**
     * Auxiliar de cambiosFirmas
     * Obtiene las firmas actuales de la base de datos para el usuario dado
     * @param string $usuario
     * @return array
     */
    private function obtenerFirma($usuario){
        $cmds = 'select firmas, firmaz from user where user=:user';
        $args = array('user'=>$usuario);
        $resultado = $this->db->exec($cmds, $args);
        return $resultado;
    }
    
    /**
     * Auxiliar de cambiosFirmas
     * Configura las firmas nuevas en la base de datos para el usuario dado
     * @param string $usuario
     * @param string $claves
     * @param string $clavez
     */
    private function configurarFirma($usuario, $claves, $clavez){
        $cmds = "UPDATE user SET firmas=:firmas, firmaz=:firmaz where user=:user";
        $args = array('firmas'=>$claves,'firmaz'=>$clavez, 'user'=>$usuario);
        $this->db->exec($cmds, $args);
    }

    /**
     * TODO: Hacer una comprobación de este proceso
     * @param string $usuario
     * @param string $password
     */
    private function cambiosFirmas($usuario, $password){
            $input = $this->obtenerFirma($usuario);
            // Desciframos las firmas con la contraseña actual
            $firmas = $this->hashes->descifrada($input[0]['firmas'], $this->pswd);
            $firmaz = $this->hashes->descifrada($input[0]['firmaz'], $this->pswd);
            // Volvemos a cifrarlas con la contraseña nueva
            $claves = $this->hashes->encrypt($firmas, $password);
            $clavez = $this->hashes->encrypt($firmaz, $password);
            // Ahora, que actualice la firma en la base de datos con la nueva contraseña
            $this->configurarFirma($usuario, $claves, $clavez);
            $this->mensaje[] = array("codigo" => "success", 'mensaje'=> "Cambio de Firmas exitoso");
    }
   
    /**
     * Auxiliar de cambioCredenciales
     * Escoge el método para cambiar contraseña en base al $rol
     * @param string $rol
     * @param string $usuario
     * @param string $password
     * @return string
     */
    protected function credenciales($rol, $usuario, $password){
        if ($rol=='usuario'){
            $this->changeLdapPassword($usuario, $password);
        }else{
            if ($this->changeLdapPassword($usuario, $password)) {
                $this->cambiosFirmas($usuario, $password);
            }
        }
    }

    /**
     * Auxiliar de cambioCredenciales
     * Verifica la complejidad de las contraseñas dadas
     * La complejidad viene dada por:
     *  Longitud de 8 caracteres
     *  Un número 
     *  Una letra mayúscula
     *  Un caracter especial entre los siguientes . _ @ & + ! $ *
     *  Basado en http://runnable.com/UmrnTejI6Q4_AAIM/how-to-validate-complex-passwords-using-regular-expressions-for-php-and-pcre
     * @param type $password
     * @return boolean
     */
    private function complejidad($password){
        return preg_match_all('$\S*(?=\S{8,})(?=\S*[A-Z])(?=\S*[\d])(?=\S*[*[\.|_|@|&|\+|!|\$|\*])\S*$', $password);
    }

    /**
     * Se encarga del cambio de contraseña y de la devolución de errores
     */
    public function cambioCredenciales(){
        // Tenemos permiso para acceder a esta funcion contenida en este método
        $this->comprobar($this->pagina);
        // ¿Que es lo que tenemos que hacer?
        $rol = $this->index->get('SESSION.rol');
        $usuario = $this->index->get('SESSION.user');
        $passchangeprima = $this->index->get('POST.passchangeprima');
        $passchangeconfirm = $this->index->get('POST.passchangeconfirm');
        if ($passchangeconfirm == $passchangeprima){
            if ($this->complejidad($passchangeprima)) {
                $this->credenciales($rol, $usuario, $passchangeprima);
                //Me encanta rehusar código de esta forma. Recuerda no hacer la redirección desde acá
                $cierre = new \controladores\loginControl();
                $cierre->cerrarSesion();
            } else {
                $this->mensaje[] = array("codigo" => "warning", 'mensaje' => "La contraseña no tiene la complejidad necesaria");
            }
        }else{
            $this->mensaje[] = array("codigo" => "warning", 'mensaje' => "Las contraseñas no coinciden");
        }
        $resultado = array(
            'mensaje' => $this->mensaje, 
            'error' => $this->error);
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
        echo $this->twig->render('main.html.twig', $this->parametros);       
    }
}
