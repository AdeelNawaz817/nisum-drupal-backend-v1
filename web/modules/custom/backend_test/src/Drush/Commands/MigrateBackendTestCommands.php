<?php

namespace Drupal\backend_test\Drush\Commands;

use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\MigrateMessage;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile for backend_test.
 */
final class MigrateBackendTestCommands extends DrushCommands {

  /**
   * Runs the specified migration.
   *
   * @param string $migration_id
   *   The ID of the migration to run.
   *
   * @command backend_test:run-migration
   * @aliases btmigrate
   * @usage backend_test:run-migration backend_test_users
   *   Runs the migration with the ID 'backend_test_users'.
   */
  #[CLI\Command(name: 'backend_test:run-migration', aliases: ['btmigrate'])]
  #[CLI\Argument(name: 'migration_id', description: 'The ID of the migration to run.')]
  #[CLI\Usage(name: 'backend_test:run-migration backend_test_users', description: 'Runs the migration for importing users from JSON.')]
  public function runMigration($migration_id) {
    // Load the migration.
    $migration = \Drupal::service('plugin.manager.migration')->createInstance($migration_id);

    if (!$migration) {
      $this->logger()->error(dt('Migration with ID @id was not found.', ['@id' => $migration_id]));
      return;
    }

    $migrateMessage = new MigrateMessage();
    $migrateExecutable = new MigrateExecutable($migration, $migrateMessage);

    // Execute the migration.
    try {
      $migrateExecutable->import();
      $this->logger()->success(dt('Migration @id completed successfully.', ['@id' => $migration_id]));
    }
    catch (\Exception $e) {
      $this->logger()->error(dt('Migration @id failed with error: @error', [
        '@id' => $migration_id,
        '@error' => $e->getMessage(),
      ]));
    }
  }

}
