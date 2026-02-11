<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260211160000 extends AbstractMigration
{
    private string $table = 'work_entries';

    public function getDescription(): string
    {
        return 'Create ' . $this->table . ' table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable($this->table);

        $table->addColumn('id', 'uuid', [
            'notnull' => true,
        ]);

        $table->addColumn('user_id', 'uuid', [
            'notnull' => true,
            'comment' => 'No FK constraint for loose coupling',
        ]);

        $table->addColumn('start_date', 'datetime_immutable', [
            'notnull' => true,
        ]);

        $table->addColumn('end_date', 'datetime_immutable', [
            'notnull' => false,
        ]);

        $table->addColumn('created_at', 'datetime_immutable', [
            'notnull' => true,
        ]);

        $table->addColumn('updated_at', 'datetime_immutable', [
            'notnull' => true,
        ]);

        $table->addColumn('deleted_at', 'datetime_immutable', [
            'notnull' => false,
        ]);

        $table->addColumn('created_by_user_id', 'uuid', [
            'notnull' => false,
        ]);

        $table->addColumn('updated_by_user_id', 'uuid', [
            'notnull' => false,
        ]);

        $table->setPrimaryKey(['id']);
        // Note: NO FK constraint on user_id (loose coupling as per requirement)
        $table->addIndex(['user_id'], 'idx_' . $this->table . '_user');
        $table->addIndex(['start_date'], 'idx_' . $this->table . '_start_date');
        $table->addIndex(['deleted_at'], 'idx_' . $this->table . '_deleted_at');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable($this->table);
    }
}
