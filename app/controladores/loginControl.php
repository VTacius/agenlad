<?php
namespace controladores;
/**
 * Controlador para el manejo de sesión
 *
 * @author alortiz
 */

class loginControl extends \clases\sesion{
    private $server;
    private $puerto;
    private $usuario;
    private $password;
    
    /**
     * El constructo inicia las variables necesarias para conectarse al servidor LDAP
     * @param type $base
     */
    public function __construct(){
        parent::__construct();
        // Conseguimos las variables hasta el servidor LDAP
        $this->usuario = $this->index->get('POST.user'); 
        $this->password = $this->index->get('POST.pswd');
        $this->server = "ldap://" . $this->index->get('sserver');
        $this->puerto = $this->index->get('spuerto');
    }
    
    /**
     * muestra la página de inicio, incluyendo un mensaje opcional que espero mencionar en el futuro
     * @param type $base
     */
    public function display(){
        $this->comprobarSesion();
        $mensaje = $this->index->get('PARAMS.mensaje');
        $twig = $this->index->get('twig');
        $parametros = array(
            'mensaje' => $mensaje
        );
        echo $twig->render('login.html.twig', $parametros);
    }
    
    /**
     * Inicia una conexión SQL
     * Compatibilidad, me encanta esa palabra
     * @return \DB\SQL
     */
    private function conectarDB(){
        return $this->index->get('dbconexion');
    }
    
    /**
     * Una vez logueado, se borran todos los intentos anteriores del usuario
     * @param string $usuario
     */
    protected function logueado($usuario){
        $base = $this->conectarDB();
        // Operamos
        $cmds = 'delete from intentos where user=:user';
        $args = array('user'=>$usuario);
        $base->exec($cmds, $args);
    }
    
    private function cifrarEnPrimerLogueo($resultado, $password, $usuario){
            $base = $this->conectarDB();
            //El usuario es un administrador que no se ha logueado antes
            // Es necesarios cifrar las contraseñas en la base
            $hashito = new \clases\cifrado();
            $firmas = $resultado[0]['firmas'];
            $firmaz = $resultado[0]['firmaz'];
            $pwds = $hashito->encrypt($firmas, $password);
            $pwdz = $hashito->encrypt($firmaz, $password);
            $cmds = "UPDATE user SET firmas=:firmas, firmaz=:firmaz, bandera=:bandera where user=:user";
            $args = array('firmas'=>$pwds,'firmaz'=>$pwdz, 'bandera'=> '2', 'user'=>$usuario);
            $base->exec($cmds, $args);
    }
    
    /**
     * Si el usuario es administrador, comprueba si ya se logueado antes (bandera=2) 
     * o nunca (bandera=1)
     * @param string $usuario
     * @return string|array
     */
    private function banderaAdmin($usuario, $password){
        $base = $this->conectarDB();
//        // Operamos
////        $cmds = 'select rol, bandera, firmas, firmaz from roles where user=:user and bandera=1';
        $cmds = 'select bandera, firmas, firmaz from user where user=:user and bandera=1';
        $args = array('user'=>$usuario);
        $resultado = $base->exec($cmds, $args);
        if ($base->count()>0){
            $this->cifrarEnPrimerLogueo($resultado, $password, $usuario);
        }
        // Al final, siempre hemos de retornar esto
        $cmds = 'select titulo as rol, permisos, firmas, firmaz from user join rol on user.rol=rol.rol where user=:user';
        $args = array('user'=>$usuario);
        $resultado = $base->exec($cmds, $args);
//        print_r($resultado);
//        exit();
        return $resultado;
    }
    
