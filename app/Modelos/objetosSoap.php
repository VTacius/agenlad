<?php
/**
 * Description of objetosSoap
 *
 * @author vtacius
 */

namespace Modelos;

class objetosSoap extends \Acceso\zimbraSoapAccess{
    /** 
     * Arreglo de los atributos del usuario. 
     * @var array 
     */
    protected $atributos = array();
    
    /** @var array */
    protected $cuenta = array();
    
    public function __construct($administrador, $password) {
        parent::__construct($administrador, $password);
    }

    public function getCuenta(){
	return $this->cuenta;
    }

    // Si necesito algo asì, pero no como en objetosLdap
    // Por ahora, si será igual
    protected function configurarDatos($usuario){
        $cuenta = $this->getAccount($usuario, 'full');
        // Siempre que la operación se haya realizado existosamente, pues esto retorna un hermoso array
        // print_r($cuenta);
        if (array_key_exists('GETACCOUNTRESPONSE', $cuenta)) {
	    $datos = $cuenta['GETACCOUNTRESPONSE']['ACCOUNT'];
	    $this->cuenta['NAME'] = $datos['NAME'];
            foreach ($datos['A'] as $attr) {
		if (in_array($attr['N'], $this->atributos)){
                    $this->cuenta[$attr['N']] = $attr['DATA'];
		}
            }
        }else{
            foreach ($this->atributos as $value) {
                $this->cuenta[$value] = "{empty}";
            }
            
        }
    }
    
    public function actualizarEntrada(){
        // De hecho, creo que algo así deberías ponerlo en objetosLdap
        unset($this->cuenta['mail']);
        // Odio no saber que es lo que quite de acá, pero me parece que es el DN
	    $cuenta = array_shift($this->cuenta);
        $this->modificarCuenta($cuenta, $this->cuenta);
    }
    
    public function nuevaEntrada($dn_auth, $mail){
        $password = "Falso_2025";
        $this->crearMailbox($dn_auth, $mail, $password, $this->cuenta);
    }
    
}
