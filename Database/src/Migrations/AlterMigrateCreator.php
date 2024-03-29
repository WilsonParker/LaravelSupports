<?php

namespace LaravelSupports\Database\Migrations;

use Illuminate\Database\Migrations\MigrationCreator;

class AlterMigrateCreator extends MigrationCreator
{

    /**
     * Get the migration stub file.
     *
     * @param string|null $table
     * @param bool $create
     * @return string
     */
    protected function getStub($table, $create)
    {
        if (is_null($table)) {
            $stub = $this->files->exists($customPath = $this->customStubPath . '/migration.stub')
                ? $customPath
                : $this->stubPath() . '/migration.stub';
        } else {
            $stub = $this->files->exists($customPath = $this->customStubPath . '/migration.update.stub')
                ? $customPath
                : $this->stubPath() . '/migration.update.stub';
        }

        return $this->files->get($stub);
    }

    public function stubPath(): string
    {
        return __DIR__ . '/stubs';
    }
}
