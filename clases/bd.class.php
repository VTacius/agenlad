<?php
    ini_set('default_charset', 'utf-8');
    require_once './clases/utilidades.php';
class controlDB {
    // Almacena los errores que puedan producirse
    protected $errorDB = array();
    
    function controlDB(){
        $dbbase = configuracion('dbbase','database');
        $dbserver = configuracion('dbserver','database');
        $dbusuario = configuracion('dbusuario','database');
        $dbpassword = configuracion('dbpassword','database');    
        try {   
            $intentos = new mysqli($dbserver, $dbusuario, $dbpassword, $dbbase);
            if ($intentos->connect_errno){
                throw new Exception($intentos->error, $intentos->errno);   
            }
        } catch(Exception $e ) {
            $this->errorDB = array(
                'titulo' => 'Error en la conexión', 
                'mensaje'=> $intentos->connect_errno);	
        }
    }
    
    function mostrarError(){
      return $this->errorDB;
    }
}
    
    

    
    

  function intento($usuario){
      global $intentos;
      $intentos->query("call inserta_usuario('$usuario')");
      return $intentos->affected_rows;
  }

  function verifica($usuario){
// ¿Esta el usurio bloqueado?
		global $intentos;
		$intentos->query("select user from bloqueados where user='$usuario'");
		return $intentos->affected_rows;
	}

  function logueado( $usuario, $ip ){
// Se ha logueado. Elimina sus culpas anteriores
		global $intentos;
		$elimina = "call logueado('$usuario', '$ip')";
		$intentos->query ($elimina);
	}
    
  function obtenerRol ( $user) {
// Obtener cadena que una vez deserializada versa los sitios que puede usar
      global $intentos;
      $query[0] = "SELECT permisos,rol FROM roles where user='$user'";
      $query[2] = "SELECT permisos,rol FROM roles where user='usuario'";
      
      foreach ($query as $value) {
        $result = $intentos->query ( $value );
        if ( $result->num_rows > 0 ){
          $row = $result->fetch_assoc();
          return $row;
          break;
        }  
      }
      
      
  }
  
 function borrarBandera($user){
   $intentos = new mysqli('10.10.20.56','directorio','directoriol','directorio');
   $query = "UPDATE roles SET bandera = 2 WHERE user='$user'";
   $resultados = $intentos->query($query); 
   return $resultados;
 }
  
  function obtenerbandera ( $user ) {
// Obtengo una cadena que he de deserializar si quiero trabajar con ella
    global $intentos;
    //
    $query[0] = "SELECT bandera,firmas,firmaz FROM roles where user='$user'";
    // O si ya hizo la firma, 1 que aún tiene la bandera
    $query[1] = "SELECT bandera,firmas,firmaz FROM roles where user='$user' and bandera = 1";
    // Veamos si su grupo tiene permisos

    $result = $intentos->query ( $query[0] );
    if ($result->num_rows > 0) {
    // El usuario tiene permisos especiales, revisemos si tiene la bandera entonces
      $result = $intentos->query ( $query[1] );
      if ($result->num_rows > 0) {
      // No tiene la firma. Obtengamos la contraseña plana
        $row = $result->fetch_assoc();
        return $row;
      }else{
        // Ya hizo la firma
        return "a";
      }
    }else{
    // El usuario no tiene vela en el entierro de la administracion
      return "a";
    }  
  }
  
  function obtenerFirma ( $user ) {
  // Obtengo ambas firmas en un array, para los usuarios que tienen ambas
    $intentos = new mysqli('10.10.20.56','directorio','directoriol','directorio');
    $query = "SELECT firmas, firmaz FROM roles WHERE user='$user'";
    if ( ($result = $intentos->query ( $query ) ) ) {
      $row = $result->fetch_assoc();
      return $row ;
    } 
  }
  
  function configuraFirma ( $user, $firmas, $firmaz ) {
  // Obtengo una cadena que he de deserializar si quiero trabajar con ella
    $intentos = new mysqli('10.10.20.56','directorio','directoriol','directorio');
    $query = "UPDATE roles SET firmas='$firmas', firmaz='$firmaz' where user='$user'";
    if ( ($result = $intentos->query ( $query ) ) ) {
      return true ;
    }
  }
