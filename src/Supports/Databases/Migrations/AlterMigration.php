<?php


namespace LaravelSupports\Supports\Databases\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class AlterMigration extends BaseMigration
{
    protected bool $needDrop = false;

    public function up()
    {
        try {
            Schema::table($this->table, function (Blueprint $table) {
                $this->defaultUpTemplate($table);
            });
        } catch (\Throwable $t) {
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
