<?php

namespace App\Library\LaravelSupports\app\Database\Migrations;

use App\Library\LaravelSupports\app\Database\Commands\Migrations\AlterMigrateMakeCommand;
use App\Library\LaravelSupports\app\Database\Commands\Migrations\CreateMigrateMakeCommand;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Migrations\InstallCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\ServiceProvider;

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
    protected function registerCreateMigrateMakeCommand()
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
    protected function registerAlterMigrateMakeCommand()
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
