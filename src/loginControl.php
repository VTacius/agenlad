<?php
ini_set('default_charset', 'utf-8');
/**
 * Controlador para el manejo de sesión
 *
 * @author alortiz
 */
class loginControl {
    private $index;
    private $server;
    private $puerto;
    private $usuario;
    private $password;
    
    /**
     * El constructo inicia las variables necesarias para conectarse al servidor LDAP
     * @param type $base
     */
    public function loginControl($base){
        $this->index = $base;
        // Conseguimos las variables hasta el servidor LDAP
        $this->usuario = $this->index->get('POST.user'); 
        $this->password = $this->index->get('POST.pswd');
        $this->server = "ldap://" . $this->index->get('server');
        $this->puerto = $this->index->get('puerto');
    }
    
    /**
     * muestra la página de inicio, incluyendo un mensaje opcional que espero mencionar en el futuro
     * @param type $base
     */
    public function display($base){
        $mensaje = $base->get('PARAMS.mensaje');
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
    
    /**
     * Obtenemos los permisos, firmas y firmaz del usuario
     * @param string $usuario
     * @return array(permisos, firmas, firmaz)
     */
    protected function roles($usuario){
        $base = $this->conectarDB();
        // Operamos
        $cmds = 'select permisos, firmas, firmaz from roles where user=:user';
        $args = array('user'=>$usuario);
        return $base->exec($cmds, $args);
    }
    
    /**
     * Inserta un intento de logueo una vez el usuario se ha equivocado
     * @param string $usuario
     */
    protected function intentoUsuario($usuario){
        $base = $this->conectarDB();
        // Operamos
        $cmds = 'insert into intentos(user,estampa) values(:user,:estampa)';
        $args = array('user'=>$usuario, 'estampa'=>date("Y-m-d H:i:s"));
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
     */
    public function autenticar(){      
        $this->comprobarBloqueo($this->usuario);
        $login = new \Auth('ldap', array(
            'dc' => $this->server,
            'rdn' => '',
            'base_dn'=> 'ou=Users,dc=salud,dc=gob,dc=sv',
            'pw' => '')
        );

        if (@$login->login($this->usuario, $this->password)){
            $this->logueado($this->usuario);
            // Iniciamos la sesion con datos a guardar en la base de datos
            $db = $this->index->get('dbconexion');
            $sesion = new \DB\SQL\Session($db);
            $this->index->set('SESSION.user', $this->usuario);
            $this->index->set('SESSION.pswd', $this->password);
            $roles = $this->roles($this->usuario);
            $this->index->set('SESSION.permisos', unserialize($roles[0]['permisos']));
            $this->index->reroute('@main');
        }else{
            $this->intentoUsuario($this->usuario);
            $this->index->reroute('@login_mensaje(@mensaje=Credenciales incorrectas)');
        }
    }
    
    /**
     * Controlador para cerrar la sesión de usuario
     */
    public function cerrar(){
        $db = $this->index->get('dbconexion');
        new \DB\SQL\Session($db);
        $this->index->clear('SESSION');
        $this->index->reroute('@login_mensaje(@mensaje=Ha cerrado la sesion)');
    }
}
