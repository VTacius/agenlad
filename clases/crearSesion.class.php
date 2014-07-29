<?php
require_once('./clases/bd.class.php');
require_once('./clases/conect.class.php');
require_once './clases/cifrado.class.php';
/**
 * Debido a lo extrañamente complicado de crear sesión, pues la hacemos clase para
 * complicarla aún más.
 *
 * * En lugar de redirigir con heade, lanzamos una excepción
 *
 * @author alortiz
 */

class crearSesion {
    protected $bd;
    protected $ld;
    protected $server;
    protected $puerto;
    protected $user;
    protected $pass;
    protected $dominio;
    private $login;
    private $base;
    
    function crearSesion($server, $puerto, $user, $pass, $dominio){
        //Inicio todas las clases necesarias
        $this->bd = new controlDB();
        $this->ld = new controlLDAP();
        $this->server = $server;
        $this->puerto = $puerto;
        $this->user = $user;
        $this->pass = $pass;
        $this->dominio = $dominio;
        $this->login = new controlLDAP();
        $this->login->conexion($server, $puerto);
        $this->login->crearDN($user,$dominio);
        $this->base = $this->login->crearBase($dominio);
    }
    
    /**
     * Consulta para averiguar gidNumber y sambaSID del grupo
     * @param string $logueo
     * @param string $gidnumber
     * @return array
     */
    function mostrarGrupo ($gidnumber) {
        $atributos = array("cn");
        // Debe volver a configurase la base en 'grupo', no servira si se usa la base por defecto
        $base = $this->login->crearBase($this->dominio,'grupo');
        $filtro = "(&(objectclass=posixgroup)(gidnumber=$gidnumber))";
        // Crear una función tan bonita como esa, para al final no poder usarla
        $this->login->datos($base, $filtro, $atributos, 1);
        $contenido = $this->login->arrayDatosLDAP($atributos);
        return $contenido;
    }


    /**
     * Hace una busqueda de algunos atributos del usuario en LDAP y BD
     * Luego, llena $_SESSION con ellos
     * @param string $user
     * @param string $pass
     */
    function llenardatos ($user, $pass) {
      // Buscamos por algunos datos en LDAP
        $atributos = array('uid', 'gecos', 'mail', 'o','ou', 'title','gidnumber');
        $filtro = "uid=" . $user;
        $this->login->datos($this->base, $filtro, $atributos, 1);
        $datos = $this->login->arrayDatosLDAP($atributos);
        $grupo = $this->mostrarGrupo($datos[0]['gidnumber']);
        $datos[0]['cn'] = $grupo[0]['cn'];
        //Empezamos a llenar la sesión
        $_SESSION['user'] = $user;
        $_SESSION['pass'] = $pass;
        $_SESSION['datos']= $datos;      
        // La siguiente operacion es cortesia de la base de datos
        $permisos = $this->bd->obtenerRol($user);
        $_SESSION['permisos'] = unserialize($permisos['permisos']);
        $_SESSION['rol'] = $permisos['rol'];
    }


    /**
     * Una vez logueado, el usuario debe verificar que tiene acceso a las contraseñas
     * cifradas de administrador
     * Luego, hace uso de llenardatos()
     * @param string $user
     * @param string $pass
     */
    function banderar($user, $pass) {
      // ¿Es el usuario administrador?
        $bandera = $this->bd->obtenerBandera($user);
        if ( $bandera == "a" ){
          // Ya tengo una firma en la base de datos
            $this->llenardatos($user, $pass);
        } else {
            // Ciframos la contraseña con los nuevos miembros de la clase
            $pwds = $this->hashito->encrypt( $bandera['firmas'], $pass );
            $pwdz = $this->hashito->encrypt( $bandera['firmaz'], $pass );
            // Ahora, que actualice la firma en la base de datos con la nueva contraseña
            $this->bd->configuraFirma($user, $pwds, $pwdz);
            // Le quitamos la bandera
            $this->bd->borrarBandera($user);
            // Llenamos con los datos del usuario
            $this->llenardatos($user, $pass);
        }
    }

    /**
     * Intenta crear la sesión con las credenciales dadas. 
     * Si verdadero, loguea, revisa bandera y le lleva al inicio
     * Si falso, le marca un intento fallido y le deja allí mismo
     * @global controlLDAP $login
     * @global array $lg
     * @param string $user
     * @param string $pass
     */
    function credenciales($user, $pass, $remota){
        if ($this->login->enlace($pass) ){
            $this->bd->logueado ($user, $remota);		
            // Creemos que es seguro colocarlo acá
            // Las funciones anteriores lanzan un error si es que no es así
            session_start();
            $this->banderar ($user, $pass);
            header('Location: clases/sesion.php');
        }else{
            $this->bd->intento($user);
            throw new Exception("Credenciales incorrectas");
        }
    }

    /**
     * Verifica que el usuario aún tenga intentos disponibles
     * Si verdadero, usa a credenciales
     * @param string $user
     * @param string $pass
     */
    function sesionar($user, $pass, $remota){
        if ( $this->bd->verificaPaso($user) ){
            throw new Exception("Su usuario esta bloqueado");
        }else{
            $this->credenciales($user, $pass, $remota);
        }
    }
}
