<div id="top-header" style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>

# WORKTIME CONTROLLER - SYMFONY 7

- [/README.md](../README.md)

# Desarrollo

En base a lo requerido en el objetivo del proyecto de examen, he trabajado en las principales catacterísticas de la REST API y he sumado el desarrollo de las plataformas donde se ejecutará el sistema con Docker de manera tal que, tanto el trabajo en local como en remoto *(testing, staging ó producción)* el stack posea las mismas características.

## Contenedores

- NGINX + PHP 8.3 *(Con los módulos requiridos para conexión con Postgre, RabbitMQ, Redis y MongoDB)*
- Postgres 16.4
- MongoDB
- Redis
- RabbitMQ
- Mailhog

## <a id="apirest-features"></a>Características

- **API RESTful**: Sigue patrones REST comunes para endpoints orientados a recursos.

- **API sin estado**: Cada solicitud es autónoma, cumpliendo con los principios REST.

- **Diseño basado en dominios**: Cada dominio es autónomo en un único directorio, excepto los recursos específicos del framework.

- **Acceso basado en roles JWT**: Los flujos de autenticación y autorización son compatibles tanto con usuarios normales como con administradores, utilizando JWT con control de acceso basado en roles.

- **Registro e inicio de sesión de usuarios**: Registro e inicio de sesión seguros para miembros con autenticación basada en JWT.

- **Operaciones CRUD**: Los usuarios pueden crear, actualizar y eliminar su propio contenido.

- **Principios SOLID**: Aplica las mejores prácticas en estructura de código, validación, gestión de errores y formatos de respuesta.

- **Endpoints de miembros y administradores**: Endpoints dedicados para la gestión de usuarios/contenido, estadísticas y herramientas de moderación.

- **Gestión integral de errores de API**: Respuestas estandarizadas y consistentes para errores y validación.

- **Pruebas de integración y análisis estático**: Incluye scripts y herramientas para pruebas automatizadas de endpoints y análisis estático de código para garantizar la calidad.

#### Stack

- **Framework:** [Symfony 7](https://symfony.com/)
- **Authentication:** [Lexik JWT](https://packagist.org/packages/tymon/jwt-auth)
- **Testing:** [PHPUnit](https://phpunit.de/)
- **Static Analysis:** [PHPStan](https://phpstan.org/) - Level 3 *(recomendado como mínimo nivel para evitar bugs en producción)*
- **Database:** [PostgreSQL](https://www.postgresql.org/)
<br>

## Propuesta de Estructura Exagonal + DDD

### Principios clave aplicados

#### 1. Hexagonal Architecture (Ports & Adapters)

| Capa | Responsabilidad | Ejemplo |
|-------|----------------|---------|
| **Dominio** | Lógica de negocio, entidades, servicios de dominio, **interfaces** de repositorio | `Domain/Member/Entity/Member.php`, `Domain/Member/Repository/MemberRepositoryInterface.php` |
| **Aplicación** | Casos de uso (comandos/consultas), orquestación, DTO | `Application/Member/Command/RegisterMemberCommand.php` + `RegisterMemberHandler.php` |
| **Infraestructura** | Adaptadores: BD (Doctrine), mensajería (Symfony Messenger), API externas | `Infrastructure/Persistence/Doctrine/Repository/MemberRepository.php` (implementa `MemberRepositoryInterface`) |
| **Presentación** | Controladores, comandos CLI, puntos finales de API | `Presentación/Http/Rest/Miembro/MemberAuthController.php` |

#### 2. Dirección de dependencia (Inversión de dependencia SOLID)

Presentación → Aplicación → Dominio ← Infraestructura

- El dominio no depende de otras capas (lógica de negocio pura).
- La aplicación depende únicamente del dominio (utiliza entidades del dominio y llama a las interfaces del repositorio).
- La infraestructura implementa las interfaces del dominio (p. ej., MemberRepository implementa MemberRepositoryInterface).
- La presentación llama a los casos de uso de la aplicación (p. ej., el controlador envía RegisterMemberCommand).
<br>

#### 3. Patrones tácticos DDD

- Entidades: Objetos de dominio con comportamiento (Miembro, Administrador, FeedPost)
- Objetos de valor: Inmutables y autovalidados (ID de miembro, Correo electrónico, Código de activación)
- Agregados: La raíz del agregado de miembros contiene Perfil de miembro y Código de activación de miembro
- Servicios de dominio: Lógica compleja que no cabe en una sola entidad (Servicio de registro de miembros)
- Eventos de dominio: Evento de registro de miembro, Evento de inicio de sesión de administrador (distribuidos desde entidades/servicios)
- Interfaces de repositorio: Definidas en el dominio, implementadas en la infraestructura

#### 4. Esquema de Estructura
- [Propuesta de Estructura Exagonal + DDD](./Hexagonal.md)
<br>

## Contrato de la REST API

En base a los puntos requeridos:

1. **CRUD de Usuarios**: Crear, leer, actualizar y eliminar usuarios.

2. **CRUD de Fichajes**: Crear, leer, actualizar y eliminar fichajes de empleados.

3. **Login**: Crear un login de acceso que devuelve un token.

4. **Registro de Fichajes**: Implementar las operaciones de registro de entrada y salida para los empleados.

5. **Listado de Fichajes**: Mostrar un registro de todos los fichajes de un empleado, ordenados por fecha y hora.

6. **Seguridad**: Asegurar que solo los empleados autenticados puedan registrar sus fichajes y acceder a su historial.
<br>

Contrato de la API:
- [Contrato de la REST API](./API-Contract.md)
<br>

## Esquema de Entidades

Para un control de acciones de los usuarios de la aplicación, he desarrollado los siguientes niveles de usuarios

- **ROLE_ADMIN**: Administrativo de RRHH / Empleador
- **ROLE_MEMBER**: Empleado

Si bien ambos usuarios pertenecen a un mismo **dominio** de cara al sistema, los **admin** y **miembros/trabajador** poseen diferentes características en la que los distintos dominios puede evolucionar de manera separada, sin depender uno del otro.

- [Esquema de Entidades](./Schemas.md)
<br>

## Tareas Pendientes

Como el objetivo final es poder concretar añadir el proyecto a un sistema **Event Driven Arquitecture** y **CQRS** *(Command Query Responsibility Segregation)* he implementado desde la plataforma y configuración de Symfony 7 para consumir dichos servicios. Por ello, he dejado las siguientes pruebas manuales a través de la API, la conexión con los mismos:

```bash
PUBLIC        GET      /api/v1/test                         #-> Test api version 1
PUBLIC        GET      /api/v1/test/database                #-> Test de conexión con base de datos Postgre
PUBLIC        GET      /api/v1/test/mailer                  #-> Test de conexxión con servicio de mensajería
PUBLIC        GET      /api/v1/test/broker                  #-> Test de conexión con RabbitMQ
PUBLIC        GET      /api/v1/test-redis                   #-> Test de conexión con Redis
PUBLIC        GET      /api/v1/test/event-db                #-> Test de conexión con MongoDB
PUBLIC        GET      /api/v1/test/all                     #-> Test de todas las conexiones
```

<!-- FOOTER -->
<br>

---

<br>

- [GO TOP ⮙](#top-header)

<div style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>