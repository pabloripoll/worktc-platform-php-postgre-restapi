<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251001193617 extends AbstractMigration
{
    /** string $table */
    private string $table = 'members_access_logs';

    public function getDescription(): string
    {
        return 'Create '. $this->table .' table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable($this->table);

        $table->addColumn('id', 'bigint', [
            'autoincrement' => true,
            'notnull' => true,
        ]);

        $table->addColumn('user_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('is_terminated', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('is_expired', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('expires_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('refresh_count', 'integer', [
            'default' => 0
        ]);

        $table->addColumn('created_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('updated_at', 'datetime', [
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
            'default' => 0
        ]);

        $table->addColumn('payload', 'json', [
            'notnull' => false,
        ]);

        $table->addColumn('token', 'text', [
            'notnull' => true,
        ]);

        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addIndex(['expires_at'], 'idx_'. $this->table .'expires_at');
        $table->addIndex(['token'], 'idx_'. $this->table .'token');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ' . $this->table);
    }
}
