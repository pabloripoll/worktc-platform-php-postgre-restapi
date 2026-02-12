<div id="top-header" style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>

# WORKTIME CONTROLLER - SYMFONY 7

- [/README.md](../README.md)

# Objetivos del Proyecto

En esta prueba técnica, el objetivo es desarrollar una API de gestión de fichajes de empleados utilizando PHP y Symfony, aplicando los principios SOLID, la arquitectura hexagonal y DDD. El sistema debe permitir a los empleados registrar sus entradas y salidas, así como visualizar un registro de todos sus fichajes. Cada fichaje debe contener la información de la fecha y la hora de entrada/salida, el empleado asociado y cualquier información adicional relevante.
<br>

## Requerimientos Técnicos

- SOLID: Se espera que el código esté bien estructurado y siga los principios SOLID para garantizar una fácil extensibilidad y mantenibilidad del sistema.

- Arquitectura Hexagonal: Debe implementarse una arquitectura hexagonal para separar claramente las capas de la aplicación (dominio, aplicación y adaptadores) y facilitar la integración con diferentes componentes externos.

- Domain Driven Design (DDD): Se debe aplicar DDD para modelar el dominio del problema de manera adecuada, identificando y definiendo las entidades, agregados, servicios y eventos relevantes para el sistema.
<br>

## Entidades

- User
     - **id**: Identificador único del usuario (UUID).
     - **name**: Nombre del usuario (cadena de caracteres).
     - **email**: Correo electrónico del usuario (cadena de caracteres).
     - **password**: Contraseña del usuario (cadena de carácteres hash)
     - **createdAt**: Fecha y hora de creación del usuario (timestamp).
     - **updatedAt**: Fecha y hora de la última actualización del usuario (timestamp).
     - **deletedAt**: Fecha y hora en la que se eliminó lógicamente el usuario (timestamp nullable).

- WorkEntry
     - **id**: Identificador único de la entrada de trabajo (UUID).
     - **userId**: Identificador del usuario asociado a la entrada de trabajo (UUID).
     - **startDate**: Fecha y hora de inicio de la entrada de trabajo (timestamp).
     - **endDate**: Fecha y hora de finalización de la entrada de trabajo (timestamp nullable).
     - **createdAt**: Fecha y hora de creación de la entrada de trabajo (timestamp).
     - **updatedAt**: Fecha y hora de la última actualización de la entrada de trabajo (timestamp).
     - **deletedAt**: Fecha y hora en la que se eliminó lógicamente la entrada de trabajo (timestamp nullable).
<br>

## Funcionalidades requeridas

1. **CRUD de Usuarios**: Crear, leer, actualizar y eliminar usuarios.

2. **CRUD de Fichajes**: Crear, leer, actualizar y eliminar fichajes de empleados.

3. **Login**: Crear un login de acceso que devuelve un token.

4. **Registro de Fichajes**: Implementar las operaciones de registro de entrada y salida para los empleados.

5. **Listado de Fichajes**: Mostrar un registro de todos los fichajes de un empleado, ordenados por fecha y hora.

6. **Seguridad**: Asegurar que solo los empleados autenticados puedan registrar sus fichajes y acceder a su historial.
<br>

## Opcionales

- Event Driven: Si lo consideras apropiado, puedes implementar el patrón de Event Driven para mantener un registro de todos los cambios en los  fichajes de los empleados a lo largo del tiempo. Esto puede agregar un valor significativo al sistema al permitir una auditoría completa de los datos.

- CQRS (Command Query Responsibility Segregation): Otra opción es implementar el patrón CQRS para separar las operaciones de lectura (queries) de las operaciones de escritura (commands), lo que puede mejorar la escalabilidad y el rendimiento del sistema.

Las tareas marcadas como opcionales no necesariamente deben ser desarrolladas en su totalidad. Entendemos que algunos candidatos pueden no tener el
tiempo adicional que estas tareas pueden requerir. En lugar de completarlas, puedes optar por explicar cómo implementarías dichas tareas. Esto nos permitirá evaluar tu pensamiento y enfoque hacia la resolución de problemas, sin requerir una inversión de tiempo adicional.
<br>

## Evaluación y Entrega

Desarrollo sobre Docker: El desarrollo y la entrega del proyecto deben realizarse utilizando Docker para garantizar la consistencia del entorno de desarrollo y simplificar el proceso de configuración. Se evaluará la calidad del código, la implementación de los principios SOLID, la adecuada aplicación de la arquitectura hexagonal y DDD, así como cualquier implementación opcional, tanto desarrollada a nivel de código como una explicación de cómo la implementarías.

Además, se valorará la cobertura de pruebas unitarias y la eficiencia de la solución propuesta. Necesitamos que nos envíes el código fuente de tu
proyecto. Y, para que no nos perdamos, incluye unas instrucciones claras de cómo poner en marcha la aplicación y las pruebas unitarias. Así  podremos evaluar tu trabajo de la mejor manera.

<!-- FOOTER -->
<br>

---

<br>

- [GO TOP ⮙](#top-header)

<div style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>