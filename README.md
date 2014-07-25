agenlad
=======

Agenda en php con conexión a un directorio LDAP


## Agenlap: Agenda telefónica en PHP para directorios LDAP

Este es un proyecto personal basado en un proyecto institucional. 
Suponemos que esta es la parte que pueda interesarle a otros

### Dependencias: 
#### Sistema:
    $ aptitude install php5-ldap`
#### Librerías de terceros:
    $ git clone git@github.com:fabpot/Twig.git
    $ git clone https://github.com/twbs/bootstrap.git
### Antes de su primer uso:
* Cree el fichero `parametros.ini` para configurar el servidor remoto. Su contenido va de la siguiente forma:
```ini
[conexion]
server = ldap.dominio.com
puerto = 389
```

