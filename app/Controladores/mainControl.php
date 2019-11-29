<?php
namespace App\Controladores;
/**
 * Controlador para el index de la aplicación, en nuestro caso, el lugar donde 
 * cambiamos contraseñas
 * 
 * @version 0.1
 * @author alortiz
 */

class mainControl extends \Clases\sesion {
    
    public function beforeRoute($index){
        $this->datos = json_decode($index['BODY']);
        if(json_last_error() > 0){
            throw new \Error('No envio datos válidos');
        }
    }

    /**
     * Cambiar las contraseñas en el directorio LDAP
     * @param string $username
     * @param string $password
     */
    private function changeLdapPassword($username, $password){
        
        $usuario = new \Modelos\userSamba($this->dn, $this->pswd);
        $usuario->setUid($username);
        $usuario->configuraPassword($password);
        
        
        if ($usuario->actualizarEntrada()) {
            return array('mensaje' => "Contraseña cambiada exitosamente para {$username}");
        }else{
            return array('error' => $usuario->getErrorLdap());
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
     * @param String $password
     * @return boolean
     */
    private function complejidad($password){
        return preg_match_all('$\S*(?=\S{8,})(?=\S*[A-Z])(?=\S*[\d])(?=\S*[*[\.|_|@|&|\+|!|\$|\*])\S*$', $password);
    }

    /**
     * POST /
     * Se encarga del cambio de contraseña y de la devolución de errores
     */
    public function cambioCredenciales(){
        $usuario = $this->datos->usuario;
        $password = $this->datos->password;
        $resultado = array();
        if ($this->complejidad($password)) {
            $resultado = $this->changeLdapPassword($usuario, $password);
        } else {
            $resultado = array('error' => 'La contraseña no tiene la complejidad necesaria');
        }
        print json_encode($resultado); 
    }
}
