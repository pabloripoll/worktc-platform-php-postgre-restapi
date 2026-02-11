<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251001225411 extends AbstractMigration
{
    /** string $table */
    private string $table = 'feed_posts_votes';

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

        $table->addColumn('post_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('up', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('down', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('refresh_count', 'integer', [
            'notnull' => false,
            'default' => 0
        ]);

        $table->addColumn('created_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('updated_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('posts', ['post_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addIndex(['created_at'], 'idx_'. $this->table .'created_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ' . $this->table);
    }
}
