<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251001193025 extends AbstractMigration
{
    /** string $table */
    private string $table = 'users';

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

        $table->addColumn('role', 'string', [
            'length' => 16,
            'notnull' => true,
        ]);

        $table->addColumn('email', 'string', [
            'length' => 64,
            'notnull' => true,
        ]);

        $table->addColumn('email_verified_at', 'datetime', [
            'notnull' => false,
            'default' => null,
        ]);

        $table->addColumn('password', 'string', [
            'length' => 128,
            'notnull' => true,
        ]);

        $table->addColumn('created_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->addColumn('updated_at', 'datetime', [
            'notnull' => true,
        ]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['email'], 'uniq_'.$this->table.'_email');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ' . $this->table);
    }
}
