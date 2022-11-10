```
@@@@@@@   @@@@@@@@   @@@@@@@   @@@@@@@
@@@@@@@@  @@@@@@@@  @@@@@@@@  @@@@@@@@
@@!  @@@  @@!       !@@       !@@
!@!  @!@  !@!       !@!       !@!
@!@!!@!   @!!!:!    !@!       !@!
!!@!@!    !!!!!:    !!!       !!!
!!: :!!   !!:       :!!       :!!
:!:  !:!  :!:       :!:       :!:
::   :::   :: ::::   ::: :::   ::: :::
 :   : :  : :: ::    :: :: :   :: :: :
```

# Recc Server

Este es el repositorio de la API de Recc.

## Especificación de la API
https://recc-moduleirors.postman.co/workspace/e51eb72d-9f8a-4cb2-99d2-f4fde72cd292

## Instalación

### Prerequisitos
- Docker
- Git
- PHP
- Composer

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

> Importante: No olvide fijar las variables de entorno a los valores deseados.

## Inicializar

El siguiente comando inicializa la imagen de Docker, la base de datos y los artistas de Last.FM:

    make init

## Comandos comunes

### Correr el servidor

Para correr el servidor ejecute el siguiente comando una vez terminada la instalación:

    make start

El comando inicializara una imagen de Docker del servidor. Que puede ser accedida en `http://localhost:8000`
Si desea detener el servidor, simplemente ejecute
    
    make stop

Si desea eliminar la imagen del servidor y todos sus contenidos, ejecute
    
    make wipe


### Correr pruebas

    make test


### Copiar artistas desde Last.FM

Copie los artistas de la API de Last.fm usando llamadas consecutivas a su API.

    make lfm-artists


## Extensiones recomendadas

Antes de realizar cambios al código de este repositorio, instale `editorconfig` o su equivalente para su editor para asegurar la uniformidad del formato.

## Troubleshooting

### `The stream or file "/var/www/html/storage/logs/laravel.log" could not be opened`

[Lo mas probable es que sea un problema de permisos en la maquina anfitrion](https://stackoverflow.com/questions/50552970/laravel-docker-the-stream-or-file-var-www-html-storage-logs-laravel-log-co)

