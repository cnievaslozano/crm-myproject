# my-project

CRM a medida para GRANOTA.NET, permite administrar briefings, incidencias y contenido compartido por los clientes. Con myproject, se mejora la comunicación y la eficiencia en la gestión de los proyectos. 


## Requisitos

- PHP >= 7.2.5

## Instalación

1. Clona el repositorio desde GitHub:

    ```bash
    git clone https://github.com/cristianbaixcamp/crm-myproject.git
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

4. Configura las variables de entorno necesarias según las instrucciones en `.env`. En este caso solo hace falta configurar la conexión con la BD y mail
    
    ```env
    DATABASE_URL="mysql://(usuario):(contraseña)@(ip):(puerto)/(nombreBD)?serverVersion=8&charset=utf8mb4"

    MAILER_DSN=gmail+smtp://USERNAME:APP-PASSWORD@default
    
    ```
5. Crea la base de datos y ejecuta las migraciones:

    ```bash
    php bin/console doctrine:database:create
    php bin/console make:migration
    php bin/console doctrine:migrations:migrate
    ```
    De esta manera ya tenemos la estructura de la base de datos


## Uso

1. Inicia el servidor de desarrollo local:

    ```bash
    symfony server:start
    ```

2. Abre tu navegador web y accede a la URL proporcionada por Symfony Server (generalmente `http://localhost:8000`).

3. Usuario administrador:
   -  Para crear un usuario con ROL_ADMIN hay un seeder en src\DataFixtures\AppFixtures.php que crea un usuario admin, desde ahí puedes cambiar la contraseña y el nombre. También crear más usuarios…etc. Para ejecutar esto:

   ```bash
    php bin/console doctrine:fixtures:load
    ```

    Si no directamente en la base de datos podrías crear usuarios, con el rol que quieras.

Luego de forma web, cuando se crea una empresa, en el área de la empresa puedes crear un usuario normal, que pertenece a esa empresa.
