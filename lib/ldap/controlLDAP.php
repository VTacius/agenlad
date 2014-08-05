<?php
/**
 * Clase para manejar el acceso a LDAP
 *
 * @author alortiz
 */
namespace DB;
class controlLDAP {
    //La conexión estará a nivel de clase, y no se piensa usar fuera de acá
    protected $lenl = "";
    protected $errorLDAP = "";
    // El enlace estará a nivel de clase
    protected $lcon;
    // Tendremos a la mano la busqueda lista para ordenarla después
    protected $searchi; 
    // El contenido de todos los datos estará a nivel de clase
    public $datos;
    // El dn del usuario estará a nivel de clase.
    public $dn;
    // El OU, que representa también la base
    public $ou;
  
  /**
		Empiezan métodos necesarios para establecer cualquier conexión
	*/
  
    /**
     * Auxiliar de crearDN por el momento
     * Puede ser usada para crear dn ldap a partir de base DNS
     * Crearemos el dn según la base proporcionada y el tipo 
     * @param string $base
     * @param string $rol
     * @return string
     */
    function crearBase ( $base, $rol='usuario' ){
        switch ($rol) {
            case 'usuario':     
                $this->ou='ou=Users'; break ;
            case 'grupo':
                $this->ou='ou=Groups'; break ;
            default:
                $this->ou = ''; break;
        }
        $dom = explode(".", $base);
        foreach($dom as $j){
            $this->ou .= ",dc=$j";
        }
        return $this->ou;
    }
  
  /**
   * Usada para crear el dn con el que se agrega al usuario dentro del LDAP
   * Y para crear el dn del grupo al que se agrega dicho usuario
   * @param type $uid
   * @param type $base
   * @param type $rol
   */
  function crearDN ( $uid, $base, $rol='usuario' ) {
    switch ($rol) {
      case 'usuario':
        $this->dn =  "uid=$uid," . $this->crearBase( $base, $rol );    
        break;
      case 'grupo':
        $this->dn =  "cn=$uid," . $this->crearBase( $base, $rol );
        break;
      case 'admin':
        $this->dn = "cn=$uid" . $this->crearBase($base, $rol);
        break;
      case 'raw':
		$this->dn = $uid; 
		break;
    }
  }
  
