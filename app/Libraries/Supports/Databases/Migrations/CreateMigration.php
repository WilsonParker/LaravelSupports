<?php


namespace LaravelSupports\Libraries\Supports\Databases\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class CreateMigration extends BaseMigration
{
    protected string $table;

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $this->defaultUpTemplate($table);
            $this->defaultTimestampTemplate($table);
            $this->defaultSet($table);
        });
    }

    /**
     * Run the migrations.
     * @param Blueprint $table
     * @return void
     */
    abstract function defaultUpTemplate(Blueprint $table);

}
