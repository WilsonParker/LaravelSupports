<?php

namespace LaravelSupports\Database\Migrations;

use Illuminate\Database\Migrations\MigrationCreator;

class CreateMigrateCreator extends MigrationCreator
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
        } else if ($create) {
            $stub = $this->files->exists($customPath = $this->customStubPath . '/migration.create.stub')
                ? $customPath
                : $this->stubPath() . '/migration.create.stub';
        }

        return $this->files->get($stub);
    }

    public function stubPath(): string
    {
        return __DIR__ . '/stubs';
    }
}
