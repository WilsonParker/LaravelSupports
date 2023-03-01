<?php

namespace LaravelSupports\Database\Commands\Migrations;

use LaravelSupports\Database\Migrations\AlterMigrateCreator;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Support\Composer;

class AlterMigrateMakeCommand extends MigrateMakeCommand
{

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:alter_migration {name : The name of the migration}
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
    protected $description = 'Create a new alter migration file';

    /**
     * The migration creator instance.
     *
     * @var \LaravelSupports\Database\Migrations\AlterMigrateCreator
     */
    protected $creator;

    /**
     * Create a new migration install command instance.
     *
     * @param \LaravelSupports\Database\Migrations\AlterMigrateCreator $creator
     * @param \Illuminate\Support\Composer $composer
     * @return void
     */
    public function __construct(AlterMigrateCreator $creator, Composer $composer)
    {
        parent::__construct($creator, $composer);
    }


}
