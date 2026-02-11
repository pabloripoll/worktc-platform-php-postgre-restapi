<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251001193924 extends AbstractMigration
{
    /** string $table */
    private string $table = 'feed_categories';

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

        $table->addColumn('key', 'string', [
            'length' => 64,
            'notnull' => true,
        ]);

        $table->addColumn('title', 'string', [
            'length' => 64,
            'notnull' => true,
        ]);

        $table->addColumn('visits_count', 'integer', [
            'notnull' => false,
            'default' => 0
        ]);

        $table->addColumn('posts_count', 'integer', [
            'notnull' => false,
            'default' => 0
        ]);

        $table->addColumn('posts_votes_up_count', 'integer', [
            'notnull' => false,
            'default' => 0
        ]);

        $table->addColumn('posts_votes_down_count', 'integer', [
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
        $table->addUniqueIndex(['key'], 'uniq_'.$this->table.'_key');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ' . $this->table);
    }
}
