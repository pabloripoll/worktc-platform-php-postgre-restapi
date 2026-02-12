<div id="top-header" style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>

# WORKTIME CONTROLLER - SYMFONY 7

- [/README.md](../README.md)
- [Implementación de Desarrollo](./Development.md)

# Propuesta de Estructura Exagonal + DDD

## <a id="hexagonal-structure"></a>Hexagonal/DDD structure

**Proposed structure desing overview:**
```bash
./src/
├── Application # Application layer (Use Cases / Commands / Queries / Handlers)
│   ├── Admin
│   │   ├── Command
│   │   │   ├── IncrementAccessLogRequestCountCommand.php
│   │   │   ├── IncrementAccessLogRequestCountHandler.php
│   │   │   ├── TerminateAccessLogCommand.php
│   │   │   ├── TerminateAccessLogHandler.php
│   │   │   ├── TerminateAllUserAccessLogsCommand.php
│   │   │   ├── TerminateAllUserAccessLogsHandler.php
│   │   │   ├── UpdateAdminProfileCommand.php
│   │   │   └── UpdateAdminProfileHandler.php
│   │   ├── DTO
│   │   │   ├── AdminAccessLogDTO.php
│   │   │   └── AdminProfileDTO.php
│   │   └── Query
│   │       ├── GetAdminAccessLogByIdHandler.php
│   │       ├── GetAdminAccessLogByIdQuery.php
│   │       ├── GetAdminAccessLogByTokenHandler.php
│   │       ├── GetAdminAccessLogByTokenQuery.php
│   │       ├── GetAdminAccessLogsHandler.php
│   │       ├── GetAdminAccessLogsQuery.php
│   │       ├── GetAdminProfileHandler.php
│   │       ├── GetAdminProfileQuery.php
│   │       ├── GetAllAdminAccessLogsHandler.php
│   │       └── GetAllAdminAccessLogsQuery.php
│   ├── Member
│   │   ├── Command
│   │   │   ├── IncrementAccessLogRequestCountCommand.php
│   │   │   ├── IncrementAccessLogRequestCountHandler.php
│   │   │   ├── TerminateAccessLogCommand.php
│   │   │   ├── TerminateAccessLogHandler.php
│   │   │   ├── TerminateAllUserAccessLogsCommand.php
│   │   │   ├── TerminateAllUserAccessLogsHandler.php
│   │   │   ├── UpdateMemberProfileCommand.php
│   │   │   └── UpdateMemberProfileHandler.php
│   │   ├── DTO
│   │   │   ├── MemberAccessLogDTO.php
│   │   │   └── MemberProfileDTO.php
│   │   └── Query
│   │       ├── GetAllMemberAccessLogsHandler.php
│   │       ├── GetAllMemberAccessLogsQuery.php
│   │       ├── GetMemberAccessLogByIdHandler.php
│   │       ├── GetMemberAccessLogByIdQuery.php
│   │       ├── GetMemberAccessLogByTokenHandler.php
│   │       ├── GetMemberAccessLogByTokenQuery.php
│   │       ├── GetMemberAccessLogsHandler.php
│   │       ├── GetMemberAccessLogsQuery.php
│   │       ├── GetMemberProfileHandler.php
│   │       └── GetMemberProfileQuery.php
│   ├── Shared # Shared Domain (cross-domain)
│   │   └── DTO
│   │       ├── PaginatedResultDTO.php
│   │       └── PaginationDTO.php
│   ├── User
│   │   ├── Command
│   │   │   ├── LoginCommand.php
│   │   │   ├── LoginHandler.php
│   │   │   ├── RegisterAdminCommand.php
│   │   │   ├── RegisterAdminHandler.php
│   │   │   ├── RegisterMemberCommand.php
│   │   │   └── RegisterMemberHandler.php
│   │   ├── DTO
│   │   │   └── UserDTO.php
│   │   └── Query
│   │       ├── GetAllUsersHandler.php
│   │       ├── GetAllUsersQuery.php
│   │       ├── GetUserByIdHandler.php
│   │       └── GetUserByIdQuery.php
│   └── WorkEntry
│       ├── Command
│       │   ├── CreateWorkEntryCommand.php
│       │   ├── CreateWorkEntryHandler.php
│       │   ├── DeleteWorkEntryCommand.php
│       │   ├── DeleteWorkEntryHandler.php
│       │   ├── UpdateWorkEntryCommand.php
│       │   └── UpdateWorkEntryHandler.php
│       ├── DTO
│       │   └── WorkEntryDTO.php
│       └── Query
│           ├── GetAllWorkEntriesQuery.php
│           ├── GetWorkEntriesByUserHandler.php
│           ├── GetWorkEntriesByUserQuery.php
│           ├── GetWorkEntryByIdHandler.php
│           └── GetWorkEntryByIdQuery.php
├── DataFixtures # Kept at root (Symfony convention)
│   ├── AppFixtures.php
│   └── UserFixtures.php
├── Domain # Domain layer (Entities, Value Objects, Domain Services, Interfaces)
│   ├── Admin
│   │   ├── Entity
│   │   │   ├── AdminAccessLog.php
│   │   │   └── AdminProfile.php
│   │   ├── Event
│   │   │   ├── AdminCreatedEvent.php
│   │   │   └── AdminLoggedInEvent.php
│   │   ├── Repository # Interface only
│   │   │   ├── AdminAccessLogRepositoryInterface.php
│   │   │   └── AdminProfileRepositoryInterface.php
│   │   ├── Service # Domain logic
│   │   │   └── AdminAuthenticationService.php
│   │   └── ValueObject
│   │       ├── AdminEmail.php
│   │       └── AdminId.php
│   ├── Member
│   │   ├── Entity
│   │   │   ├── MemberAccessLog.php
│   │   │   └── MemberProfile.php
│   │   ├── Event
│   │   │   ├── MemberCreatedEvent.php
│   │   │   └── MemberLoggedInEvent.php
│   │   ├── Repository
│   │   │   ├── MemberAccessLogRepositoryInterface.php
│   │   │   └── MemberProfileRepositoryInterface.php
│   │   ├── Service
│   │   │   └── MemberAuthenticationService.php
│   │   └── ValueObject
│   │       ├── MemberEmail.php
│   │       └── MemberId.php
│   ├── Shared
│   │   ├── Exception
│   │   │   ├── DomainException.php
│   │   │   ├── EntityNotFoundException.php
│   │   │   ├── InvalidEmailException.php
│   │   │   ├── InvalidUuidException.php
│   │   │   └── ValidationException.php
│   │   └── ValueObject
│   │       ├── DateTimeVO.php
│   │       ├── Email.php
│   │       └── Uuid.php
│   ├── User
│   │   ├── Entity
│   │   │   └── User.php
│   │   ├── Event
│   │   │   ├── UserCreatedEvent.php
│   │   │   └── UserLoggedInEvent.php
│   │   ├── Repository
│   │   │   └── UserRepositoryInterface.php
│   │   ├── Service
│   │   │   └── UserAuthenticationService.php
│   │   └── ValueObject
│   │       └── UserRole.php
│   └── WorkEntry
│       ├── Entity
│       │   └── WorkEntry.php
│       ├── Event
│       │   ├── WorkEntryCreatedEvent.php
│       │   └── WorkEntryEndedEvent.php
│       ├── Repository
│       │   └── WorkEntryRepositoryInterface.php
│       ├── Service
│       │   └── WorkEntryService.php
│       └── ValueObject
│           └── WorkEntry.php
├── Infrastructure # Infrastructure layer (Adapters: Persistence, External APIs, Messaging)
│   ├── Event
│   │   ├── Listener
│   │   │   └── JWTCreatedListener.php
│   │   └── Subscriber
│   │       ├── ApiExceptionSubscriber.php
│   │       └── DomainExceptionSubscriber.php
│   ├── Mail # implements Domain service or Application port
│   │   └── UserRegisterMail.php
│   ├── Messaging
│   │   ├── Handler
│   │   │   └── NotifyUserMessageHandler.php
│   │   └── Message
│   │       └── NotifyUserMessage.php
│   ├── Persistence
│   │   ├── Doctrine
│   │   │   ├── Mapping # Optional: if using XML/YAML instead of annotations
│   │   │   └── Repository
│   │   │       ├── AdminAccessLogRepository.php
│   │   │       ├── AdminProfileRepository.php
│   │   │       ├── MemberAccessLogRepository.php
│   │   │       ├── MemberProfileRepository.php
│   │   │       ├── UserRepository.php
│   │   │       └── WorkEntryRepository.php
│   │   ├── MongoDB
│   │   └── Redis
│   ├── Security
│   │   ├── ApiAccessDeniedHandler.php
│   │   ├── CustomAuthenticationSuccessHandler.php
│   │   └── JwtAuthenticationEntryPoint.php
│   └── Service
│       ├── MongoDBService.php
│       └── RedisService.php
├── Kernel.php
└── Presentation # Presentation layer (Controllers, CLI, GraphQL resolvers)
    ├── Cli
    │   └── Command
    ├── Http
    │   ├── GraphQl # Open to GraphQL
    │   │   └── Resolver
    │   └── Rest
    │       ├── Admin
    │       │   ├── AdminAuthController.php
    │       │   ├── AdminMembersController.php
    │       │   ├── AdminProfileController.php
    │       │   └── AdminUsersController.php
    │       ├── Member
    │       │   ├── MemberAuthController.php
    │       │   ├── MemberClockingController.php
    │       │   └── MemberProfileController.php
    │       ├── ServicesTestController.php
    │       ├── User
    │       │   └── UserAuthController.php
    │       └── WorkEntry
    └── Request
        ├── Admin
        │   ├── CreateAdminRequest.php # Request DTOs for validation
        │   ├── CreateMemberRequest.php
        │   ├── UpdateAdminProfileRequest.php
        │   └── UpdateMemberProfileRequest.php
        ├── BaseRequest.php
        ├── Member
        │   └── UpdateMemberProfileRequest.php
        ├── ValidatableRequestTrait.php
        └── WorkEntry
            ├── CreateWorkEntryRequest.php
            └── UpdateWorkEntryRequest.php


# Final folder structure summary
src/
├── Application/          # Use cases (Commands/Queries + Handlers)
├── Domain/               # Pure business logic (Entities, VOs, Interfaces, Domain Services)
├── Infrastructure/       # Adapters (Doctrine repos, Mailer, Redis, Messaging)
├── Presentation/         # Controllers, CLI
├── DataFixtures/         # Fixtures
└── Kernel.php            # Symfony
```

<!-- FOOTER -->
<br>

---

<br>

- [GO TOP ⮙](#top-header)

<div style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>