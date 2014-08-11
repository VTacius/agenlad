<?php
    ini_set('default_charset', 'utf-8');
    require_once '/var/www/agenlad/herramientas/utilidades.php';
class controlDB {
    // Almacena los errores que puedan producirse
    protected $errorDB = array();
    // La conexión nunca saldrá
    protected $conexion;
    // El fetch, parece
    protected $consulta; 
    
    /**
     * Contructor de conexión a la base de datos 
     * con parametros desde archivo de configuracion 
     * y captura de errores
     * @throws Exception
     */
    
    function controlDB(){
        $dbbase = configuracion('dbbase','database');
        $dbserver = configuracion('dbserver','database');
        $dbusuario = configuracion('dbusuario','database');
        $dbpassword = configuracion('dbpassword','database');    
        try {   
            $this->conexion = new mysqli($dbserver, $dbusuario, $dbpassword, $dbbase);
            if ($this->conexion->connect_errno){
                throw new Exception($intentos->error, $intentos->errno);   
            }
        } catch(Exception $e ) {
            $this->errorDB = array(
                'titulo' => 'Error en la conexión', 
                'mensaje'=> $this->conexion->connect_errno);	
        }
    }
    
    /**
     * Devuelve las filas afectadas por la consulta que se ha pasado como parametro
     * @param string $sentencia consulta sql 
     * @param string $usuario
     * @return integer
     * @throws Exception
     */
    
    protected function afectados($sentencia){
        try {
          $this->consulta = $this->conexion->query($sentencia);
          $error = $this->conexion->errno;
          if ($error){
            throw new Exception($this->conexion->errno);
          }
        } catch (Exception $e) {
          $this->errorDB = array(
                'titulo' => 'Error en la consulta', 
                'mensaje'=> $e->getMessage());
        }
        return $this->conexion->affected_rows;
    }
    
    /**
     * Inserta un intento de parte del usuario por conectarse
     * Y verifica que no este bloqueado
     * Todo con el procedimiento inserta_usuario
    DELIMITER ;;
      CREATE PROCEDURE `inserta_usuario`(IN usuario VARCHAR(50))
        BEGIN
          INSERT INTO intentos(user) VALUES(usuario);
          SELECT @intentos := (SELECT COUNT(user) AS intentos FROM intentos WHERE user=usuario AND DATE(estampa) = CURDATE()) AS intento;
          IF @intentos = 4 
            THEN
            INSERT INTO bloqueados(user) VALUES(usuario);
          END IF;
        END ;;
    DELIMITER ;
     * @param string $usuario
     * @return integer
     */
    
    function intento ($usuario){
      $attr = $this->conexion->real_escape_string($usuario);
      $sentencia = "call inserta_usuario('$attr')";
      return $this->afectados($sentencia);
    }
    
    /**
     * Regresa verdadero si el usuario no esta en la tabla bloqueados
     * @param string $usuario
     * @return boolean
     */
    
    function verificaPaso($usuario){
        $attr = $this->conexion->real_escape_string($usuario);
		$sentencia = "select user from bloqueados where user='$attr'";
        if ($this->afectados($sentencia)==1){
            return TRUE;
        }else{
            return FALSE;
        }
	}
    
    /**
     * Cuando el usuario se ha logueado, lo elimina de la tabla bloqueados
     * mediante procedimiento 
    DELIMITER ;;
    CREATE DEFINER=`directorio`@`%` PROCEDURE `logueado`(IN usuario VARCHAR(50), IN ip_address VARCHAR(16))
    BEGIN
      INSERT INTO intentos(user) VALUES(usuario);
      DELETE FROM intentos WHERE user = usuario;
      DELETE FROM bloqueados WHERE user = usuario;
      INSERT INTO accesos (user, ip ) VALUES(usuario, ip_address);
    END ;;
    DELIMITER ;
     * @param type $usuario
     * @param type $ip
     */
    
    function logueado( $usuario, $ip ){
        $user = $this->conexion->real_escape_string($usuario);
        $ipaddress = $this->conexion->real_escape_string($ip);
		$sentencia = "call logueado('$user', '$ipaddress')";
		$this->afectados($sentencia);
	}
    
