<div id="top-header" style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>

# WORKTIME CONTROLLER - SYMFONY 7

- [/README.md](../README.md)
- [Implementación de Desarrollo](./Development.md)

# Contrato de la REST API

- Los contratos para usuarios con ROLE_ADMIN estan diferenciadas para por seguridad, puedan ser cambiadas
- Los contratos para los usuarios con ROLE_MEMBER, son las básicas pero no puede acceder los usuarios con ROLE_ADMIN

Este es el contrato de API sugerido para el proyecto
```bash
ROLE_ADMIN    POST     /api/v1/admin/auth/login             #-> Login de los usuarios ROLE_ADMIN
ROLE_ADMIN    GET      /api/v1/admin/profile                #-> Lectura del propio usuario administrador
ROLE_ADMIN    PATCH    /api/v1/admin/profile                #-> Actualización de datos del propio usuario administrador
ROLE_ADMIN    POST     /api/v1/admin/users                  #-> Creación de usuario ROLE_ADMIN
ROLE_ADMIN    GET      /api/v1/admin/users                  #-> Listado de usuarios administradores
ROLE_ADMIN    GET      /api/v1/admin/users/{id}/profiles    #-> Actualizar específico usuario administrador
ROLE_ADMIN    DELETE   /api/v1/admin/users/{id}             #-> Eliminar específico usuario administrador
ROLE_ADMIN    POST     /api/v1/admin/members                #-> Usuarios administradores puede crear usuarios ROLE_MEMBER (empleados/trabajadores)
ROLE_ADMIN    GET      /api/v1/admin/members                #-> Listado de usuarios empleados
ROLE_ADMIN    GET      /api/v1/admin/members/{id}/profiles  #-> Leer perfil de empleado específico
ROLE_ADMIN    PATCH    /api/v1/admin/members/{id}/profiles  #-> Actualizar empleado específico
ROLE_ADMIN    DELETE   /api/v1/admin/members/{id}/profiles  #-> Eliminar empleado específico
ROLE_ADMIN    GET      /api/v1/admin/members/{id}/clockings       #-> Listado de todos los registros de horario de un usuario específico
ROLE_ADMIN    GET      /api/v1/admin/members/{id}/clockings/{id}  #-> Lectura del registro de horario de un usuario específico
ROLE_ADMIN    PATCH    /api/v1/admin/members/{id}/clockings/{id}  #-> Actualización del registro de horario de un usuario específico
ROLE_ADMIN    DELETE   /api/v1/admin/members/{id}/clockings/{id}  #-> Eliminación del registro de horario de un usuario específico

ROLE_MEMBER   POST     /api/v1/auth/login                   #-> Login de empleado
ROLE_MEMBER   POST     /api/v1/clockings                    #-> Registro de horario de trabajo de empleado autentificado
ROLE_MEMBER   GET      /api/v1/clockings                    #-> Listado de horarios del propio usuario
ROLE_MEMBER   GET      /api/v1/clockings/{id}               #-> Lectura de un horario específico
ROLE_MEMBER   PATCH    /api/v1/clockings/{id}               #-> Actualización de un horario específico
ROLE_MEMBER   DELETE   /api/v1/clockings/{id}               #-> Borrado de un horario específico
ROLE_MEMBER   GET      /api/v1/profile                      #-> Lectura del propio perfil de usuario
ROLE_MEMBER   PATCH    /api/v1/profile                      #-> Actualización del propio perfil de usuario

ANY_ROLE      POST     /api/v1/auth/refresh                 #-> Actualización de token de sesión (Stateless - JWT)
ANY_ROLE      POST     /api/v1/auth/logout                  #-> Finalización de sesión (Stateless - JWT)
ANY_ROLE      GET      /api/v1/auth/whoami                  #-> Consulta de mi sesión de usuario (Stateless - JWT)

PUBLIC        GET      /api/v1/test                         #-> Test api version 1
PUBLIC        GET      /api/v1/test/database                #-> Test de conexión con base de datos Postgre
PUBLIC        GET      /api/v1/test/mailer                  #-> Test de conexxión con servicio de mensajería
PUBLIC        GET      /api/v1/test/broker                  #-> Test de conexión con RabbitMQ
PUBLIC        GET      /api/v1/test-redis                   #-> Test de conexión con Redis
PUBLIC        GET      /api/v1/test/event-db                #-> Test de conexión con MongoDB
PUBLIC        GET      /api/v1/test/all                     #-> Test de todas las conexiones
```

Se puede consultar las rutas creadas para el proyecto con el siguiente comando
```bash
$ php bin/console debug:router --show-controllers | grep "/api/"
```

<!-- FOOTER -->
<br>

---

<br>

- [GO TOP ⮙](#top-header)

<div style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>