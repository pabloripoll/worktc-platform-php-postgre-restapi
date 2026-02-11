<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251001224713 extends AbstractMigration
{
    /** string $table */
    private string $table = 'feed_reports';

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

        $table->addColumn('type_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('reporter_user_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('reporter_message', 'string', [
            'length' => 256,
            'notnull' => false,
        ]);

        $table->addColumn('in_review', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('in_review_since', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('is_closed', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('closed_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('moderation_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('member_user_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('member_post_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('created_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('updated_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('posts_report_types', ['type_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('users', ['reporter_user_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('users', ['member_user_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('members_moderations', ['moderation_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('posts', ['member_post_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addIndex(['created_at'], 'idx_'. $this->table .'created_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ' . $this->table);
    }
}
