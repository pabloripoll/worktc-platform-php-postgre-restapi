<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251001202129 extends AbstractMigration
{
    /** string $table */
    private string $table = 'members_moderations';

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

        $table->addColumn('admin_user_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('type_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('is_applied', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('expires_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('is_on_member', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('is_on_feed_post', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('member_user_id', 'bigint', [
            'notnull' => false
        ]);

        $table->addColumn('member_feed_post_id', 'bigint', [
            'notnull' => false
        ]);

        $table->addColumn('created_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('updated_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('users', ['admin_user_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('users', ['member_user_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('feed_posts', ['member_feed_post_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addIndex(['expires_at'], 'idx_'. $this->table .'expires_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ' . $this->table);
    }
}