    /**
     * Después del primer logueo, borra la bandera que se pone cuando el usuario 
     * es creado
     * @param string $usuario
     * @return integer
     */
    
    function borrarBandera($usuario){
        $user = $this->conexion->real_escape_string($usuario);
        $sentencia = "UPDATE roles SET bandera = 2 WHERE user='$user'";
        $resultados = $this->afectados($sentencia); 
        return $resultados;
    }
    
    /**
     * Obtiene el rol expresado en una arreglo json con sus permisos desde la tabla roles
     * @param string $usuario
     * @return json
     */
    
    function obtenerRol($usuario){
        $user = $this->conexion->real_escape_string($usuario);
        $sentencias[0] = "SELECT user,permisos,rol FROM roles where user='$user'";
        $sentencias[1] = "SELECT user,permisos,rol FROM roles where user='usuario'";
        foreach ($sentencias as $sentencia) {
            $permisos = $this->afectados($sentencia);
            //  Odio este if. Hacer morder el polvo de POO
            if ($this->consulta->num_rows > 0){
                $fila = $this->consulta->fetch_assoc();
                return $fila;
            }
        }
        return $permisos;    
    }
    
    function mostrarError(){
        return $this->errorDB;
    }
    
    /**
     * NECESITA PRUEBAS
     * Si el usuario es administrador, comprueba si tiene o no la bandera
     * @param string $usuario
     * @return string
     */
    protected function banderaAdmin ($usuario){
        // 0 si ya hizo la firma, que aún tiene bandera
        $sentencia = "SELECT bandera,firmas,firmaz FROM roles where user='$usuario' and bandera = 1";
        $bandera = $this->afectados($sentencia);
        if ($bandera>0){
            // El usuario es un administrador que no se ha logueado antes, 
            // Obtenemos la contraseña para usarla
            $row = $this->consulta->fetch_assoc();
            return $row;
        }else{
            // Ya hizo la firma
            return "a";
        }
    }
    
    /**
     * NECESITA PRUEBAS
     * Si el usuario es administrador o no
     * @param strin $usuario
     * @return string
     */
    function obtenerBandera ($usuario){
        $user = $this->conexion->real_escape_string($usuario);
        // El usuario tiene bandea 0 si su rol es administrador y nunca se ha
        // logueado antes
        $consulta = "SELECT bandera,firmas,firmaz FROM roles where user='$user'";
        
        $bandera = $this->afectados($consulta);
        if ($bandera>0){
            return $this->banderaAdmin($user);
        }else{
            // El usuario no es administrador
            return "a";
        }           
    }
    
    /**
     * Obtiene firmas y firmaz en un array.
     * Un dia te darás cuenta que podrías hacerlo con obtener bandera, pero ese día será muy tarde ya
     * @param string $usuario
     * @return diccionario
     */
    function obtenerFirma($usuario){
        $user = $this->conexion->real_escape_string($usuario);
        $sentencia = "SELECT firmas, firmaz FROM roles WHERE user='$user'";
        $this->afectados($sentencia);
        $row = $this->consulta->fetch_assoc();
        return $row ;
    }
    
    /**
     * Configura las firmas.
     * Cuando el usuario se loguea por primera vez (Verificado por obtenerBandera
     * en ayuda con banderaAdmin), se usa obtenerBandera para obtener la contraseña 
     * de administrador samba y administrador zimbra en texto claro, luego, 
     * las configuramos cifradas de ser posible
     * @param string $usuario
     * @param string $firmas
     * @param string $firmaz
     */
    function configuraFirma($usuario, $firmas, $firmaz){
        $user = $this->conexion->real_escape_string($usuario);
        $sigs = $this->conexion->real_escape_string($firmas);
        $sigz = $this->conexion->real_escape_string($firmaz);
        $sentencia = "UPDATE roles SET firmas='$sigs', firmaz='$sigz' where user='$user'";
        $this->afectados($sentencia);
        
    }
}
