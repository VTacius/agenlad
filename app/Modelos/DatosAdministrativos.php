<?php

namespace App\Modelos;

class DatosAdministrativos {
    private $atributos;
    private $conexion;  
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
        $this->atributos = Array('pregunta', 'respuesta', 'fecha_nacimiento', 'jvs', 'nit');
    }

    protected function filtrarAtributos($datos){
        return array_filter($this->atributos, function($item) use ($datos) {
            return array_key_exists($item, $datos);
        }); 
    }

    private function existeEntrada($usuario){
        $parametros = Array('usuario' => $usuario);
        $this->conexion->exec('SELECT usuario FROM datos_administrativos WHERE usuario=:usuario', $parametros);
        return $this->conexion->count() === 1;
    }

    protected function actualizarEntrada($atributos, $datos){
        $resultado = \array_reduce($atributos, function($acumulador, $item){
            $acumulador .= "$item=:$item, ";
            return $acumulador;
        }, "");

        $resultado = substr($resultado, 0, -2);
        
        return "update datos_administrativos set {$resultado} where usuario=:usuario";
    }

    protected function crearEntrada($atributos, $datos){
        $resultado = \array_reduce($atributos, function($acumulador, $item){
            $acumulador['c'] .= "$item, ";
            $acumulador['i'] .= ":$item, ";
            return $acumulador;
        }, Array('c' => "usuario, ", 'i' => ":usuario, " ));
        
        $resultado = array_map(function($item){
            return \substr($item, 0, -2);
        }, $resultado);

        return "insert into datos_administrativos({$resultado['c']}) values({$resultado['i']})";
    }

    protected function crearOperacion($usuario, $atributos, $datos){
        if ($this->existeEntrada($usuario)) {
            $sentencia = $this->actualizarEntrada($atributos, $datos);
        } else {
            $sentencia = $this->crearEntrada($atributos, $datos);
        }
        
        $valores = array_reduce($atributos, function($acumulador, $item) use ($datos){
            $acumulador[$item] = $datos[$item];
            return $acumulador;
        }, Array());
        
        if (array_key_exists('fecha_nacimiento', $valores)){
           $fecha = \DateTime::createFromFormat('d/m/Y', $valores['fecha_nacimiento']);
           $valores['fecha_nacimiento'] = $fecha->format('Y/m/d');
        }
        
        return Array($sentencia, $valores);
    }

    public function dateador($usuario, $datos){
        $atributos = $this->filtrarAtributos($datos);
        list($operacion, $parametros) = $this->crearOperacion($usuario, $atributos, $datos);
        print ("Datos actuales de los parametros en DatosAdministrativos\n");
        print_r($parametros);
        
        if (sizeof($parametros) > 0 ){
            $parametros['usuario'] = $usuario;
            return $this->conexion->exec($sentencia, $parametros);
        } 
        
        return false;   
       
    }
}