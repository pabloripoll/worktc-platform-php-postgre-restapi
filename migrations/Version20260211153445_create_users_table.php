<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260211153445 extends AbstractMigration
{
    private string $table = 'users';

    public function getDescription(): string
    {
        return 'Create ' . $this->table . ' table with Single Table Inheritance';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable($this->table);

        $table->addColumn('id', 'uuid', [
            'notnull' => true,
            'comment' => 'UUIDv7',
        ]);

        $table->addColumn('role', 'string', [
            'length' => 50,
            'notnull' => true,
            'comment' => 'Discriminator: ROLE_ADMIN or ROLE_MEMBER',
        ]);

        $table->addColumn('email', 'string', [
            'length' => 64,
            'notnull' => true,
        ]);

        $table->addColumn('password', 'string', [
            'length' => 256,
            'notnull' => true,
        ]);

        $table->addColumn('created_at', 'datetime_immutable', [
            'notnull' => true,
        ]);

        $table->addColumn('updated_at', 'datetime_immutable', [
            'notnull' => true,
        ]);

        $table->addColumn('deleted_at', 'datetime_immutable', [
            'notnull' => false,
        ]);

        $table->addColumn('created_by_user_id', 'uuid', [
            'notnull' => true,
        ]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['email'], 'uniq_' . $this->table . '_email');
        $table->addIndex(['role'], 'idx_' . $this->table . '_role');
        $table->addIndex(['deleted_at'], 'idx_' . $this->table . '_deleted_at');

        // Self-referencing FK for createdByUserId
        $table->addForeignKeyConstraint(
            $this->table,
            ['created_by_user_id'],
            ['id'],
            ['onDelete' => 'RESTRICT'],
            'fk_' . $this->table . '_created_by_user'
        );
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable($this->table);
    }
}
