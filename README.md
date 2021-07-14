# Recc Server

Este es el repositorio de la API de Recc.

## Instalación

Clone primero el repositorio:

    git clone git@github.com:Moduleirors2-0/recc-server.git
    cd recc-server

o bien:

    https://github.com/Moduleirors2-0/recc-server.git
    cd recc-server

Instale las dependencias:

    composer install

Fije las variables de entorno, reemplace las variables correspondientes para su base de datos:

    cp .env.example .env
    php artisan key:generate
    cp .env .env.testing

### Extensiones recomendadas

Antes de realizar cambios al código de este repositorio, instale `editorconfig` o su equivalente para su editor para asegurar la uniformidad del formato.

## Correr el servidor

Para correr el servidor ejecute el siguiente comando una vez terminada la instalación:

    php artisan serve

## Correr pruebas

    php artisan test
