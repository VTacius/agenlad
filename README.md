## Agenlap: Agenda telefónica en PHP para directorios LDAP

Este es un proyecto personal basado en un proyecto institucional. 
Suponemos que esta es la parte que pueda interesarle a otros

### Dependencias: 
#### Sistema:
    $ aptitude install php5-ldap`
#### Librerías de terceros:
    $ wget http://code.jquery.com/jquery-2.1.1.js -O  bootstrap/dist/js/jquery-2.1.1.js
    $ git clone git@github.com:fabpot/Twig.git
    $ git clone https://github.com/twbs/bootstrap.git
### Antes de su primer uso:
* Cree el fichero `parametros.ini` para configurar el servidor remoto. Su contenido va de la siguiente forma:
```ini
[conexion]
server = ldap.dominio.com
puerto = 389
[database]
dbbase = directoriolU
dbserver = 10.10.20.56
dbusuario = directorio
dbpassword = directorio 
```

