## Agenlap: Agenda telefónica en PHP para directorios LDAP

Este es un proyecto personal basado en un proyecto institucional. 
Supuse que a alguien podrá interesarle.
Latimos con el corazón de Fat Free Framework, y hacemos el intento por ser una aplicacion MVC

### Pasos previos
* La dependencia de terceros es manejada con [composer](https://gist.github.com/VTacius/4b9ed8b1deee1ecdfb04) y bower
* Los módulos para php pueden ser instalados en Debian con 

```shell 
aptitude install php5-{mcrypt,mysql,ldap}
```

```shell 
composer update
bower update
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
[credenciales-samba]
; No usar un usuario administrador
; Este usuario no se usa para algo más que el simple logueo.
lectorldap="cn=lector,dc=dominio,dc=com"
passwdldap="lector_ldap_dominio"
[seguridad]
; Esta es parte de la clave a usar para cifrar las contraseñas. 
; No la cambie nunca una vez configurada
semilla="B4c10"
[database]
dbbase=directorio
dbserver=mysql.dominio.com
dbusuario=agenlad
dbpassword=agenlad 
```

 * Use el script proveído para iniciar la estructura en la base de datos

```shell
mysql -h 10.10.20.56 -u user_database -p database_name < db.sql

```

 * La mínima configuración requerida para que funcione en Apache es la siguiente

```apacheconf
<VirtualHost *:80>
        ServerAdmin alortiz@salud.gob.sv

        DocumentRoot /var/www/web
        <Directory /var/www/web>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride FileInfo
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

 * Active el módulo Rewrite de Apache, luego de lo cual habrá que reiniciar el servidor Apache
```shell 
a2enmod rewrite
service apache2 restart
```

* Copie los archivos CSS y Javascript de terceros a la ubicación de la aplicación:
```shell
cp bower_components/jquery/dist/jquery.min.js web/js/vendor/
cp bower_components/jquery-ui/jquery-ui.min.js web/js/vendor/
cp bower_components/bootstrap/dist/js/bootstrap.min.js web/js/vendor/
cp bower_components/jQuery-Mask-Plugin/dist/jquery.mask.min.js web/js/vendor/
cp bower_components/jquery.validate/dist/jquery.validate.min.js web/js/vendor/
cp bower_components/jquery.validate/src/localization/messages_es.js web/js/vendor/
cp bower_components/select2/dist/js/select2.min.js web/js/vendor/
cp bower_components/handlebars/handlebars.min.js web/js/vendor/

cp bower_components/bootstrap/dist/css/bootstrap{,-theme}.min.css web/css/vendor/
cp bower_components/jquery-ui/themes/smoothness/*css web/css/vendor/
cp -r bower_components/jquery-ui/themes/smoothness/images/ web/css/vendor/
cp bower_components/bootstrap/dist/fonts/* web/css/fonts/
cp bower_components/select2/dist/css/select2.min.css web/css/vendor/
cp bower_components/select2/dist/js/i18n/es.js web/js/vendor/
cp bower_components/select2-bootstrap3-css/select2-bootstrap.min.css web/css/vendor/
```

### Configurando la aplicación por primera vez
No es un asistente al uso, pero facilitará mucho la configuración inicial:
* Dado agenlad.dominio.com como la URL de la aplicación, acceda a agenlad.dominio.com/inicializacion/ para empezar con la configuración. 
* Continúe en agenlad.dominio.com/inicializacion/usuario

