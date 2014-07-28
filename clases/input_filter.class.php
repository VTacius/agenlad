<?php
ini_set('default_charset','UTF-8');
/**
 * Clase para verificación de los parametros que se le pasan al script
 *
 * @author alortiz
 */

class verificador {
  private $estamento = array();
  private $comprobando = TRUE;
  
  /**
   * Contructor que se inicia con un array que especifica las variables a usar
   * @param array $correspondencia
   */
  function verificador ($correspondencia) {
    foreach ($correspondencia as $vary => $llamada) {
      if ($llamada[0] === 6) {
        $this->filtros($vary, $llamada);
      }else{
        $this->filtrop($vary, $llamada);
      }
    }
  }
  
  private function filtros ($vary, $llamada) {
    if ( (isset($_SESSION[$vary]) ) ){
      $final_res = self::$llamada[1]($_SESSION[$vary]);
      $this->estamento[$vary] = $final_res[0];
    } else if ($llamada[2] == "n") {
      $this->comprobando = FALSE;
    } else {
      $this->comprobando = TRUE;
    }
  }
  
  private function filtrop ( $vary, $llamada ){
    $final_res = "";
    if ( ($final_res = filter_input(
                                $llamada[0], 
                                $vary, 
                                FILTER_CALLBACK, 
                                array("options"=>array($this, $llamada[1] ) ) ) ) ) 
    {
      $this->estamento[$vary] = $final_res[0];
    } else if ($llamada[2] == "n") {
      $this->comprobando = FALSE;
    } else {
      $this->comprobando = TRUE;
    }
  }
  
  /**
   * La variable en cuestión es un arreglo
   * @param type $arreglo
   * @return boolean
   */
  static function verificaArreglo( $arreglo ){
    $variables = ""; $matches = "";
    if (count($arreglo) > 0) {
      $variables[0] = $arreglo;
      return $variables;
    }else{
      return FALSE;
    }
  }
  
  /**
   * Cualquier contenido posible hasta 150 caracteres
   * @param type $cadena
   * @return boolean|string
   */
  static function verificaContenido( $cadena ){
    $variables = ""; $matches = "";
    if (preg_match('/^.{1,150}$/', $cadena, $matches)) {
      $variables = $matches;
      return $variables;
    }else{
      return FALSE;
    }
  }
  
  /**
   * Cadenas de texto tipo nombres mediante regex
   * @param type $cadena
   * @return boolean|string
   */
  static function verificaNombres( $cadena ){
    $variables = ""; $matches = "";
    if ( preg_match('/^([A-Za-z]+\s*)+$/', $cadena, $matches)) {
      $variables = $matches;
      return $variables;
    }else{
      return FALSE;
    }  
  }
  
  /**
   * Números mediante regex
   * @param type $numero
   * @return boolean|string
   */
  static function verificaNumeros ( $numero ) {
    $variables = ""; $matches = "";
    if ( preg_match("/^([0-9]+\s*)+$/", $numero, $matches) ) {
      $variables = $matches[0];
      return $matches;
    }else{
      return FALSE;
    }
  }
  
  /**
   * Retorna el array estamento, que contiene todas las variables que han pasado sus pruebas
   * @return array
   */
  function resultar(){
    return $this->estamento;
  }
  
  /**
   * Retorna el boleano comprobando, que es false cuando todas las variables 
   * marcadas con n han pasado su prueba
   * @return boolean
   */
  function comprobar () {
     return $this->comprobando;
  }
}

//$correspondencia["esta"] = array(1, "verificaNombres", "u");
//$correspondencia["esto"] = array(1, "verificaNumeros", "u");
//$correspondencia['REMOTE_ADDR'] = array( 5, 'verificaContenido', 'n' );
//$muestra = new verificador($correspondencia);
//
//print "Ya estoy fuera<br>";
//
//if ( $muestra->comprobar()) {
//  $variable = $muestra->resultar();
//} else {
//  $variable = array("Resultado" => "Faltan algunos valores o su contenido es incorrecto");
//}
//
//foreach ($variable as $key => $value) {
//  print "$key: $value <br>";
//}