<?php
	//Creo que estas cosas te podrán ser util para mañan
	//Creo que estas cosas te podrán ser util para mañanaa
	$mensaje = (array_key_exists('2',$grupo))?"Verdadero":"Falso";
	echo $mensaje;
	ldap_start_tls($conexion);
	function	cambiarAtributo($lcon,$atributo,$valor,$dn){
		$datos = array($atributo => array(0 => $valor));
		ldap_modify($con,$dn,$datos) or die (ldap_errno($con));
	}
?>
