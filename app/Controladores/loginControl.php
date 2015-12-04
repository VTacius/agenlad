<?php
namespace Controladores;
/**
 * Controlador para el manejo de sesión
 *
 * @author alortiz
 */

class loginControl extends \Clases\sesion{
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
        return $this->index->get('dbsession');
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
    
    /**
     * El usuario es administrador o no
     * Si el usuario es administrador, envia a cifrarEnPrimerLogueo para cifrar o no 
     * las credenciales
     * @param string $usuario
     * @return string|array
     */
    protected function obtenerBandera($usuario, $password){
        $base = $this->conectarDB();
        // Operamos
        $cmds = "select titulo, user.rol, permisos, dominio from user join rol on user.rol=rol.rol where user=:user;";
        $args = array('user'=>$usuario);
        $resultado = $base->exec($cmds, $args);
        if ($base->count() == 0){
            $cmds = "select titulo, user.rol, permisos, dominio from user join rol on user.rol=rol.rol where user='usuario';";
            return $base->exec($cmds, $args);
        } 
//        $this->cifrarEnPrimerLogueo($resultado, $password, $usuario);
        return $resultado;
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
     * Si el usuario tiene más de 4 intentos intentos, lo redirige con mensaje "Su usuario esta bloqueado"
     * @param string $usuario
     */
    protected function comprobarBloqueo($usuario){
        $base = $this->conectarDB();
        // Operamos
        $cmds = "select count(user) as intentos from intentos where user=:user";
        // Funciona sin los dos puntos y con los dos puntos antes del índice
        $pams = array(':user' => $usuario);
        $resultado = $base->exec($cmds, $pams)[0]['intentos'];
        if ($resultado == 4 ){
            $this->index->reroute('@login_mensaje(@mensaje=Su usuario esta bloqueado)');
        }
    }
    
    /**
     * Si el valor ´dominio´ en la base de datos esta vacío, debe crear el dominio
     * a partir de los domain component en el DN del usuario
     * TODO: Si este fuera static o algo por el estilo, usuarioControl lo habrìa cambiado
     * @param string $dn
     * @param string $dominio
     * @return string
     */
    protected function dominioUser($dn, $dominio){
        $pattern = "(dc=(?P<componentes>[A-Za-z]+))";
        $matches = array();
        if (empty($dominio)) {
            preg_match_all($pattern, $dn, $matches );
            foreach ($matches['componentes'] as $componentes){
                    $dominio .= $componentes . ".";
            }
        }
        return rtrim($dominio, ".");
    }
          
    /**
     * Auxiliar de autenticar
     * Iniciamos la sesion con datos a guardar en la base de datos
     * @param \Clases\authentication $login
     */
    protected function sesionar($login){
        $db = $this->index->get('dbsession');
        // Señores, he acá donde se inicia la puta sesión
        $sesion = new \DB\SQL\Session($db);
        $this->index->set('SESSION.dn', $login->getDN());
        $this->index->set('SESSION.user', $this->usuario);
        $this->index->set('SESSION.pswd', $this->password);

        // Obtenemos los procedimientos de roles de la base de datos
        $roles = $this->obtenerBandera($this->usuario, $this->password);

        // Llenamos los siguiente datos en base a lo obtenido en roles
        $this->index->set('SESSION.rol', $roles[0]['rol']);
        $this->index->set('SESSION.titulo', $roles[0]['titulo']);
        $this->index->set('SESSION.dominio', $this->dominioUser($login->getDN(), $roles[0]['dominio']));
        $this->index->set('SESSION.permisos', unserialize($roles[0]['permisos']));
    }


    /**
     * Controlador para el proceso de autenticacion
     * TODO: Quizá puedas meter después los datos ('uid', 'gecos', 'mail', 'o','ou', 'title','gidnumber')
     * si bien nunca los usaste
     */
    public function autenticar(){
        $this->comprobarBloqueo($this->usuario);
        $login = new \Clases\authentication('ldap', array(
                'dc' => $this->server,
                'pw' => $this->index->get('passwdldap'),
                'rdn' => $this->index->get('lectorldap'),
                'base_dn'=> $this->index->get('sbase')
            )
        );
        if (@$login->login($this->usuario, $this->password)){
            $this->logueado($this->usuario);
            $this->sesionar($login);
            $this->index->reroute('@main');
        }else{
            $this->intentoUsuario($this->usuario);
            $this->index->reroute('@login_mensaje(@mensaje=Credenciales incorrectas)');
        }
    }
    
    // TODO: Si alguna vez cambiamos a Silex o cualquier otro framework con 
    // soporte de mensajes Flash, por favor, unifica estas vergonzosas funciones
    /**
     * Auxiliar para cerrar y cerrarMensaje
     * Acciones para el cierre de sesión
     */
    public function cerrarSesion(){
        $db = $this->index->get('dbsession');
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
    
    public function cerrarMensajeExterno($mensaje){
        $this->cerrarSesion();
        $this->index->reroute('@login_mensaje(@mensaje='. $mensaje .')');
    }
}
