#!/bin/bash
usuarios="alortiz opineda"
for usuario in `cat usuarios.lst`; do 
    for ficherio in `find /var/log/samba -type f `; do
        apariciones=$(grep $usuario $ficherio | grep "authentication for user"  | grep "succeeded" -c)
        if [ $apariciones -gt 1 ]; then
            echo $usuario;
            break;
        fi
    done
done
