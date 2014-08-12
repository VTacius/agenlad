## Agenlap: Agenda telefónica en PHP para directorios LDAP

Este es un proyecto personal basado en un proyecto institucional. 
Supuse que a alguien podrá interesarle.
Latimos con el corazón de Fat Free Framework, e intentamos parecernos un poco a 

### Pasos previos
* La dependencia de terceros es manejada con composer
    $ composer update
* Aplique el pequeño parche para hacerlo funcionar
    $ patch < auth.php.patch
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