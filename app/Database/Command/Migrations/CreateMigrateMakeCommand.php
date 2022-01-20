<?php

namespace App\Library\LaravelSupports\app\Database\Command\Migrations;

use App\Library\LaravelSupports\app\Database\Migrations\CreateMigrateCreator;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Support\Composer;

class CreateMigrateMakeCommand extends MigrateMakeCommand
{

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:create_migration {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new create migration file';

    /**
     * The migration creator instance.
     *
     * @var \App\Library\LaravelSupports\app\Database\Migrations\CreateMigrateCreator
     */
    protected $creator;

    /**
     * Create a new migration install command instance.
     *
     * @param \App\Library\LaravelSupports\app\Database\Migrations\CreateMigrateCreator $creator
     * @param \Illuminate\Support\Composer $composer
     * @return void
     */
    public function __construct(CreateMigrateCreator $creator, Composer $composer)
    {
        parent::__construct($creator, $composer);
    }


}
