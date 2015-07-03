<?php
$permisos = 'a:4:{s:10:"directorio";s:22:"Directorio Telefónico";s:4:"main";s:21:"Cambio de Contraseña";s:7:"usuario";a:3:{s:8:"usershow";s:11:"Ver Usuario";s:7:"usermod";s:17:"Modificar Usuario";s:7:"useradd";s:15:"Agregar Usuario";}s:13:"configuracion";a:2:{s:12:"confdominios";s:8:"Dominios";s:12:"confpermisos";s:8:"Permisos";}}';
$nuevos_permisos = unserialize($permisos);
print_r($nuevos_permisos);
$nuevos_permisos['actualizacion'] = "Actualización de Datos";
print_r($nuevos_permisos);
print(serialize($nuevos_permisos));
print "\n";
