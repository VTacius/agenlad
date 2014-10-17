<?php
$search = array("uid"=> "NOT (root OR nobody)", "cn" => "NOT nobody");
$filtro = "(&(objectClass=shadowAccount)";
foreach ($search as $attr => $valor){
	if (preg_match_all("/(NOT|OR)\s{1,2}\(*(?<valores>[a-z]+)/", $valor, $matches)){
		$pre_attr = "(&";
		foreach($matches['valores'] as $value){
			$pre_attr .= "(!($attr=$value))";
		}
		$pre_attr .= ")";
		$filtro .= $pre_attr;
	}else{
		$filtro .= "($attr=$valor)\n";
	}
}
$filtro .= ")";
print $filtro . "\n";
