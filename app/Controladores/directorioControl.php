<?php
namespace Controladores;
/**
 * Controlador para directorio telefónico de la aplicacion
 *
 * @author alortiz
 */

class directorioControl extends \Clases\baseControlador {
    
    /**
     * TODO: Hay uno bastante parecido en usershowControl
     * @param array @filtro Use los siguiente valores: ('cn','title','o', 'ou','mail')
     * @return array
     */
    private function busquedaUsuarios($filter = array()) {
        $usuarios = new \Modelos\userPosix($this->dn, $this->pswd, 'central' );
        $atributos = array('uid','cn','title','o', 'ou','mail', 'telephoneNumber');
        $filtro = empty($filter) ? array("cn"=>"NOT (root OR nobody)") : array_merge($filter, array("cn"=>"NOT (root OR nobody)"));
        $datos = $usuarios->search($filtro, $atributos, "dc=sv");
        $this->parametros['errorLdap'] = $usuarios->getErrorLdap();
        return $datos;   
    }
    
    /**
     * TODO: Revisar con wireshark si en verdad el filtro creado corresponde con nuestras
     * necesidades, aunque todo parezca indicar que es así tal 
     */    
    public function mostrarUsuario(){
        $this->comprobar($this->pagina);
        // Usamos los valores enviados por POST para construir el filtro
        $parametros = $this->index->get('POST');
        $filtro = array();
	    if(isset($parametros['uid']) && $parametros['uid'] != "*" ){
	    	$filtro["personalizado"] = "(|(uid={$parametros['uid']})(cn={$parametros['uid']}))";
	    	unset($parametros['uid']);	
	    }elseif(strpos($parametros['uid'], " ")){
	    	$filtro['uid'] = $parametros['uid'];
	    	unset($parametros['uid']);	
	    }
        foreach ($parametros as $key => $value) {
            if (  !(empty($value) || $value == "*") ) {
                $filtro[$key] = $value;
            }
        }
        $datos['datos'] = $this->busquedaUsuarios($filtro);
        $resultado = array_merge($datos, array('errorLdap'=> $this->parametros['errorLdap']));
        print json_encode($resultado);
    }
    
}
