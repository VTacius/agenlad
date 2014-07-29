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
        $this->base = $this->login->crearBase("salud.gob.sv");
    }
    
    /**
     * Consulta para averiguar gidNumber y sambaSID del grupo
     * NO HACE LO QUE DESCRIBI HACE MUCHO
     * @global string $base
     * @param string $logueo
     * @param string $gidnumber
     * @return array
     */
    function mostrarGrupo ($gidnumber) {
        $atributos = array("cn");
        $filtro = "(&(objectClass=posixGroup)(gidnumber=$gidnumber))";
        $bg = $this->login->crearBase($this->base, 'grupo');
        $this->login->datos($bg, $filtro, $atributos, 1);
        return $this->login->arrayDatosLDAP($atributos);
    }


    /**
     * Hace una busqueda de algunos atributos del usuario en LDAP y BD
     * Luego, llena $_SESSION con ellos
     * @global string $base
     * @global controlLDAP $login
     * @param string $user
     * @param string $pass
     */
    function llenardatos ($user, $pass) {
      // Buscamos por algunos datos en LDAP
        $atributos = array('uid', 'gecos', 'mail', 'o','ou', 'title','gidnumber');
        $filtro = "uid=" . $this->user;
        $basel = $this->login->crearBase($this->base);
        $this->login->datos($basel, $filtro, $atributos, 1);
        $datos = $this->login->arrayDatosLDAP($atributos);
        $grupo = mostrarGrupo($datos[0]['gidnumber']);
        $datos[0]['cn'] = $grupo[0]['cn'];
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
     * @global cifrado $hashito
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
            //$this->banderar ($user, $pass);
            header('Location: index.php');
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
    function crearSession($user, $pass){
        if ( $this->bd->verificaPaso($user) == 0 ){
            $this->credenciales($user, $pass);
        }else{
          throw new Exception("Su usuario esta bloqueado");
        }
    }
}
