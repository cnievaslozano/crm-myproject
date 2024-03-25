# my-proyect

Es un proyecto de gestión de clientes, permite administrar briefings, incidencias y contenido compartido por los clientes. Con myproject, las empresas pueden mejorar la comunicación y la eficiencia en la gestión de sus proyectos con granota.


## Requisitos

- PHP >= 7.2.5
- Composer
- MySql
## Instalación

1. Clona el repositorio desde GitHub:

    ```bash
    git clone https://github.com/cristianbaixcamp/my-proyect.git
    ```

2. Accede al directorio del proyecto:

    ```bash
    cd my-proyect
    ```

3. Instala las dependencias del proyecto usando Composer:

    ```bash
    composer install
    npm install
    ```

4. Configura las variables de entorno necesarias según las instrucciones en `.env`. En este caso solo hace falta configurar la conexión con la BD.
    
    ```env
    DATABASE_URL="mysql://(usuario):(contraseña)@(ip):(puerto)/(nombreBD)?serverVersion=8&charset=utf8mb4"
    ```
5. Crea la base de datos y ejecuta las migraciones:

    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```
    De esta manera ya tenemos la estructura de la base de datos


## Uso

1. Inicia el servidor de desarrollo local:

    ```bash
    symfony server:start
    ```

2. Abre tu navegador web y accede a la URL proporcionada por Symfony Server (generalmente `http://localhost:8000`).

