<?php
	//ini_set('display_errors', '1');
	//ini_set('error_reporting','-1');
	ini_set('default_charset', 'utf-8');

	//Veamos si esta variable global funciona en php
	//Recuerdo haber usado var alguna vez en php, pero creo que se referirá a las clases
	$errorLDAP='';
	//La siguiente variable limita los valores que se tomaran del árbol ldap, 
	//Por un lado en el fetch que se hara en listarLDAP, y en la tabulacion de datos en tabDatosLDAP();
	//Un mal momento me hizo saber que se deben escribir en minúscula
	$valores = array ('cn','loginshell','sn','mail');
	function conecLDAP($lhost,$lport){
		//Cuando se usa openLDAP, siempre se regresa un recurso "válido", por lo que en nuestro particular caso,
		//así que validar no servirá de nada
		$lcon = ldap_connect($lhost,$lport);
		//print_r($lcon);
		ldap_set_option($lcon,LDAP_OPT_PROTOCOL_VERSION,3);
		ldap_set_option($lcon,LDAP_OPT_NETWORK_TIMEOUT,1);
		return $lcon;
	}
	
	function enlaceLDAP($lcon,$luser,$lpwd){
		try{
			$lenl = ldap_bind($lcon,$luser,$lpwd);
			if($lenl){
				return $lenl;
			}else{
				throw new Exception("<br>enlaceLDAP: Ocurrió el siguiente error a la hora de conectarse: <br><center> ".ldap_error($lcon)."</center></br>");
			}
		}catch(Exception $e){
			 $GLOBALS['errorLDAP'] = $e->getMessage();
		}
	}	

	function listarLDAP($lcon,$lbase,$lfiltro){
		try{
			//Le asignaremos un filtro por defecto: El objectClass de más alta jerarquía, por fuerza debe poseerlo cualquier objeto en el arbol LDAP
			$filtro = (isset($lfiltro))?$lfiltro:'objectClass=top';
			//print_r($filtro);
			$llist = ldap_list($lcon,$lbase,$filtro,$GLOBALS['valores']);
			//print_r($llist);
			if($llist){
				return $llist;
			}else{
				throw new Exception("<br>listarLDAP: ocurrió el siguiente error a la hora de listar: <br><center>".ldap_error($lcon)."</center><br>");
			}
		}catch(Exception $e){
			$GLOBALS['errorLDAP'] = $e->getMessage();
		}
	}
	
	function getLDAP($lcon,$llist){
		//Algo me dice que en el actual esquema, no hay mayor error que pueda producirse a estas alturas, pero veremos que onda
		try{
			$lcont = ldap_get_entries($lcon,$llist);
			if ($lcont) {
				//print_r($lcont);
				return $lcont;
			}else{
				throw new Exception ("<br>listarLDAP: ocurrió el siguiente error a la hora del fetch: <br><center>".ldap_error($lcon)."</center><br>");
			}
		} catch (Exception $e){
			$GLOBALS['errorLDAP'] = $e->getMessage();
		}	
	}
	
	function tabDatosLDAP($lcontenido){
		//print_r($lcontenido);
		$entradas = $lcontenido['count'];
		$tabla = "";
		//Hay un array por cada entrada que haya encontrado en el LDAP
		for ( $i = 0; $i < $entradas; $i++ ) {
			$tabla .= "\n\t<tr>";
			//Se recorre el array con los valores que se necesitan, se toman como índice para el array de cada entrada que exista en el árbol LDAP 
			foreach ($GLOBALS['valores'] as $j){
				//De esta forma enunciado solo para tener un orden visual, pero bien podrìan ir las tres lineas en una sola
				$tabla .= "\n\t\t<td>";
				$tabla .= $lcontenido[$i][$j][0]."";
				$tabla .= "</td>\n";
			}
			$tabla .= "\n\t</tr>";
		}
		return $tabla;
	}	
?>
