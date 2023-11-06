<?php


namespace LaravelSupports\Database\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class CreateMigration extends BaseMigration
{
    protected string $table;

    /**
     * @throws \Throwable
     */
    public function up()
    {
        try {
            Schema::create($this->table, function (Blueprint $table) {
                $this->defaultUpTemplate($table);
                $this->defaultTimestampTemplate($table);
                $this->defaultSet($table);
            });
        } catch (\Throwable $t) {
            $this->down();
            throw $t;
        }
    }

    /**
     * Run the migrations.
     * @param Blueprint $table
     * @return void
     */
    abstract protected function defaultUpTemplate(Blueprint $table);
}
