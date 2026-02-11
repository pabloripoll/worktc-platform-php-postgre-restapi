<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260211154000 extends AbstractMigration
{
    private string $table = 'admin_profiles';

    public function getDescription(): string
    {
        return 'Create ' . $this->table . ' table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable($this->table);

        $table->addColumn('user_id', 'uuid', [
            'notnull' => true,
        ]);

        $table->addColumn('name', 'string', [
            'length' => 64,
            'notnull' => true,
        ]);

        $table->addColumn('surname', 'string', [
            'length' => 64,
            'notnull' => true,
        ]);

        $table->addColumn('birth_date', 'date_immutable', [
            'notnull' => false,
        ]);

        $table->addColumn('phone_number', 'string', [
            'length' => 32,
            'notnull' => false,
        ]);

        $table->addColumn('department', 'string', [
            'length' => 64,
            'notnull' => false,
        ]);

        $table->setPrimaryKey(['user_id']);
        $table->addForeignKeyConstraint(
            'users',
            ['user_id'],
            ['id'],
            ['onDelete' => 'CASCADE'],
            'fk_' . $this->table . '_user'
        );
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable($this->table);
    }
}
