<div id="top-header" style="with:100%;height:auto;text-align:right;">
    <img src="./public/files/pr-banner-long.png">
</div>

# SOCIAL FEED - SYMFONY 7

This repository contains a basic example of a RESTful API service built with **Symfony 7**, intended for research purposes and as a demonstration of my developer profile. It implements the core features of a minimal, custom social feed application and serves as a reference project for learning, experimentation, or as a back-end development code sample.

> ⚠️ **Project Status: In Development**
>
> This repository is currently under active development and not yet ready for use. Features and APIs may change, and breaking changes can occur. Please, be aware that the codebase is actively evolving.

## Project Overview

The API supports a registry of platform "members," enabling users to create posts and voting with like or dislike other users' posts. An administrator role is provided for managing users, content, and platform statistics via a dedicated back office.

## Content of this page:

- [REST API Features](#apirest-features)
- [Infrastructure Platform](#infrastructure-platform)
- [REST API - Symfony 7](#apirest-symfony)
- [API Authentication with JWT](#apirest-jwt)
- [Swagger API Documentation](#apirest-swagger)
- [Domain Driven Design](#apirest-ddd)
- [Use this Platform Repository for REST API project](#platform-usage)
<br><br>

## <a id="apirest-features"></a>REST API Features

- **RESTful API** — Follows common REST patterns for resource-oriented endpoints.
- **Stateless API** — Each request is self-contained, adhering to REST principles.
- **Domain-Driven Design** — Each domain is self-contained in a single directory, except for resources specific to the framework.
- **JWT Role-Based Access** — Authentication and authorization flows support both regular users and administrators, using JWTs with role-based access control.
- **User Registration and Login** — Secure registration and login for members with JWT-based authentication.
- **CRUD Operations** — Users can create, update, and delete their own content.
- **SOLID Principles** — Applies best practices in code structure, validation, error handling, and response formats.
- **Member and Admin Endpoints** — Dedicated endpoints for user/content management, statistics, and moderation tools.
- **Comprehensive API Error Handling** — Standardized, consistent responses for errors and validation.
- **Integration Testing & Static Analysis** — Includes scripts and tools for automated endpoint testing and static code analysis to ensure quality.
- **OpenAPI/Swagger Documentation** — Interactive API documentation generated from code annotations, accessible via a web interface.

#### Tech Stack

- **Framework:** [Symfony 7](https://symfony.com/)
- **Authentication:** [Lexik JWT](https://packagist.org/packages/tymon/jwt-auth)
- **Testing:** [PHPUnit](https://phpunit.de/)
- **Static Analysis:** [PHPStan](https://phpstan.org/)
- **Documentation:** [NelmioApiDocBundle](https://symfony.com/bundles/NelmioApiDocBundle/current/index.html)
- **Database:** [PostgreSQL](https://www.postgresql.org/)
<br><br>

> **Note**: This project is intended for educational and evaluation purposes only. It is not production-ready, but can be extended for more complex scenarios. Contributions and suggestions are welcome!

> **Convention:** `$` at the start of a line means "run this command in your shell."

<br>

## <a id="infrastructure-platform"></a>Infrastructure Platform

You can use your own local infrastructure to clone and run this repository. However, if you use [GNU Make](https://www.gnu.org/software/make/) installed, we recommend using the dedicated Docker repository [**NGINX 1.28, PHP 8.3 - POSTGRES 17.5**](https://github.com/pabloripoll/docker-platform-nginx-php-8.3-pgsql-17.5)

With just a few configuration steps, you can quickly set up this project—or any other—with this same required stack.

**Repository directories structure overview:**
```
.
├── apirest (Symfony)
│   ├── bin
│   ├── config
│   ├── migrations
│   └── ...
│
├── platform
│   ├── nginx-php
│   │   ├── docker
│   │   │   ├── config
│   │   │   │   ├── php
│   │   │   │   ├── nginx
│   │   │   │   └── supervisor
│   │   │   ├── .env
│   │   │   ├── docker-compose.yml
│   │   │   └── Dockerfile
│   │   │
│   │   └── Makefile
│   └── postgres-17.5
│       ├── docker
│       └── Makefile
├── .env
├── Makefile
└── README.md
```

Follow the documentation to implement it:
- https://github.com/pabloripoll/docker-platform-nginx-php-8.3-pgsql-17.5?tab=readme-ov-file#platform--usage
<br><br>

## <a id="apirest-symfony"></a>REST API - Symfony 7

The following steps assume you are using the recommended [NGINX-PHP with Postgres 17.5 platform repository](https://github.com/pabloripoll/docker-platform-nginx-php-8.3-pgsql-17.5).

Clone the repository
```bash
$ cd ./apirest
$ git clone https://github.com/your-username/social-feed-symfony.git .
```
<br>

Set up environment
- Copy `.env.example` to `.env` and adjust settings (database, JWT secret, etc.)
<br>

Access container to install the project
```bash
$ make apirest-ssh

/var/www $
```

Once accessed into the container, you will placed into root proyect directory at `/var/www`
```bash
/var/www $ composer install
```
<br>

Generate app key and JWT secret
```bash
/var/www $ php bin/console secrets:generate-keys
/var/www $ php bin/console lexik:jwt:generate-keypair
```
<br>

Run database models migrations
```bash
/var/www $ php bin/console doctrine:migrations:migrate
```
<br>

<font color="orange"><b>IMPORTANT:</b></font> Editing project scripts and source code can be done directly `./apirest` on your local machine. Enter the container only when you need to run ***Composer*** or ***Symfony CLI*** commands.
<br><br>

## <a id="apirest-jwt"></a>API Authentication with JWT

This application uses JWT for stateless authentication:

- **Token lifecycle:**
  - Access tokens are valid for 90 minutes (JWT TTL), but the access token registry expiration is set to 60 minutes.
  - Tokens can only be refreshed if their expiration is recorded in the `members_access_logs` or `admins_access_logs` table.
  - When a token expires but is still eligible for refresh, the API responds with:
    ```bash
    HTTP CODE 403
    ```
    ```json
    {
        "message": "Token is expired.",
        "error": "token_expired"
    }
    ```
  - If a token is invalidated (e.g., via logout), or has expired beyond both the registry and JWT TTL, it cannot be refreshed.
<br><br>

## <a id="apirest-swagger"></a>Swagger API Documentation

The Swagger API documentation is available at:
`http://127.0.0.1:[selected-port]/api/doc`

**Tip:** Replace `[selected-port]` with the actual port mapped to your container if it's not the default 80.
<br><br>

## <a id="apirest-ddd"></a>Domain Driven Design

Domain Driven Design (DDD) is a software development approach that emphasizes modeling software to match a business domain as closely as possible. In a DDD project, code is organized around the core business concepts, rules, and processes, rather than technical layers (like "Controllers" or "Entities" globally).

There are several approaches to structuring a DDD project. In this project, each **Domain** is implemented as a modularized Service Provider within Symfony. This design promotes separation of concerns, encapsulation, and reusability.

### Key Characteristics of this DDD Approach

- **Domains as Modules:**
  Each business domain (such as "Admin", "Member", or "Post") is contained within its own directory under `./src/Domain/`, following a modular structure. This means each domain encapsulates its own controllers, models, requests, routes, services, and tests.

- **Service Providers:**
  Each domain registers a Symfony Service Provider e.g., `./config/packages/doctrine.yaml` and `./config/routes/annotations.yaml`, which are the main configuration files for mapping domain-specific bindings, event listeners, and routes. This makes domain logic easy to plug in or remove from the application.

- **Encapsulation:**
  By grouping all logic, data models, and services related to a domain together, each domain remains independent, preventing unintended coupling between features.

- **Scalability & Maintainability:**
  New domains or features can be added with minimal impact on existing code, and cross-domain interactions remain explicit and manageable.

### Project Structure Overview

```
.
├── apirest (Symfony)
│   ├── bin
│   ├── config
│   ├── migrations
│   ├── public
│   │   ├── bundles
│   │   ├── files
│   │   └── index.php
│   ├── src
│   │   ├── Domain
│   │   │   ├── Admin
│   │   │   ├── Member
│   │   │   │   ├── Controller
│   │   │   │   ├── Entity
│   │   │   │   ├── Fixtures
│   │   │   │   ├── Repository
│   │   │   │   ├── Service
│   │   │   │   └── Tests
│   │   │   └── Post
│   │   └── Kernel.php
│   ├── templates
│   ├── vendor
│   ├── .env
│   └── Makefile
```
<br>

## Contributing

Contributions are very welcome! Please open issues or submit PRs for improvements, new features, or bug fixes.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/YourFeature`)
3. Commit your changes (`git commit -am 'feat: Add new feature'`)
4. Push to the branch (`git push origin feature/YourFeature`)
5. Create a new Pull Request
<br><br>

## License

This project is open-sourced under the [MIT license](LICENSE).

<!-- FOOTER -->
<br>

---

<br>

- [GO TOP ⮙](#top-header)

<div style="with:100%;height:auto;text-align:right;">
    <img src="./public/files/pr-banner-long.png">
</div>