<?php
	ini_set('default_charset', 'utf-8');

	$errorLDAP='';
	$valores = array ('cn','loginshell','sn','mail');
class phpLDAP{
	var $errorLDAP;
	function conecLDAP($lhost,$lport){
		$lcon = ldap_connect($lhost,$lport);
		ldap_set_option($lcon,LDAP_OPT_PROTOCOL_VERSION,3);
		ldap_set_option($lcon,LDAP_OPT_NETWORK_TIMEOUT,1);
		return $lcon;
	}
	
	function enlaceLDAP($lcon,$luser,$lpwd){
		try{	
			$dn = "uid=".$luser.",ou=people,dc=xibalba,dc=com";
			if($lenl = ldap_bind($lcon,$dn,$lpwd)){
				return $lenl;
			}else{
				throw new Exception("<br>enlaceLDAP: Ocurrió el siguiente error a la hora de conectarse: <br><center> ".ldap_error($lcon)."</center></br>");
			}
		}catch(Exception $e){
			 $this->errorLDAP = $e->getMessage();
		}
	}	

	function listarLDAP($lcon,$lbase,$lfiltro){
		try{
			$filtro = (isset($lfiltro))?$lfiltro:'objectClass=top';
			$llist = ldap_list($lcon,$lbase,$filtro,$GLOBALS['valores']);
			if($llist){
				return $llist;
			}else{
				throw new Exception("<br>listarLDAP: ocurrió el siguiente error a la hora de listar: <br><center>".ldap_error($lcon)."</center><br>");
			}
		}catch(Exception $e){
			$this->errorLDAP = $e->getMessage();
		}
	}
	
	function getLDAP($lcon,$llist){
		try{
			$lcont = ldap_get_entries($lcon,$llist);
			if ($lcont) {
				return $lcont;
			}else{
				throw new Exception ("<br>listarLDAP: ocurrió el siguiente error a la hora del fetch: <br><center>".ldap_error($lcon)."</center><br>");
			}
		} catch (Exception $e){
			$GLOBALS['errorLDAP'] = $e->getMessage();
		}	
	}

	function arrayDatosLDAP($lcontenido){
		$objecto = array();
			foreach ($GLOBALS['valores'] as $j){
				if(array_key_exists($j,$lcontenido[0])){
					array_push($objecto,$lcontenido[0][$j][0]) ;
				}else{
					array_push($objecto,"-");
				}
			}
		return $objecto;
	}
	
	function tabDatosLDAP($lcontenido){
		$entradas = $lcontenido['count'];
		$tabla = "";
		for ( $i = 0; $i < $entradas; $i++ ) {
			$tabla .= "\n\t<tr>";
			foreach ($GLOBALS['valores'] as $j){
				$tabla .= "\n\t\t<td>";
				if(array_key_exists($j,$lcontenido[$i])){
					$tabla .= $lcontenido[$i][$j][0]."" ;
				}else{
					$tabla .= "-";
				}
				$tabla .= "</td>\n";
			}
			$tabla .= "\n\t</tr>";
		}
		return $tabla;
	}	
}
?>
