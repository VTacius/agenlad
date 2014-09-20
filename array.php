<?php
$falso = array('uno', 'dos','tres');
$verdadero = array('uno');
foreach($falso as $values){
	if (in_array($values, $verdadero)){
		print $values;
	}else{
	}

}
