## Agenlap: Agenda telefónica en PHP para directorios LDAP

Este es un proyecto personal basado en un proyecto institucional. 
Supuse que a alguien podrá interesarle.
Latimos con el corazón de Fat Free Framework, y hacemos el intento por ser una aplicacion MVC

### Pasos previos
* La dependencia de terceros es manejada con [composer](https://gist.github.com/VTacius/4b9ed8b1deee1ecdfb04)
* Los módulos para php pueden ser instalados en Debian con 

```shell 
composer update
```

```shell 
aptitude install php5-{mcrypt,mysql,ldap}
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
; No usar un usuario administrador
; Pero sus limites, tal como el administrador, debe ser infinito
; No olvidar por ningún motivo las comillas
lectorldap="cn=admin,dc=dominio,dc=com"
passwdldap="lector_ldap_hacienda"
; Este si debe ser un usuario administrador
; No olvidar por ningún motivo las comillas
; La contraseña esta en la base de datos, cifrada, para cada usuario que tenga rol admon
adminldap="cn=admin,dc=dominio,dc=com"
[credenciales-zimbra]
; No usar un usuario administrador, pero darle permisos de lectura a este usuario para los atributos zimbraAccountStatus y zimbraMailStatus
lectorzimbra="uid=zmlectura,cn=appaccts,cn=zimbra"
[database]
dbbase = directorio
dbserver = 
dbusuario = 
dbpassword = 

 * La mínima configuración requerida para que funcione en Apache es la siguiente

```apacheconf
<VirtualHost *:80>
        ServerAdmin alortiz@salud.gob.sv

        DocumentRoot /var/www/web
        <Directory /var/www/web>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

```
 * Active el módulo Rewrite de Apache, luego de lo cual habrá que reiniciar el servidor Apache
```shell 
a2enmod rewrite
```

* Para no complicar las cosas, por ahora, copie los ficheros necesarios:
```shell
mkdir web/ui/js/vendor
cp vendor/components/jquery/jquery.min.js web/ui/js/vendor/
cp vendor/components/jqueryui/jquery-ui.min.js web/ui/js/vendor/
cp vendor/components/bootstrap/js/bootstrap.min.js web/ui/js/vendor/
cp vendor/components/jqueryui/themes/smoothness/*css web/ui/css/vendor/
cp vendor/components/bootstrap/css/bootstrap.min.css web/ui/css/vendor/
cp vendor/components/bootstrap/css/bootstrap-theme.min.css web/ui/css/vendor/
cp -r vendor/components/jqueryui/themes/smoothness/images/ web/ui/css/vendor/
```