<div id="top-header" style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>

# WORKTIME CONTROLLER - SYMFONY 7

- [/README.md](../README.md)
- [Instalación de las Plataformas](#platforms)
- [Instalación de la REST API](#apirest)

# Instalación del Proyecto en Entorno Local

## <a id="platforms"></a>Instalación de las Plataformas

Clonar el repositorio [https://github.com/pabloripoll/worktc-platform-php-postgre](https://github.com/pabloripoll/worktc-platform-php-postgre)
```bash
$ git@github.com:pabloripoll/worktc-platform-php-postgre.git
```

Copiar el fichero .env.example a .env, borrar los comentarios y elegir los puertos disponible en local *(recomendable que sean seguidos)*
```bash
# REMOVE COMMENTS WHEN COPY THIS FILE AND TRIM TRAILING WHITESPACES
# Ask the team for recommended values

# DOCKER VARIABLES FOR AUTOMATION
SUDO=sudo                                               # <- how local user executes system commands - leave it empty if not necessary ----------------------> #
DOCKER=sudo docker                                      # <- how local user executes Docker commands --------------------------------------------------------> #
DOCKER_COMPOSE=sudo docker compose                      # <- how local user executes "docker compose" or docker-compose command -----------------------------> #

# CONTAINERS BASE INFORMATION FOR BUILDING WITH docker-compose.yml
PROJECT_NAME="WORK TC"                                  # <- project name will be used for automation outputs -----------------------------------------------> #
PROJECT_LEAD=wtc                                        # <- abbreviation or acronym name as part of the container tag that is useful relationship naming ---> #
PROJECT_HOST="127.0.0.1"                                # <- machine hostname referrer - not necessary for this project -------------------------------------> #
PROJECT_CNET=snf-dev                                    # <- useful when a network is required for container connections between each other -----------------> #

# DATABASE - LOCAL
DATABASE_PLTF=pgsql-16.4                                # <- platform assets directory's name ---------------------------------------------------------------> #
DATABASE_IMGK=alpine3.22-pgsql-16.4                     # <- real main image keys to manage automations for sharing resources -------------------------------> #
DATABASE_PORT=7700                                      # <- local machine port opened for container service ------------------------------------------------> #
DATABASE_CAAS=snf-pgsql-dev                             # <- container name to build the service - it is important to set the environment in this variable --> #
DATABASE_CAAS_MEM=128M                                  # <- container's maximum RAM usage to apply by docker-compose ---------------------------------------> #
DATABASE_CAAS_SWAP=512M                                 # <- container's RAM swap space in storage executed by automation command ---------------------------> #
DATABASE_ROOT="J4YPuJaieJ35gNAsk5U2usyphvnjAMRe"        # <- database root password -------------------------------------------------------------------------> #
DATABASE_NAME=wtc_local                                 # <- database name ----------------------------------------------------------------------------------> #
DATABASE_USER=devuser                                   # <- database user ----------------------------------------------------------------------------------> #
DATABASE_PASS="devpass"                                 # <- database password ------------------------------------------------------------------------------> #
DATABASE_PATH="/resources/database/"                    # <- sql file's directory ---------------------------------------------------------------------------> #
DATABASE_INIT=pgsql-init.sql                            # <- init sql file ----------------------------------------------------------------------------------> #
DATABASE_BACK=pgsql-backup.sql                          # <- backup sql file --------------------------------------------------------------------------------> #

# REDIS - LOCAL
REDIS_PLTF=redis-8.6                                    # <- platform assets directory's name ---------------------------------------------------------------> #
REDIS_IMGK=alpine-3.23-redis-8.6                        # <- real main image keys to manage automations for sharing resources -------------------------------> #
REDIS_PORT=7701                                         # <- local machine port opened for container service ------------------------------------------------> #
REDIS_BIND="../data"                                    # <- container persistant data on local machine -----------------------------------------------------> #
REDIS_CAAS=snf-redis-dev                                # <- container name to build the service - it is important to set the environment in this variable --> #
REDIS_CAAS_MEM=128M                                     # <- container's maximum RAM usage to apply by docker-compose ---------------------------------------> #
REDIS_CAAS_SWAP=512M                                    # <- container's RAM swap space in storage executed by automation command ---------------------------> #
REDIS_ROOT_USER=rootuser                                # <- database root user -----------------------------------------------------------------------------> #
REDIS_ROOT_PASS=rootpass                                # <- database root password -------------------------------------------------------------------------> #
REDIS_APP_USER=devuser                                  # <- database user ----------------------------------------------------------------------------------> #
REDIS_APP_PASS=devpass                                  # <- database password ------------------------------------------------------------------------------> #

# MONGODB - LOCAL
MONGODB_PLTF=mongodb-8.2                                # <- platform assets directory's name ---------------------------------------------------------------> #
MONGODB_IMGK=mongo:8.2.4                                # <- real main image keys to manage automations for sharing resources -------------------------------> #
MONGODB_BIND="../data"                                  # <- container persistant data on local machine -----------------------------------------------------> #
MONGODB_CAAS=snf-mongodb-dev                            # <- container name to build the service - it is important to set the environment in this variable --> #
MONGODB_CAAS_MEM=128M                                   # <- container's maximum RAM usage to apply by docker-compose ---------------------------------------> #
MONGODB_CAAS_SWAP=512M                                  # <- container's RAM swap space in storage executed by automation command ---------------------------> #
MONGODB_PORT=7702                                       # <- local machine port opened for container service ------------------------------------------------> #
MONGODB_NAME=wtc_local                                  # <- default database name --------------------------------------------------------------------------> #
MONGODB_USER=rootuser                                   # <- database root user -----------------------------------------------------------------------------> #
MONGODB_PASS=rootpass                                   # <- database root password -------------------------------------------------------------------------> #
MONGODB_APP_PORT=7703                                   # <- local machine port opened for container service client -----------------------------------------> #
MONGODB_APP_USER=devuser                                # <- database user ----------------------------------------------------------------------------------> #
MONGODB_APP_PASS=devpass                                # <- database password ------------------------------------------------------------------------------> #

# API - LOCAL
APIREST_PLTF=nginx-php-8.3                              # <- platform assets directory's name ---------------------------------------------------------------> #
APIREST_IMGK=alpine3.22-nginx1.28-php8.3                # <- real main image keys to manage automations for sharing resources -------------------------------> #
APIREST_PORT=7704                                       # <- local machine port opened for container service ------------------------------------------------> #
APIREST_BIND="../../../apirest"                         # <- path where application is binded from container to local ---------------------------------------> #
APIREST_CAAS=snf-apirest-dev                            # <- container name to build the service - it is important to set the environment in this variable --> #
APIREST_CAAS_USER=osuser                                # <- container's project directory user -------------------------------------------------------------> #
APIREST_CAAS_GROUP=osgroup                              # <- container's project directory group ------------------------------------------------------------> #
APIREST_CAAS_MEM=128M                                   # <- container's maximum RAM usage to apply by docker-compose ---------------------------------------> #
APIREST_CAAS_SWAP=512M                                  # <- container's RAM swap space in storage executed by automation command ---------------------------> #

# MAILER - LOCAL
MAILER_PLTF=mailhog-1.0                                 # <- platform assets directory's name ---------------------------------------------------------------> #
MAILER_IMGK=mailhog:alpine-3.12-mailhog-1.0             # <- real main image keys to manage automations for sharing resources -------------------------------> #
MAILER_PORT=7705                                        # <- local machine port opened for container service ------------------------------------------------> #
MAILER_CAAS=snf-mailhog-dev                             # <- container name to build the service - it is important to set the environment in this variable --> #
MAILER_CAAS_MEM=128M                                    # <- container's maximum RAM usage to apply by docker-compose ---------------------------------------> #
MAILER_CAAS_SWAP=512M                                   # <- container's RAM swap space in storage executed by automation command ---------------------------> #
MAILER_APP_PORT=7706                                    # <- application ui management port -----------------------------------------------------------------> #

# BROKER - LOCAL
BROKER_PLTF=rabbitmq                                    # <- platform assets directory's name ---------------------------------------------------------------> #
BROKER_IMGK=rabbitmq:4.2-management-alpine              # <- real main image keys to manage automations for sharing resources -------------------------------> #
BROKER_PORT=7707                                        # <- local machine port opened for container service ------------------------------------------------> #
BROKER_BIND="./rabbitmq_data"                           # <- platform broker data storage in local ----------------------------------------------------------> #
BROKER_CAAS=snf-rabbitmq-dev                            # <- container name to build the service - it is important to set the environment in this variable --> #
BROKER_CAAS_MEM=128M                                    # <- container's maximum RAM usage to apply by docker-compose ---------------------------------------> #
BROKER_CAAS_SWAP=512M                                   # <- container's RAM swap space in storage executed by automation command ---------------------------> #
BROKER_APP_PORT=7708                                    # <- application ui management port -----------------------------------------------------------------> #
BROKER_APP_USER=guest                                   # <- application ui management user -----------------------------------------------------------------> #
BROKER_APP_PASS=guest                                   # <- application ui management password -------------------------------------------------------------> #
BROKER_APP_COOKIE="3pBRVIu08orKbB7ddlEFeSZ2sQ4kpX8d"    # <- application security ---------------------------------------------------------------------------> #
BROKER_APP_NODENAME=rabbit@rabbitmq                     # <- application configuration ----------------------------------------------------------------------> #
```

### Makefile

Este repositorio contiene un Makefile para automatizar comandos. Se puede ver los comandos que contiene de la siguiente manera
```bash
$ make help
Usage: $ make [target]
Targets:
$ make help                           shows this Makefile help message

$ make local-info                     shows local machine ip and container ports set
$ make local-ownership                shows local ownership
$ make local-ownership-set            sets recursively local root directory ownership

# Shorthand for all services
$ make services-set                   sets all container services
$ make services-create                builds and starts up all container services
$ make services-info                  shows all container services information
$ make services-destroy               destroys all container services

# REST API - Symfony
$ make apirest-hostcheck              shows this project ports availability on local machine for apirest container
$ make apirest-info                   shows the apirest docker related information
$ make apirest-set                    sets the apirest enviroment file to build the container
$ make apirest-create                 creates the apirest container from Docker image
$ make apirest-network                creates the apirest container network - execute this recipe first before others
$ make apirest-ssh                    enters the apirest container shell
$ make apirest-start                  starts the apirest container running
$ make apirest-stop                   stops the apirest container but its assets will not be destroyed
$ make apirest-restart                restarts the running apirest container
$ make apirest-destroy                destroys completly the apirest container

# Mailhog
$ make mailer-hostcheck               shows this project ports availability on local machine for mailer container
$ make mailer-info                    shows the mailer docker related information
$ make mailer-set                     sets the mailer enviroment file to build the container
$ make mailer-create                  creates the mailer container from Docker image
$ make mailer-network                 creates the mailer container network - execute this recipe first before others
$ make mailer-ssh                     enters the mailer container shell
$ make mailer-start                   starts the mailer container running
$ make mailer-stop                    stops the mailer container but its assets will not be destroyed
$ make mailer-restart                 restarts the running mailer container
$ make mailer-destroy                 destroys completly the mailer container

# Postgre
$ make db-hostcheck                   shows this project ports availability on local machine for database container
$ make db-info                        shows docker related information
$ make db-set                         sets the database enviroment file to build the container
$ make db-create                      creates the database container from Docker image
$ make db-network                     creates the database container external network
$ make db-ssh                         enters the apirest container shell
$ make db-start                       starts the database container running
$ make db-stop                        stops the database container but its assets will not be destroyed
$ make db-restart                     restarts the running database container
$ make db-destroy                     destroys completly the database container with its data
$ make db-test-up                     creates a side database for testing porpuses
$ make db-test-down                   drops the side testing database
$ make db-sql-install                 migrates sql file with schema / data into the container main database to init a project
$ make db-sql-replace                 replaces the container main database with the latest database .sql backup file
$ make db-sql-backup                  copies the container main database as backup into a .sql file
$ make db-sql-drop                    drops the container main database but recreates the database without schema as a reset action

# MongoDB
$ make mongodb-hostcheck              shows this project ports availability on local machine for database container
$ make mongodb-info                   shows docker related information
$ make mongodb-set                    sets the database enviroment file to build the container
$ make mongodb-create                 creates the database container from Docker image
$ make mongodb-network                creates the database container external network
$ make mongodb-ssh                    enters the apirest container shell
$ make mongodb-start                  starts the database container running
$ make mongodb-stop                   stops the database container but its assets will not be destroyed
$ make mongodb-restart                restarts the running database container
$ make mongodb-destroy                destroys completly the database container with its data

# Redis
$ make redis-hostcheck                shows this project ports availability on local machine for database container
$ make redis-info                     shows docker related information
$ make redis-set                      sets the database enviroment file to build the container
$ make redis-create                   creates the database container from Docker image
$ make redis-network                  creates the database container external network
$ make redis-ssh                      enters the apirest container shell
$ make redis-start                    starts the database container running
$ make redis-stop                     stops the database container but its assets will not be destroyed
$ make redis-restart                  restarts the running database container
$ make redis-destroy                  destroys completly the database container with its data

# RabbitMQ
$ make broker-hostcheck               shows this project ports availability on local machine for broker container
$ make broker-info                    shows the broker docker related information
$ make broker-set                     sets the broker enviroment file to build the container
$ make broker-create                  creates the broker container from Docker image
$ make broker-network                 creates the broker container network - execute this recipe first before others
$ make broker-ssh                     enters the broker container shell
$ make broker-start                   starts the broker container running
$ make broker-stop                    stops the broker container but its assets will not be destroyed
$ make broker-restart                 restarts the running broker container
$ make broker-destroy                 destroys completly the broker container
$ make repo-flush                     echoes clearing commands for git repository cache on local IDE and sub-repository tracking remove
$ make repo-commit                    echoes common git commands
```
<br>

## <a id="apirest"></a>Instalación de la REST API

- Eliminar el contenido del directorio `./apirest` tanto del local como de GIT:
```bash
$ git rm -r --cached -- "apirest/*" ":(exclude)apirest/.gitkeep"
$ git clean -fd
$ git reset --hard
$ git commit -m "Remove apirest directory and its default installation"
```

- Clonar dentro del direcotrio `./apirest` [https://github.com/pabloripoll/worktc-platform-php-postgre-restapi](https://github.com/pabloripoll/worktc-platform-php-postgre-restapi)
```bash
$ cd ./apirest
$ git@github.com:pabloripoll/worktc-platform-php-postgre-restapi.git .
```

- El directorio `./apirest` ahora es un **repositorio independiente** y no se rastreará como submódulo en el repositorio principal. Puedes usar comandos de `git` libremente dentro de `apirest` desde el local ó desde dentro del contenedor.

### Proyecto Symfony

Una vez instalado la plataforma y el contenedor de la REST API funcionando, hay que ejecutar los comandos de inicialización
```bash
# Entrar al contenedor desde el local
$ make apirest-ssh
```

Instalar la aplicación con Composer
```bash
$ composer install
```

Generar las claves de JWT
```bash
# Generate JWT keys if they don't exist
/var/www $ mkdir -p config/jwt

# Generate private key
/var/www $ openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096

# Generate public key
/var/www $ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

# Set permissions
/var/www $ chmod 644 config/jwt/private.pem
/var/www $ chmod 644 config/jwt/public.pem
```

Ejecutar las migraciones y fixtures
```bash
# Migrations
$ php bin/console doctrine:migrations:migrate

# Fixtures
$ php bin/console doctrine:fixtures:load --append --dry-run # See fixtures
$ php bin/console doctrine:fixtures:load --append --no-interaction # Avoid purging existing data
```

Ejecutar los tests Unitarios, Integración y Funcionales
```bash
# Setup test database - Load fixtures for testing - Verify the schema
/var/www $ php bin/console --env=test doctrine:database:create --if-not-exists
/var/www $ php bin/console --env=test doctrine:schema:create
/var/www $ php bin/console --env=test doctrine:fixtures:load --no-interaction
/var/www $ php bin/console doctrine:schema:validate --env=test

# Run all tests
/var/www $ php vendor/bin/phpunit

# Run specific test suite
/var/www $ php vendor/bin/phpunit --testsuite=Unit
/var/www $ php vendor/bin/phpunit --testsuite=Integration
/var/www $ php vendor/bin/phpunit --testsuite=Functional

# Run specific test file
/var/www $ php vendor/bin/phpunit tests/Unit/Domain/Shared/ValueObject/EmailTest.php --testdox

# Remove test database for new tests execution if required
/var/www $ php bin/console --env=test doctrine:database:drop --force --if-exists
/var/www $ php bin/console cache:clear --env=test
```

Ejecutar los tests estáticos
```bash
/var/www $  composer phpstan

# Clear cache
/var/www $ rm -rf var/phpstan
```

Ejecutar todos los tests a la vez
```bash
/var/www $  rm -rf var/phpstan; composer phpstan; php vendor/bin/phpunit --testdox
```

<!-- FOOTER -->
<br>

---

<br>

- [GO TOP ⮙](#top-header)

<div style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>