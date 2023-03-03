<?php

namespace LaravelSupports\Database\Migrations;

use LaravelSupports\Database\Commands\Migrations\AlterMigrateMakeCommand;
use LaravelSupports\Database\Commands\Migrations\CreateMigrateMakeCommand;

class MigrationServiceProvider extends \Illuminate\Database\MigrationServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'CreateMigrateMake' => 'command.create_migrate.make',
        'AlterMigrateMake' => 'command.alter_migrate.make',
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepository();

        $this->registerMigrator();

        $this->registerCreator();

        $this->registerCommands($this->commands);
    }

    protected function registerCreator()
    {
        $this->app->singleton('migration.wp_create_creator', function ($app) {
            return new CreateMigrateCreator($app['files'], $app->basePath('stubs'));
        });
        $this->app->singleton('migration.wp_alter_creator', function ($app) {
            return new AlterMigrateCreator($app['files'], $app->basePath('stubs'));
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerCreateMigrateMakeCommand(): void
    {
        $this->app->singleton('command.create_migrate.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.wp_create_creator'];

            $composer = $app['composer'];

            return new CreateMigrateMakeCommand($creator, $composer);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerAlterMigrateMakeCommand(): void
    {
        $this->app->singleton('command.alter_migrate.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.wp_alter_creator'];

            $composer = $app['composer'];

            return new AlterMigrateMakeCommand($creator, $composer);
        });
    }
}
