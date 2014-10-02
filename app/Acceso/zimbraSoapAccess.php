<?php
namespace Acceso;

use SoapClient;
use SoapHeader;
use SoapParam;
use SoapVar;
use SoapFault;
use Exception;
//require_once '../clases/\clasesxmlToArray.php';

class zimbraSoapAccess {
        /** @var \Base */
    protected $index;

    /** @var array */
    private $errorSoap = array();
    
        
    /** @var string full | admin */
    private $verbosidad = "admin";
    
    /** @var array */
    protected $lastResponse = array();
    
    /** @var array $mapa['atributo_samba'] = 'atributo_zimbra' */
    protected $mapa = array (
        'o' => 'company',
        'ou' => 'ou' ,
        'sn' => 'sn',
        'title' => 'title',
        'givenName' => 'givenName',
        'displayName' => 'displayName',
        'telephoneNumber' => 'telephoneNumber'
    );

    public function __construct( $administrador, $password){
        $this->index = \Base::instance();
        $servidor = $this->index->get('zserver');
        $conexion = array(
            'location' => "https://$servidor:7071/service/admin/soap/",
            'uri'          => 'urn:zimbraAdmin',
            'trace'        => 1,
            'exception'    => 1,
            'soap_version' => 'SOAP_1_1',
            'style'        => 'SOAP_RPC',
            'use'          => 'SOAP_LITERAL',
            'connection_timeout' => '2'
            );
        
        try { 
            $this->soapClient = @new SoapClient(null, $conexion);
        } catch (SoapFault $error) {
            $this->setErrorSoap($error->faultcode, $error->faultstring);
        }
        // Preparemos el mensaje SOAP para autenticarnos
        $soapHeader = new SoapHeader('urn:zimbra', 'context');
        $this->soapClient->__setSoapHeaders($soapHeader);

        // Primer mensaje: Nos autenticamos
        $parametrosAuth = array (
            new SoapParam($administrador, 'name'),
            new SoapParam($password, 'password')
        );

        $this->llamada("AuthRequest", $parametrosAuth, array('uri' => 'urn:zimbraAdmin'));

    }
    
    public function setErrorSoap($titulo, $mensaje){
        $this->errorSoap[] = array( 'titulo' => $titulo, 'mensaje' => $mensaje);
    }
    
    public function getErrorSoap(){
        if (sizeof($this->errorSoap)>0) {
            return $this->errorSoap;
        }
    }
    
    public function setLastResponse($titulo, $mensaje){
        $this->lastResponse[$titulo] =  $mensaje;
    }
    
    public function getLastResponse(){
        if (sizeof($this->lastResponse)>0) {
            return $this->lastResponse;
        }
    }
    /**
     * Ocultamos un poco la clase que estamos usando para esto, no vaya a ser 
     * tengamos problemas con ella en el futuro
     * @param string $contenido La cadena debe tener vocación de ser XML
     * @return array
     */
    private function parsearRespuesta($contenido){
        $xml = new \clases\xmlToArray();
        $result = $xml->parse($contenido);
        return $result['SOAP:ENVELOPE']['SOAP:BODY'];
    }
    
    protected function llamada ($peticion, $parametros, $uri){
        // Intentamos
        try {
            @$this->soapClient->__soapCall($peticion, $parametros, $uri);
            // La última llamada tiro error. No llenemos ya esto, basta con el primer error
            $mensaje = $this->parsearRespuesta($this->soapClient->__getLastResponse());
            // Me preocupa un poco que sea más bien indiferente con el hecho de tener una respuesta vacía
            $index = array_keys($mensaje)[0];
	    // Esto parece ser el límte de lo que podemos limpiar
	    unset($mensaje[$index]['XMLNS']);
            $this->setLastResponse($index, $mensaje[$index]);
        } catch (SoapFault $error) {
                $this->setErrorSoap($error->faultcode, $error->faultstring);
        }

    }

    /**
     * Devuelve el atributo Id de un 
     * @param array $objeto
     * @param string $atributo
     * @return string
     */
    protected function getAttributeAccountInfo($objeto, $atributo){
        if (!array_key_exists('GETACCOUNTINFORESPONSE', $objeto)) {
            throw new Exception("No se han devuelto datos");
        }
        foreach ($objeto['GETACCOUNTINFORESPONSE']['A'] as $value) {
            if ($value['N'] === $atributo) {
                return $value['DATA'];
            }
        }
        return "El atributo no existe o no esta configurado";
    }
    
    protected function getAttributeAccountShow($objeto, $atributo){
        if (!array_key_exists('GETACCOUNTRESPONSE', $objeto)) {
            throw new Exception("No se han devuelto datos");
        }
        foreach ($objeto['GETACCOUNTRESPONSE']['ACCOUNT']['A'] as $value) {
            if ($value['N'] === $atributo) {
                return $value['DATA'];
            }
        }
        return ;
    }

    public function getAttributeAccount($objeto, $atributo){
        try {
            if ($this->verbosidad == "admin") {
                return $this->getAttributeAccountInfo($objeto, $atributo);
            }else{
                return $this->getAttributeAccountShow($objeto, $atributo);
            }
        } catch (Exception $e) {
            $this->setErrorSoap("datos", $e->getMessage());
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
        return $this->lastResponse;
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
    
    public function modificarCuenta($usuario, $cambios){
        $cuenta = $this->getAccount($usuario);
        $zimbraId = $this->getAttributeAccount($cuenta, "zimbraId");
        $parametrosModificacionUsuario = array(
            new SoapParam($zimbraId, 'id')
        );
        foreach ($cambios as $attrZimbra => $attrValue) {
            $parametrosModificacionUsuario[] = new SoapVar("<a n='$attrZimbra'>$attrValue</a>", XSD_ANYXML);
        }
        $this->llamada("ModifyAccountRequest" , $parametrosModificacionUsuario, array ( 'uri' => 'urn:zimbraAdmin'));
    }

}


