<?php


namespace LaravelSupports\Database\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Throwable;

abstract class CreateMigration extends BaseMigration
{
    /**
     * @throws \Throwable
     */
    public function up(): void
    {
        try {
            Schema::create($this->getTable(), function (Blueprint $table) {
                $this->defaultUpTemplate($table);
                $this->defaultTimestampTemplate($table);
                $this->defaultSet($table);
            });
        } catch (Throwable $t) {
            $this->down();
            throw $t;
        }
    }

    /**
     * Run the migrations.
     *
     * @param Blueprint $table
     * @return void
     */
    abstract protected function defaultUpTemplate(Blueprint $table): void;
}
