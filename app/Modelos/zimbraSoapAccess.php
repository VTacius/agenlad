<?php
//namespace Modelos;

use SoapClient;
use SoapHeader;
use SoapParam;
use SoapVar;
use SoapFault;

require_once '../clases/xmlToArray.php';

class zimbraSoapAccess {
    /** @var array Almacena mensajes de error o la última respuesta a nuestra peticion */
    private $ultimoResultado = array();
   
    /** @var string full | admin */
    private $verbosidad = "admin";
    
    /** @var array $mapa['atributo_samba'] = 'atributo_zimbra' */
    private $mapa = array (
        'o' => 'company',
        'ou' => 'ou' ,
        'sn' => 'sn',
        'title' => 'title',
        'givenName' => 'givenName',
        'displayName' => 'displayName',
        'telephoneNumber' => 'telephoneNumber'
    );

    public function __construct($servidor){
        $conexion = array(
            'location' => "https://$servidor:7071/service/admin/soap/",
            'uri'          => 'urn:zimbraAdmin',
            'trace'        => 1,
            'exception'    => 1,
            'soap_version' => 'SOAP_1_1',
            'style'        => 'SOAP_RPC',
            'use'          => 'SOAP_LITERAL'
            );
        $this->soapClient = new SoapClient(null, $conexion);
    }

    /**
     * Ocultamos un poco la clase que estamos usando para esto, no vaya a ser 
     * tengamos problemas con ella en el futuro
     * @param string $contenido La cadena debe tener vocación de ser XML
     * @return array
     */
    private function parsearRespuesta($contenido){
        $xml = new xmlToArray();
        $result = $xml->parse($contenido);
        return $result['SOAP:ENVELOPE']['SOAP:BODY'];
    }
    
    public function getMensaje(){
        print_r($this->ultimoResultado) ;
    }


    protected function llamada ($peticion, $parametros, $uri){
        // Intentamos
        try {
            $this->soapClient->__soapCall($peticion, $parametros, $uri);
            // La última llamada tiro error. No llenemos ya esto, basta con el primer error
            if (!isset($this->ultimoResultado['codigo'])){
                $this->ultimoResultado = array(
                    "mensaje" => $this->parsearRespuesta($this->soapClient->__getLastResponse())
                );
            }
        } catch (SoapFault $error) {
            if (!isset($this->ultimoResultado['codigo'])){
                $this->ultimoResultado = array("codigo"=> $error->faultcode, "mensaje"=> $error->faultstring);
            }
        }

    }

    /**
     * Devuelve el atributo Id de un 
     * @param array $objeto
     * @param string $atributo
     * @return string
     */
    protected function getAttributeAccountInfo($objeto, $atributo){
        foreach ($objeto['GETACCOUNTINFORESPONSE']['A'] as $value) {
            if ($value['N'] === $atributo) {
                return $value['DATA'];
            }
        }
        return "El atributo no existe o no esta configurado";
    }
    
    protected function getAttributeAccountShow($objeto, $atributo){
        foreach ($objeto['GETACCOUNTRESPONSE']['ACCOUNT']['A'] as $value) {
            if ($value['N'] === $atributo) {
                return $value['DATA'];
            }
        }
        return ;
    }

    public function getAttributeAccount($objeto, $atributo){
        if ($this->verbosidad == "admin") {
            return $this->getAttributeAccountInfo($objeto, $atributo);
        }else{
            return $this->getAttributeAccountShow($objeto, $atributo);
        }
    }


    /**
     * 
     * @param string $name
     * @param string $verbosidad full | admin
     * @return type
     */
    function getAccount($name, $verbosidad = "admin"){
        $this->verbosidad = $verbosidad;
        $request = array("admin"=>"GetAccountInfoRequest", "full"=>"GetAccountRequest");
        $parametrosGetter = array(
            new SoapVar('<account by="name">' . $name . '</account>', XSD_ANYXML),
        );
        $this->llamada($request[$verbosidad], $parametrosGetter, array ( 'uri' => 'urn:zimbraAdmin'));
        return $this->ultimoResultado['mensaje'];
    }
    
    public function login($administrador, $password){
        // Preparemos el mensaje SOAP
        $soapHeader = new SoapHeader('urn:zimbra', 'context');
        $this->soapClient->__setSoapHeaders($soapHeader);

        // Primer mensaje: Nos autenticamos
        $parametrosAuth = array (
            new SoapParam($administrador, 'name'),
            new SoapParam($password, 'password')
        );

        $this->llamada("AuthRequest", $parametrosAuth, array('uri' => 'urn:zimbraAdmin'));
       
    }

    public function crearMailbox($usuario){
        $parametrosCreacionUsuario = array (
            new SoapParam($usuario['mail'], 'name'),
            new SoapParam($usuario['password'], 'password'),
        );
   
        // El atributo necesario para que se autentique
        $parametrosCreacionUsuario[] = new SoapVar("<a n='zimbraAuthLdapExternalDn'>{$usuario["dn"]}</a>", XSD_ANYXML);

        // Agregamos los datos de $usuario
        foreach ($this->mapa as $attrSamba => $attrZimbra) {
            if (array_key_exists($attrSamba, $usuario)){
                $parametrosCreacionUsuario[] = new SoapVar("<a n='$attrZimbra'>{$usuario[$attrSamba]}</a>", XSD_ANYXML);
            }
        }
        $this->llamada("CreateAccountRequest" , $parametrosCreacionUsuario, array ( 'uri' => 'urn:zimbraAdmin'));

    }
    
    public function modificarMailbox($usuario, $cambios){
        $cuenta = $this->getAccount($usuario);
        $zimbraId = $this->getAttributeAccount($cuenta, "zimbraId");
        $parametrosModificacionUsuario = array(
            new SoapParam($zimbraId, 'id')
        );
        foreach ($this->mapa as $attrSamba => $attrZimbra) {
            if (array_key_exists($attrSamba, $cambios)){
                $parametrosModificacionUsuario[] = new SoapVar("<a n='$attrZimbra'>{$cambios[$attrSamba]}</a>", XSD_ANYXML);
            }
        }
        $this->llamada("ModifyAccountRequest" , $parametrosModificacionUsuario, array ( 'uri' => 'urn:zimbraAdmin'));
    }

}

$administrador = "admin@salud.gob.sv";
$contrasenia = "srv2025";

$usuario = array(
    'sn' => "Guevara",
    'dn' => "uid=alortiz,ou=Users,dc=salud,dc=gob,dc=sv",
    'mail' => "virgini137@salud.gob.sv",
    'password' => 'virginia134',
    'givenName' => "Virginia"
);

$modificacionUsuario = array(
    'givenName' => "Virginia Esmeralda",
    'sn' => "Guevara Ochoa",
);

$login = new zimbraSoapAccess("10.10.20.102");
$login->login("admin@salud.gob.sv", "srv2025");
$cuenta = $login->getAccount("virgini137", "full");
print_r($login->getAttributeAccount($cuenta, "givenName"));
print "\n\n";
$login->modificarMailbox("virgini137", $modificacionUsuario);
$cuentaMod = $login->getAccount("virgini137", "full");
print_r($login->getAttributeAccount($cuentaMod, "givenName"));
print "\n\n";
//$login->modificarMailbox($usuario, $cambios);
//$login->getMensaje();
//$login->crearMailbox($usuario);
//$login->getMensaje();
