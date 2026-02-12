<div id="top-header" style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>

# WORKTIME CONTROLLER - SYMFONY 7

- [/README.md](../README.md)
- [Implementación de Desarrollo](./Development.md)

# Esquema de Entidades

### DB Schema

```bash
User (Single Table Inheritance)
├── id (UUID, PK)
├── role (discriminator: ROLE_ADMIN | ROLE_MEMBER)
├── [UC] email (varchar(64), UNIQUE, NOT NULL)
├── password (varchar(256), NOT NULL)
├── createdAt (timestamp)
├── updatedAt (timestamp)
├── deletedAt (timestamp)
└── createdByUserId (UUID, FK → users.id) (by itself or another user)

AdminProfile
├── userId (UUID, PK, FK → users.id)
├── name (varchar(64), UNIQUE, NOT NULL)
├── surname (varchar(64), UNIQUE, NOT NULL)
├── birthDate (timestamp)
├── phoneNumber (varchar(32), UNIQUE, NOT NULL)
└── department (varchar(64), UNIQUE, NOT NULL)

AdminAccessLog
├── [PK] id (bigint, PK, NOT NULL) [auto-increment]
├── [FK] userId (UUID, PK, FK → users.id)
├── isTerminated (boolean, NOT NULL)
├── isExpired (boolean, NOT NULL)
├── [IN] expires_at (timestamp, NOT NULL)
├── refreshCount (integer, NOT NULL)
├── [IN] created_at (timestamp)
├── updatedAt (timestamp)
├── ipAddress (varchar(45))
├── userAgent (text)
├── requestsCount (integer, NOT NULL)
├── payload (json)
└── [IN] token (text, NOT NULL)

MemberProfile
├── userId (UUID, PK, FK → users.id)
├── name (varchar(64), UNIQUE, NOT NULL)
├── surname (varchar(64), UNIQUE, NOT NULL)
├── birthDate (timestamp)
├── phoneNumber (varchar(32), UNIQUE, NOT NULL)
└── department (varchar(64), UNIQUE, NOT NULL)

MemberAccessLog
├── [PK] id (bigint, PK, NOT NULL) [auto-increment]
├── [FK] userId (UUID, PK, FK → users.id)
├── isTerminated (boolean, NOT NULL)
├── isExpired (boolean, NOT NULL)
├── [IN] expires_at (timestamp, NOT NULL)
├── refreshCount (integer, NOT NULL)
├── [IN] created_at (timestamp)
├── updatedAt (timestamp)
├── ipAddress (varchar(45))
├── userAgent (text)
├── requestsCount (integer, NOT NULL)
├── payload (json)
└── [IN] token (text, NOT NULL)

WorkEntries
├── id (UUID, PK)
├── userId (UUID, FK → users.id, but no DB FK constraint for loose coupling)
├── startDate (timestamp)
├── endDate (timestamp)
├── createdAt (timestamp)
├── updatedAt (timestamp)
├── deletedAt (timestamp)
├── createdByUserId (UUID, FK → users.id)
└── updatedByUserId (UUID, FK → users.id)
```

<!-- FOOTER -->
<br>

---

<br>

- [GO TOP ⮙](#top-header)

<div style="with:100%;height:auto;text-align:right;">
    <img src="../public/files/pr-banner-long.png">
</div>