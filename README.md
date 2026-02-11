<div id="top-header" style="with:100%;height:auto;text-align:right;">
    <img src="./public/files/pr-banner-long.png">
</div>

# WORKTIME CONTROLLER - SYMFONY 7

This repository contains a basic example of a RESTful API service built with **Symfony 7**, intended for research purposes and as a demonstration of my developer profile. It implements the core features of a minimal, custom back-end Work Time Controller application and serves as a reference project for learning, experimentation, or as a back-end development code sample.

> ⚠️ **Project Status: In Development**
>
> This repository is currently under active development and not yet ready for use. Features and APIs may change, and breaking changes can occur. Please, be aware that the codebase is actively evolving.

## Project Overview

The API supports a registry of platform "members," enabling users to create posts and voting with like or dislike other users' posts. An administrator role is provided for managing users, content, and platform statistics via a dedicated back office.

## Content of this page:

- [REST API Features](#apirest-features)
- [Hexagonal/DDD structure](#hexagonal-structure)
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

## <a id="hexagonal-structure"></a>Hexagonal/DDD structure

**Proposed structure desing overview:**
```bash
src/
├── Application/              # Application layer (Use Cases / Commands / Queries / Handlers)
│   ├── Admin/
│   │   ├── Command/
│   │   │   ├── CreateAdminCommand.php
│   │   │   ├── CreateAdminHandler.php
│   │   │   ├── UpdateAdminProfileCommand.php
│   │   │   └── UpdateAdminProfileHandler.php
│   │   ├── Query/
│   │   │   ├── GetAdminByIdQuery.php
│   │   │   ├── GetAdminByIdHandler.php
│   │   │   └── GetAdminListQuery.php
│   │   └── DTO/
│   │       ├── AdminDTO.php
│   │       └── AdminProfileDTO.php
│   ├── Member/
│   │   ├── Command/
│   │   │   ├── RegisterMemberCommand.php
│   │   │   ├── RegisterMemberHandler.php
│   │   │   ├── ActivateMemberCommand.php
│   │   │   └── ActivateMemberHandler.php
│   │   ├── Query/
│   │   │   ├── GetMemberByIdQuery.php
│   │   │   └── GetMemberByIdHandler.php
│   │   └── DTO/
│   │       ├── MemberDTO.php
│   │       └── MemberRegistrationDTO.php
│   └── User/
│       └── Query/
│           └── UserQuery.php
│
├── Domain/                   # Domain layer (Entities, Value Objects, Domain Services, Interfaces)
│   ├── Admin/
│   │   ├── Entity/
│   │   │   ├── Admin.php
│   │   │   ├── AdminProfile.php
│   │   │   └── AdminAccessLog.php
│   │   ├── ValueObject/
│   │   │   ├── AdminId.php
│   │   │   └── AdminEmail.php
│   │   ├── Repository/
│   │   │   └── AdminRepositoryInterface.php  # Interface only
│   │   ├── Service/
│   │   │   └── AdminAuthenticationService.php  # Domain logic
│   │   └── Event/
│   │       ├── AdminCreatedEvent.php
│   │       └── AdminLoggedInEvent.php
│   ├── Member/
│   │   ├── Entity/
│   │   │   ├── Member.php
│   │   │   ├── MemberProfile.php
│   │   │   ├── MemberActivationCode.php
│   │   │   ├── MemberFollower.php
│   │   │   ├── MemberFollowing.php
│   │   │   ├── MemberModeration.php
│   │   │   ├── MemberNotification.php
│   │   │   └── MemberAccessLog.php
│   │   ├── ValueObject/
│   │   │   ├── MemberId.php
│   │   │   ├── MemberEmail.php
│   │   │   ├── ActivationCode.php
│   │   │   └── MemberStatus.php
│   │   ├── Repository/
│   │   │   ├── MemberRepositoryInterface.php
│   │   │   └── MemberActivationCodeRepositoryInterface.php
│   │   ├── Service/
│   │   │   ├── MemberRegistrationService.php  # Domain logic
│   │   │   └── MemberActivationService.php
│   │   └── Event/
│   │       ├── MemberRegisteredEvent.php
│   │       └── MemberActivatedEvent.php
│   ├── User/
│   │   ├── Entity/
│   │   │   └── User.php
│   │   ├── ValueObject/
│   │   │   ├── UserId.php
│   │   │   ├── UserEmail.php
│   │   │   └── UserRole.php  # Move from Core/Enum
│   │   └── Repository/
│   │       └── UserRepositoryInterface.php
│   └── Shared/               # Shared Domain (cross-domain)
│       ├── ValueObject/
│       │   ├── Email.php
│       │   ├── Uuid.php
│       │   └── DateTimeVO.php
│       └── Exception/
│           ├── DomainException.php
│           ├── EntityNotFoundException.php
│           └── ValidationException.php
│
├── Infrastructure/           # Infrastructure layer (Adapters: Persistence, External APIs, Messaging)
│   ├── Persistence/
│   │   ├── Doctrine/
│   │   │   ├── Repository/
│   │   │   │   ├── AdminRepository.php  # implements Domain\Admin\Repository\AdminRepositoryInterface
│   │   │   │   ├── MemberRepository.php
│   │   │   │   └── UserRepository.php
│   │   │   └── Mapping/  # Optional: if using XML/YAML instead of annotations
│   │   │       ├── Admin.orm.xml
│   │   │       └── Member.orm.xml
│   │   ├── MongoDB/
│   │   │   └── EventStoreRepository.php
│   │   └── Redis/
│   │       └── CacheRepository.php
│   ├── Messaging/
│   │   ├── Handler/
│   │   │   ├── NotifyUserMessageHandler.php
│   │   │   └── UserRegisterMessageHandler.php
│   │   └── Message/
│   │       ├── NotifyUserMessage.php
│   │       └── UserRegisterMessage.php
│   ├── Mail/
│   │   └── SymfonyMailer/
│   │       └── UserRegistrationMailer.php  # implements Domain service or Application port
│   ├── Security/
│   │   ├── JwtAuthenticationEntryPoint.php
│   │   ├── CustomAuthenticationSuccessHandler.php
│   │   └── ApiAccessDeniedHandler.php
│   ├── Event/
│   │   ├── Listener/
│   │   │   └── JWTCreatedListener.php
│   │   └── Subscriber/
│   │       └── ApiExceptionSubscriber.php
│   └── Service/
│       ├── MongoDBService.php
│       └── RedisService.php
│
├── Presentation/             # Presentation layer (Controllers, CLI, GraphQL resolvers)
│   ├── Http/
│   │   ├── Rest/
│   │   │   ├── Admin/
│   │   │   │   ├── AdminAccountController.php
│   │   │   │   ├── AdminAuthController.php
│   │   │   │   └── AdminProfileController.php
│   │   │   ├── Member/
│   │   │   │   ├── MemberAccountController.php
│   │   │   │   ├── MemberAuthController.php
│   │   │   │   └── MemberProfileController.php
│   │   │   └── ApiTestController.php
│   │   └── GraphQL/  # Open to GraphQL
│   │       └── Resolver/
│   ├── Cli/
│   │   └── Command/
│   │       └── SeedDatabaseCommand.php
│   └── Request/
│       ├── Admin/
│       │   └── CreateAdminRequest.php  # Request DTOs for validation
│       └── Member/
│           └── RegisterMemberRequest.php
│
├── DataFixtures/             # Kept at root (Symfony convention)
│   ├── AdminFixtures.php
│   ├── MemberFixtures.php
│   ├── GeoGroupFixture.php
│   └── AppFixtures.php
│
└── Kernel.php

# Final folder structure summary
src/
├── Application/          # Use cases (Commands/Queries + Handlers)
├── Domain/               # Pure business logic (Entities, VOs, Interfaces, Domain Services)
├── Infrastructure/       # Adapters (Doctrine repos, Mailer, Redis, Messaging)
├── Presentation/         # Controllers, CLI
├── DataFixtures/         # Fixtures
└── Kernel.php            # Symfony
```

## Key principles applied

### 1. Hexagonal Architecture (Ports & Adapters)

| Layer | Responsibility | Example |
|-------|----------------|---------|
| **Domain** | Business logic, entities, domain services, repository **interfaces** | `Domain/Member/Entity/Member.php`, `Domain/Member/Repository/MemberRepositoryInterface.php` |
| **Application** | Use cases (commands/queries), orchestration, DTOs | `Application/Member/Command/RegisterMemberCommand.php` + `RegisterMemberHandler.php` |
| **Infrastructure** | Adapters: DB (Doctrine), messaging (Symfony Messenger), external APIs | `Infrastructure/Persistence/Doctrine/Repository/MemberRepository.php` (implements `MemberRepositoryInterface`) |
| **Presentation** | Controllers, CLI commands, API endpoints | `Presentation/Http/Rest/Member/MemberAuthController.php` |

### 2. Dependency direction (SOLID Dependency Inversion)

Presentation → Application → Domain ← Infrastructure

- Domain has zero dependencies on other layers (pure business logic)
- Application depends only on Domain (uses domain entities, calls repository interfaces)
- Infrastructure implements Domain interfaces (e.g., MemberRepository implements MemberRepositoryInterface)
- Presentation calls Application use cases (e.g., controller dispatches RegisterMemberCommand)
<br>

### 3. DDD tactical patterns

- Entities: Rich domain objects with behavior (Member, Admin, FeedPost)
- Value Objects: Immutable, self-validating (MemberId, Email, ActivationCode)
- Aggregates: Member aggregate root contains MemberProfile, MemberActivationCode
- Domain Services: Complex logic that doesn't fit in one entity (MemberRegistrationService)
- Domain Events: MemberRegisteredEvent, AdminLoggedInEvent (dispatched from entities/services)
- Repository Interfaces: Defined in Domain, implemented in Infrastructure
<br><br>

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