    /**
     * El usuario es administrador o no
     * @param string $usuario
     * @return string|array
     */
    protected function obtenerBandera($usuario, $password){
        $base = $this->conectarDB();
        // Operamos
        $cmds = 'select permisos, firmas, firmaz from user join rol on user.rol=rol.rol where user=:user';
        $args = array('user'=>$usuario);
        $resultado = $base->exec($cmds, $args);
        if ($base->count()>0){
            // El usuario tiene un rol de administrador
            return $this->banderaAdmin($usuario, $password);
        }else{
            // Acá no es administrador, por lo que seguimos
            $cmds = 'select titulo as rol, permisos from user join rol on user.rol=rol.rol where user=:user';
            $resultado = $base->exec($cmds, $args);
            return $resultado;
        }
    }

    /**
     * Inserta un intento de logueo una vez el usuario se ha equivocado
     * @param string $usuario
     */
    protected function intentoUsuario($usuario){
        $base = $this->conectarDB();
        // Operamos
        $cmds = 'insert into intentos(user) values(:user)';
        $args = array('user'=>$usuario);
        $base->exec($cmds, $args);
    }

    /**
     * Si el usuario tiene más de tres intentos, lo redirige con mensaje "Su usuario esta bloqueado"
     * @param string $usuario
     */
    protected function comprobarBloqueo($usuario){
        $base = $this->conectarDB();
        // Operamos
        $cmds = "select count(user) as intentos from intentos where user=:user";
        // Funciona sin los dos puntos y con los dos puntos antes del índice
        $pams = array(':user' => $usuario);
        $resultado = $base->exec($cmds, $pams)[0]['intentos'];
        if ($resultado >3){
            $this->index->reroute('@login_mensaje(@mensaje=Su usuario esta bloqueado)');
        }else{
            $this->intentoUsuario($usuario);
        }
    }
    
    /**
     * Controlador para el proceso de autenticacion
     * TODO: Quizá puedas meter después los datos ('uid', 'gecos', 'mail', 'o','ou', 'title','gidnumber')
     * si bien nunca los usaste
     */
    public function autenticar(){
        $this->comprobarBloqueo($this->usuario);
        $login = new \Auth('ldap', array(
            'dc' => $this->server,
            'rdn' => $this->index->get('lectorldap'),
            'base_dn'=> $this->index->get('sbase'),
            'pw' => $this->index->get('passwdldap')
                )
        );
        if (@$login->login($this->usuario, $this->password)){
            $this->logueado($this->usuario);
            // Iniciamos la sesion con datos a guardar en la base de datos
            $db = $this->index->get('dbconexion');
            $sesion = new \DB\SQL\Session($db);
            $this->index->set('SESSION.user', $this->usuario);
            $this->index->set('SESSION.dn', $login->getDN());
            $this->index->set('SESSION.pswd', $this->password);
            $roles = $this->obtenerBandera($this->usuario, $this->password);
            $this->index->set('SESSION.permisos', unserialize($roles[0]['permisos']));
            $this->index->set('SESSION.rol', $roles[0]['rol']);
            $this->index->set('SESSION.firmas', $roles[0]['firmas']);
            $this->index->set('SESSION.firmaz', $roles[0]['firmaz']);
            // Ha finalizado el procedimiento
            $this->index->reroute('@main');
        }else{
            $this->intentoUsuario($this->usuario);
            $this->index->reroute('@login_mensaje(@mensaje=Credenciales incorrectas)');
        }
    }
    
    /**
     * Auxiliar para cerrar y cerrarMensaje
     * Acciones para el cierre de sesión
     */
    private function cerrarSesion(){
        $db = $this->index->get('dbconexion');
        new \DB\SQL\Session($db);
        $this->index->clear('SESSION');
    }
    /**
     * Controlador para cierre normal de sesión
     */
    public function cerrar(){
        $this->cerrarSesion();
        $this->index->reroute('@login_mensaje(@mensaje=Ha cerrado la sesión)');
    }
    
    /**
     * Controlador para cierre de sesión con mensaje definido
     * en PARAMS.mensaje
     */
    public function cerrarMensaje(){
        $this->cerrarSesion();
        $mensaje = $this->index->get('PARAMS.mensaje');
        $this->index->reroute('@login_mensaje(@mensaje='. $mensaje .')');
    }
}
