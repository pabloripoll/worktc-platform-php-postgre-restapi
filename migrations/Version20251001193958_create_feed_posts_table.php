<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251001193958 extends AbstractMigration
{
    /** string $table */
    private string $table = 'feed_posts';

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

        $table->addColumn('uid', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('user_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('region_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('category_id', 'bigint', [
            'notnull' => true
        ]);

        $table->addColumn('is_active', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('is_draft', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('is_banned', 'boolean', [
            'default' => false
        ]);

        $table->addColumn('visits_count', 'integer', [
            'notnull' => false,
            'default' => 0
        ]);

        $table->addColumn('reports_count', 'integer', [
            'notnull' => false,
            'default' => 0
        ]);

        $table->addColumn('votes_up_count', 'integer', [
            'notnull' => false,
            'default' => 0
        ]);

        $table->addColumn('votes_down_count', 'integer', [
            'notnull' => false,
            'default' => 0
        ]);

        $table->addColumn('title', 'string', [
            'length' => 128,
            'notnull' => false,
        ]);

        $table->addColumn('slug', 'string', [
            'length' => 128,
            'notnull' => false,
        ]);

        $table->addColumn('summary', 'string', [
            'length' => 256,
            'notnull' => false,
        ]);

        $table->addColumn('article', 'text', [
            'notnull' => false,
        ]);

        $table->addColumn('created_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('updated_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['uid'], 'uniq_'.$this->table.'_uid');
        $table->addForeignKeyConstraint('users', ['user_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('geo_regions', ['region_id'], ['id'], ['onDelete' => 'CASCADE']);
        $table->addForeignKeyConstraint('posts_categories', ['category_id'], ['id'], ['onDelete' => 'CASCADE']);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ' . $this->table);
    }
}
