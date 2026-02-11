<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260211155500 extends AbstractMigration
{
    private string $table = 'member_access_logs';

    public function getDescription(): string
    {
        return 'Create ' . $this->table . ' table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable($this->table);

        $table->addColumn('id', 'bigint', [
            'autoincrement' => true,
            'notnull' => true,
        ]);

        $table->addColumn('user_id', 'uuid', [
            'notnull' => true,
        ]);

        $table->addColumn('is_terminated', 'boolean', [
            'default' => false,
            'notnull' => true,
        ]);

        $table->addColumn('is_expired', 'boolean', [
            'default' => false,
            'notnull' => true,
        ]);

        $table->addColumn('expires_at', 'datetime_immutable', [
            'notnull' => true,
        ]);

        $table->addColumn('refresh_count', 'integer', [
            'default' => 0,
            'notnull' => true,
        ]);

        $table->addColumn('created_at', 'datetime_immutable', [
            'notnull' => true,
        ]);

        $table->addColumn('updated_at', 'datetime_immutable', [
            'notnull' => true,
        ]);

        $table->addColumn('ip_address', 'string', [
            'length' => 45,
            'notnull' => false,
        ]);

        $table->addColumn('user_agent', 'text', [
            'notnull' => false,
        ]);

        $table->addColumn('requests_count', 'integer', [
            'default' => 0,
            'notnull' => true,
        ]);

        $table->addColumn('payload', 'json', [
            'notnull' => false,
        ]);

        $table->addColumn('token', 'text', [
            'notnull' => true,
        ]);

        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint(
            'users',
            ['user_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'fk_' . $this->table . '_user'
        );
        $table->addIndex(['user_id'], 'idx_' . $this->table . '_user');
        $table->addIndex(['expires_at'], 'idx_' . $this->table . '_expires_at');
        $table->addIndex(['token'], 'idx_' . $this->table . '_token');
        $table->addIndex(['created_at'], 'idx_' . $this->table . '_created_at');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable($this->table);
    }
}
