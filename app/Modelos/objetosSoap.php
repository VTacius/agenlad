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

    // Si necesito algo asì, pero no como en objetosLdap
    // Por ahora, si será igual
    protected function configurarDatos($usuario){
        $cuenta = $this->getAccount($usuario, 'full');
        if (array_key_exists('GETACCOUNTINFORESPONSE', $cuenta)) {
            foreach ($cuenta['GETACCOUNTINFORESPONSE']['A'] as $value) {
                if (array_key_exists($value['N'], $this->atributos)) {
                    $this->cuenta[$value['N']] = $value['DATA'];
                }else{
                    $this->cuenta[$value['N']] = "{empty}";
                }
            }
        }else{
            foreach ($this->atributos as $value) {
                $this->cuenta[$value] = "{empty}";
            }
            
        }
        
        
    }
    
}