  /**
   * Conseguimos la conexión y la configuramos necesariamente
   * @param string $host
   * @param string $port
   * @return conection
   */
  function conexion($host,$port){
    $this->lcon = ldap_connect($host,$port);
    ldap_set_option($this->lcon, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($this->lcon, LDAP_OPT_NETWORK_TIMEOUT, 1);
//    ldap_set_option($this->lcon, LDAP_OPT_SIZELIMIT, 1);    
//    ldap_set_option($this->lcon, LDAP_OPT_TIMELIMIT, 4);
//    ldap_set_option($this->lcon, LDAP_OPT_REFERRALS, 0);
//    ldap_start_tls($this->lcon);
    return $this->lcon;
  }
  
  /**
   * Realiza el enlace con el servidor LDAP
   * Comprueba que al menos haya credenciales que usar
   * @param string $lpasswd
   * @return boolean
   * @throws Exception
   */
  function enlace($lpasswd){	
      try{
      if (empty($lpasswd) | empty($this->dn)) {
          throw new Exception("Error en la conexión: <b> Credenciales vacías</b>");
      }elseif(($this->lenl = ldap_bind($this->lcon,$this->dn,$lpasswd))){
              return true;
          }else{
              throw new Exception("Error en la conexión: <b>".ldap_error($this->lcon)."</b>");
          }
      }catch(Exception $e){
          $this->errorLDAP = $e->getMessage();	
          return false;
      }
  }
	
  // Terminan métodos necesarios para establecer cualquier conexión 
	
  /**
    Empiezan métodos auxiliares de primer nivel
  */
  
  /**
   * 
   * @return errorLDAP
   */
  function mostrarERROR(){ 
      return $this->errorLDAP;
  }

  /**
   * Hacemos una busqueda y obtenemos resultados en $this->datos
   * @param string $base
   * @param string $filtro
   * @param array $attributes
   * @param int $size
   */
  function datos($base, $filtro, $attributes, $size=1){
    try {
        // Es necesarios silenciar el error para que FatFree no devuelva su error, 
        $this->searchi = @ldap_search ($this->lcon, $base, $filtro, $attributes, 0, $size, $size);
        // probaremos a agregar ldap_sort sin romper compatibilidad
        ldap_sort($this->lcon, $this->searchi, $attributes[0]);
        $this->datos = ldap_get_entries($this->lcon, $this->searchi);
    } catch (Exception $e) {
        $this->errorLDAP = $e->getMessage();
    }
  
  }

  /**
   * Esta funcion sirve únicamente para prearrayAttr
   * Devuelve una cadena con los elementos del array de un atributo separado por comas
   * @param array $attrArray
   * @return string
   */
  function arraytoCadena($attrArray){
    $cadValor = "";
    if (isset($attrArray['count'])) {
      for ($i=0; $i < $attrArray['count']; $i++ ) {
        $cadValor .= $attrArray[$i] . ", ";
      }
    }else{
      $cadValor .= $attrArray . ", ";
    }
      return rtrim($cadValor, ", ");
  }

  // Terminan métodos auxiliares de primer nivel

  /**
      Empiezan metodos para individuos
      En principio, prearrayAttr, arrayAttr y arrayDatosLDAP sirven para trabajar cadenas de un solo usuario
  */

  /**
   * Para uso de arrayAttr
   * Devuelve una cadena que contiene todos los valores de cada atributo
   * Se auxilia de arraytoCadena para recorrer el array atributo
   * @param string $atributo
   * @param array $registro
   * @return string
   */
  function prearrayAttr($atributo, $registro){
      $objeti = "";
      if(array_key_exists($atributo, $registro)){
        $valor = $this->arraytoCadena($registro[$atributo]);
        $objeti .= $valor;
      }else{
        $objeti .= "-";
      }
      return $objeti; 
  }

  /**
   * Para uso de arrayDatosLDAP
   * Devuelve un array más limpio que el original, los índices son el atributo y los valores son ahora cadenas
   * pueda ser más fácil mostrar los datos
   * @param type $atributos
   * @param type $registro
   * @return type
   */
  function arrayAttr($atributos, $registro){
      $objetu = array();
      foreach ($atributos as $i){
          $cadena = $this->prearrayAttr($i, $registro);
          $objetu[$i] = $cadena;
      }
      return $objetu; 
  }
    
  /**
   * Devuelve un array con informacion de los usuarios tomados desde LDAP
   * @param type $atributos
   * @return array
   */
	function arrayDatosLDAP($atributos){
	// Funcion final de presentación: Es la que usaremos dentro de cada página
		$objecto = array();
		for ($i=0; $i < $this->datos['count']; $i++) {
			array_push($objecto, ($this->arrayAttr($atributos, $this->datos[$i])));
		}
		return $objecto;
	}
    
	//	Terminan los métodos para individuos
	
	/**
		Empiezan metodos para Grupos de datos
		En principio, preaceldaAttr, celdaAttr y celdaDatosLDAP sirven para trabajar array con informacion de varios usuarios
	*/
    
	function preceldaAttr($atributo, $registro){
	// Para uso de arrayAttr
	// Devuelve una cadena que contiene todos los valores de cada atributo
	// Se auxilia de arraytoCadena para recorrer el array atributo
		$objeti = "";
		if(array_key_exists($atributo, $registro)){
				$valor = $this->arraytoCadena($registro[$atributo]);
				$objeti .= $valor;
			}else{
				$objeti .= "-";
			}
		return $objeti; 
	}

	function celdaAttr($atributos, $registro){
	// Para uso de celdaDatosLDAP
	// Devuelve un array más limpio que el original, los índices son el atributo y los valores son ahora cadenas
	// pueda ser más fácil mostrar los datos
		$objetu = "<tr>";
		foreach ($atributos as $i){
			$cadena = $this->preceldaAttr($i, $registro);
			$objetu .= "<td>" . $cadena . "</td>";
		}
		$objetu .= "<tr>";
		return $objetu; 
	}

	function datosSelect ($atributo, $listado){
	// Para uso de celdaDatosLDAP
	// Forma item de un selecto con value igual al nombre en minisculas y sin espacios
	// ADVERTENCIA: Use sólo atributos únicos
	  $cadena = "";
  	foreach ($listado as $i) {
    	$cadena .= '<option value=" '. $i[$atributo[1]] .' ">' . $i[$atributo[0]] . '</option> ';
  	}
		return $cadena; 
	}
	
	function celdaDatosLDAP($atributos){
	// Esta es una funcion final de presentación: Es la que usaremos dentro de cada página
		$objecto = array();
		for ($i=0; $i < $this->datos['count']; $i++) {
			array_push($objecto, ($this->celdaAttr($atributos, $this->datos[$i])));
		}
		return $objecto;
	}
	//	Terminan los métodos para individuos
	
	/*
		Empiezan métodos para manipulación de datos
	*/	
	
  function modEntrada ($valores) {
    try{
      if (ldap_modify($this->lcon, $this->dn, $valores)) {
        return true;
      } else {
        throw new Exception("Error Manipulando datos: <b>".ldap_error($this->lcon)."</b>");
      }
    }catch(Exception $e){
      print $this->dn;
      $this->errorLDAP = $e->getMessage();	
      return false;
    }
  }

	function nuevaEntrada( $valores, $entry ) {
		try{
			if (ldap_add($this->lcon, $entry, $valores)) {
				return true;
			} else {
				throw new Exception("Error Manipulando datos: <b>".ldap_error($this->lcon)."</b>");
			}
		}catch(Exception $e){
			$this->errorLDAP = $e->getMessage();
			return false;
		}
	}

	function agregarAtributosGrupo( $valores, $entry ) {
	// En realidad, parece que esta función agrega un atributo del tipo 
	// "permito varios y no me ahuevo"
		try{
			if (ldap_mod_add($this->lcon, $entry, $valores)) {
				return true;
			} else {
				throw new Exception("Error Manipulando datos: <b>".ldap_error($this->lcon)."</b>");
			}
		}catch(Exception $e){
			$this->errorLDAP = $e->getMessage();
			return false;
		}
	}
	/*
		Terminan métodos para manipulación de datos	
	*/
}
