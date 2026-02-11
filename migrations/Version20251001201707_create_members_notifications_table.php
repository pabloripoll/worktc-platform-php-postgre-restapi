<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251001201707 extends AbstractMigration
{
    /** string $table */
    private string $table = 'members_notifications';

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

        $table->addColumn('notification_type_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('user_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('is_opened', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('opened_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('created_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('updated_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('message', 'string', [
            'length' => 512,
            'notnull' => true,
        ]);

        $table->addColumn('last_member_user_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('last_member_nickname', 'string', [
            'length' => 32,
            'notnull' => true,
        ]);

        $table->addColumn('last_member_avatar', 'text', [
            'notnull' => false,
        ]);

        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('members_notifications', ['notification_type_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('users', ['last_member_user_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addIndex(['opened_at'], 'idx_'. $this->table .'opened_at');
        $table->addIndex(['created_at'], 'idx_'. $this->table .'created_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ' . $this->table);
    }
}
