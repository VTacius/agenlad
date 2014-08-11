#!/bin/bash
echo "Empieza el script-post instalación"
vendor="vendor"
bootstrap="twitter/bootstrap"
jquery="components/jquery"
vendor/components/jquery/jquery.min.js
ficheros=($bootstrap/dist/css $bootstrap/dist/fonts $bootstrap/dist/js $jquery/jquery.min.js)
for item in ${ficheros[*]}
	do
	echo $vendor/$item
	if [ -d $vendor/$item ] || [ -f $vendor/$item ]
		then 
			echo "Instalado $vendor/$item en ui/$item"
			cp -r $vendor/$item ui/
	else
		echo "Parece que el directorio $item no existe en $vendor"
	fi
done
