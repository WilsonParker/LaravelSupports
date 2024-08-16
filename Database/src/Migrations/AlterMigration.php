<?php


namespace LaravelSupports\Database\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Throwable;

abstract class AlterMigration extends BaseMigration
{
    protected bool $needDrop = false;

    public function up(): void
    {
        try {
            Schema::table($this->getTable(), function (Blueprint $table) {
                $this->defaultUpTemplate($table);
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
