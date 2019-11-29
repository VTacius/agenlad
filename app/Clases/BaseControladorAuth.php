<?php

namespace App\Clases;

use App\Clases\Cifrado;
use App\Clases\BaseControlador;

class BaseControladorAuth extends  BaseControlador {
    
    public function beforeRoute($index){
        $this->ipaddress = $index['IP'];
        
        $this->tokens = new \stdClass();
        if(array_key_exists('Authentication', $index['HEADERS'])){
            $this->tokens->usuario = explode(' ', $index['HEADERS']['Authentication'])[1]; ;
        } else {
            $index->error(401);
        }
        $this->tokens->maestro = $index['claveMaestra'];

        $this->cifrado = new Cifrado();
    }

}