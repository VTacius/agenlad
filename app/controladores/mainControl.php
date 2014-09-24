<?php
namespace controladores;
/**
 * Controlador para el index de la aplicación, especificamente, donde hacemos cambios de contraseña
 *
 * @author alortiz
 */

class mainControl extends \clases\sesion {
    private $hashes;
    private $ldap;
    private $db;
    
    function __construct(){
        // Consigue el objeto F3 en uso mediante el constructor de la clase padre
        parent::__construct();
        // Nombramos la página que hemos de producir
        $this->pagina = "main";
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
    private function changeLdap($password){
        $credenciales = array();
        $credenciales['userPassword'] = $this->hashes->slappasswd($password);
        $credenciales['sambaNTPassword'] = $this->hashes->NTLMHash($password);
        $credenciales['sambaLMPassword'] = $this->hashes->LMhash($password);
        return $this->ldap->modificarEntrada($credenciales);
    }
    
    /**
     * Bifurcación de credenciales para usuarios normales
     * Cambia la contraseña en LDAP
     * @param string $password
     */
    private function cambioPassword($password){
        if ($this->changeLdap($password)) {
            return "Contraseña cambiada con exito";
        }else{
            return ($this->ldap->mostrarERROR());
        }
    }
    
    /**
     * Auxiliar de cambioPasswordAdmin
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
     * Auxiliar de cambioPasswordAdmin
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
     * Bifurcación de credenciales para usuarios con nivel administrativo
     * No sólo cambia la contraseña en LDAP, sino que configura sus firmas en 
     * la base de datos
     * @param type $usuario
     * @param type $password
     */
    private function cambioPasswordAdmin($usuario, $password){
        if(($this->changeLdap($password))){
            $input = $this->obtenerFirma($usuario);
            // Desciframos las firmas con la contraseña actual
            $firmas = $this->hashes->descifrada($input[0]['firmas'], $this->pswd);
            $firmaz = $this->hashes->descifrada($input[0]['firmaz'], $this->pswd);
            // Volvemos a cifrarlas con la contraseña nueva
            $claves = $this->hashes->encrypt($firmas, $password);
            $clavez = $this->hashes->encrypt($firmaz, $password);
            // Ahora, que actualice la firma en la base de datos con la nueva contraseña
            $this->configurarFirma($usuario, $claves, $clavez);
            // Devuelvo un mensaje para la vista
            return "Contraseña cambiada con exito";
        }else{
            return ($this->ldap->mostrarERROR());
        }
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
            return $this->cambioPassword($password);
        }else{
            return $this->cambioPasswordAdmin($usuario, $password);
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
                $retorno = $this->credenciales($rol, $usuario, $passchangeprima);
                print $retorno; 
                //Me encanta rehusar código de esta forma. Recuerda no hacer la redirección desde acá
                $cierre = new \controladores\loginControl();
                $cierre->cerrarSesion();
            } else {
                print("Las contraseña no tiene la complejidad necesaria");
            }
        }else{
            print("Las contraseña no coinciden");
        }
    }
    
    /**
     * Método por defecto
     */
    public function display() {
        // Esto es importante en la vista
        $this->parametros['pagina'] = $this->pagina;
        // ¿Tenemos en serio acceso a esta página?
        $this->comprobar($this->pagina); 
        
//        $this->parametros['datos'] = $mensaje;
        echo $this->twig->render('main.html.twig', $this->parametros);       
    }
}


