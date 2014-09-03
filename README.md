## Agenlap: Agenda telefónica en PHP para directorios LDAP

Este es un proyecto personal basado en un proyecto institucional. 
Supuse que a alguien podrá interesarle.
Latimos con el corazón de Fat Free Framework, y hacemos el intento por ser una aplicacion MVC

### Pasos previos
* La dependencia de terceros es manejada con [composer](https://gist.github.com/VTacius/4b9ed8b1deee1ecdfb04)

```shell 
composer update
```

* Cree un directorio temporal para que Twig cree las plantillas
```shell
mkdir -p tmp/cache
chown www-data:www-data -R tmp/
```

* Cree el fichero `parametros.ini` para configurar el servidor remoto. Su contenido va de la siguiente forma:
```ini
[globals]
DEBUG=3
UI=ui/
[conexion-samba]
sserver=directorio.dominio.com
spuerto=389
sbase="dc=dominio,dc=com"
[conexion-zimbra]
zserver=mail.dominio.com
zpuerto=389
zbase="dc=dominio,dc=com"
[credenciales-samba]
; No usar un usuario administrador
; No olvidar por ningún motivo las comillas
lectorldap="cn=lector,dc=dominio,dc=com"
passwdldap=
[credenciales-zimbra]
; No usar un usuario administrador, pero darle permisos de lectura a este usuario para los atributos zimbraAccountStatus y zimbraMailStatus
lectorzimbra="uid=zmlectura,cn=appaccts,cn=zimbra"
[database]
dbbase = directorio
dbserver = 
dbusuario = 
dbpassword = 
[atributos-posix]
maildomain = "dominio.com"
objectClass[0] = "top"
objectClass[1] = "person"
objectClass[2] = "organizationalPerson"
objectClass[3] = "inetOrgPerson"
objectClass[4] = "posixAccount"
objectClass[5] = "shadowAccount"
objectClass[6] = "sambaSamAccount"
shadowMin = "99999"
shadowMax = "99999"
[atributos-samba]
sambasid = "S-1-5-21-37xxxxxxx-14xxxxxx-23xxxxxxxx"
netbiosname = "NETBIOS"
```
 * Active el módulo Rewrite de Apache
```shell 
a2enmod rewrite
```

* Para no complicar las cosas, por ahora, copie los ficheros necesarios:
```shell 
cp vendor/components/jquery/ web/ui/js/vendor/
cp vendor/components/jquery/jquery.min.js web/ui/js/vendor/
cp vendor/components/bootstrap/js/bootstrap.min.js web/ui/js/vendor/
cp vendor/components/bootstrap/css/bootstrap.min.css web/ui/css/vendor/
cp vendor/components/bootstrap/css/bootstrap-theme.min.css web/ui/css/vendor/
```