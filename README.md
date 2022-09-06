# Recc Server

Este es el repositorio de la API de Recc.

## Especificación de la API
https://recc-moduleirors.postman.co/workspace/e51eb72d-9f8a-4cb2-99d2-f4fde72cd292

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

**Opcional, recomendado en entorno de desarrollo**
Corra los seeders para llenar la base de datos con informacion de prueba:

    php artisan db:migrate

**Opcional**
Copie los artistas de la API de Last.fm usando llamadas consecutivas a su API.

    php artisan lfm:clone-artists

### Extensiones recomendadas

Antes de realizar cambios al código de este repositorio, instale `editorconfig` o su equivalente para su editor para asegurar la uniformidad del formato.

## Correr el servidor

Para correr el servidor ejecute el siguiente comando una vez terminada la instalación:

    php artisan serve

## Correr pruebas

    php artisan test

