<?php


namespace LaravelSupports\Libraries\Supports\Databases\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// php artisan migrate --path='./database/migrations/2020_04_26_033358_create_member_overdue_information.php'
abstract class BaseMigration extends Migration
{
    protected string $table = "";

    protected function defaultSet(Blueprint $table)
    {
        $table->engine = 'InnoDB';
        $table->charset = 'utf8';
        $table->collation = 'utf8_unicode_ci';
    }

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $this->defaultCreateTemplate($table);
            $this->defaultSet($table);
        });
    }

    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            if (Schema::hasTable($this->table)) {
                $this->defaultDropTemplate($table);
            }
        });
    }

    /**
     * Run the migrations.
     * @param Blueprint $table
     * @return void
     */
    function defaultCreateTemplate(Blueprint $table)
    {

    }

    /**
     * Reverse the migrations.
     * @param Blueprint $table
     * @return void
     */
    function defaultDropTemplate(Blueprint $table)
    {
        $table->drop();
    }
}
