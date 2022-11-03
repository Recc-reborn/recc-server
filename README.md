# ReccRecc
El motor de recomendaciones de Recc que habla con recc-server por medio de HTTP.

## Instalacion
1. Asegurate de tener python 3 instalado
2. Instala `pipenv`
    ```
    pip install --user pipenv
    ```
3. Instala las dependencias
    ```
    pipenv install
    ```
4. Copia las variables de entorno
    ```
    cp .env.example .env
    ```
4. Actualiza las variables de entorno

## Correr el servidor
1. Para iniciar el servidor, corre
    ```
    make start
    ```
2. Si todo sale bien, cuando visitas `127.0.0.1:5000`, debes de ver
    ```
    Hello :)
    ```
