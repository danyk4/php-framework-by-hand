<?php

namespace danyk\Framework\Console\Commands;

use danyk\Framework\Console\CommandInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

class MigrateCommand implements CommandInterface
{

    private const MIGRATIONS_TABLE = 'migrations';
    private string $name = 'migrate';

    public function __construct(
        private Connection $connection,
        private string $migrationsPath
    ) {
    }

    public function execute(array $parameters = []): int
    {
        try {
            // 1. Make migrations table, if it not exists

            $this->createMigrationsTable();

            $this->connection->beginTransaction();

            // 2. Get $appliedMigrations - existing migrations

            $appliedMigrations = $this->getAppliedMigrations();

            // 3. Get $migrationFiles from migration folder

            $migrationFiles = $this->getMigrationsFiles();

            // 4. Get migrations

            $migrationsToApply = array_values(array_diff($migrationFiles, $appliedMigrations));

            $schema = new Schema();

            foreach ($migrationsToApply as $migration) {
                $migrationInstance = require $this->migrationsPath."/$migration";

                // 5. Make Sql query for new migrations
                $migrationInstance->up($schema);

                // 6. Add migrations to db
                $this->addMigration($migration);
            }

            $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());


            // 7. Execute Sql query
            foreach ($sqlArray as $sql) {
                $this->connection->executeQuery($sql);
            }


            $this->connection->commit();
        } catch (\Throwable $e) {
            $this->connection->rollBack();

            throw $e;
        }

        return 0;
    }

    private function createMigrationsTable(): void
    {
        $schemaManager = $this->connection->createSchemaManager();

        if ( ! $schemaManager->tableExists(self::MIGRATIONS_TABLE)) {
            $schema = new Schema();

            $table = $schema->createTable(self::MIGRATIONS_TABLE);
            $table->addColumn('id', Types::INTEGER, [
                'unsigned'      => true,
                'autoincrement' => true,
            ]);
            $table->addColumn('migration', Types::TEXT);
            $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, [
                'default' => 'CURRENT_TIMESTAMP',
            ]);
            $table->setPrimaryKey(['id']);

            $sqlArray = $schema->toSql($this->connection->getDatabasePlatform());

            $this->connection->executeQuery($sqlArray[0]);

            echo 'Migrating table created!'.PHP_EOL;
        }
    }

    private function getAppliedMigrations(): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder
            ->select('migration')
            ->from(self::MIGRATIONS_TABLE)
            ->executeQuery()
            ->fetchFirstColumn();
    }

    private function getMigrationsFiles(): array
    {
        $migrationFiles = scandir($this->migrationsPath);

        $filteredFiles = array_filter($migrationFiles, function ($fileName) {
            return ! in_array($fileName, ['.', '..']);
        });

        return array_values($filteredFiles);
    }

    private function addMigration(string $migration): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->insert(self::MIGRATIONS_TABLE)
                     ->values(['migration' => ':migration'])
                     ->setParameter('migration', $migration)
                     ->executeQuery();
    }
}
