<?php

function createFiltro($filtro){
        $filtrado = "(&(&(!(uid=root))(!(uid=nobody)))";
        $atributos = array('uid','cn','title','o', 'ou','mail');
        foreach ($atributos as $value) {
            if (array_key_exists($value, $filtro)) {
                $filtrado .= "($value=$filtro[$value])";
            }
        }
        $filtrado .= $filtrado=="(&(&(!(uid=root))(!(uid=nobody)))" ? "(uid=*))" :  ")";
        return $filtrado;
}

function search( $search, $base = false, $filtro = false){
        if ($base) {
            $this->base =  $base;
        }
        $this->datos = array();
        $atributes = array_keys($search);
        if (!$filtro){
            $filtro = "(&(objectClass=$this->objeto)";
            foreach($search as $indice => $valor){
                $filtro .= "($indice=$valor)";
            }
            $filtro .= ")";
        }
        return $filtro;
        
    }

print createFiltro(array("uid"=>"*"));
$primero = array("uid"=>"*");
$segundo = array("a", "b");
$attribute = array_merge(array_keys($primero), $segundo);

print "\n\n";
print_r($attribute);
