<?php
/**
 * Description of objetosSoap
 *
 * @author vtacius
 */

namespace App\Modelos;

use App\Acceso\zimbraSoapAccess;

class objetosSoap extends zimbraSoapAccess {
    /** 
     * Arreglo de los atributos del usuario. 
     * @var array 
     */
    protected $atributos = array();
    
    /** @var array */
    protected $cuenta = array();
    
    public function getCuenta(){
	    return $this->cuenta;
    }

    // Si necesito algo asì, pero no como en objetosLdap
    // Por ahora, si será igual
    protected function configurarCuenta($usuario){
        $cuenta = $this->getAccount($usuario, 'full');
        // Siempre que la operación se haya realizado existosamente, pues esto retorna un hermoso array
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
        $cuenta = $this->cuenta['mail'];
        unset($this->cuenta['mail']);
        unset($this->cuenta['NAME']);
        print ("Datos actuales de la cuenta en en ObjetosSOAP\n");
        print_r($this->cuenta);
        $this->modificarCuenta($cuenta, $this->cuenta);
    }
    
    public function nuevaEntrada($dn_auth, $mail){
        $password = "Falso_2025";
        $this->crearMailbox($dn_auth, $mail, $password, $this->cuenta);
    }
    
}
