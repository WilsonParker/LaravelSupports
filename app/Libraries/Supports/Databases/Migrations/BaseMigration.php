<?php


namespace LaravelSupports\Libraries\Supports\Databases\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// php artisan migrate --path='./database/migrations/2020_04_26_033358_create_member_overdue_information.php'
abstract class BaseMigration extends Migration
{
    protected string $table;
    protected bool $needDrop = true;

    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            if (Schema::hasTable($this->table)) {
                $this->defaultDownTemplate($table);
            }
            if($this->needDrop) {
                $table->dropIfExists();
            }
        });
    }

    protected function defaultSet(Blueprint $table)
    {
//        $table->engine = 'InnoDB';
//        $table->charset = 'utf8mb4';
//        $table->collation = 'utf8mb4_unicode_ci';
//        $table->charset = 'utf8';
//        $table->collation = 'utf8_general_ci';
    }

    protected function defaultTimestampTemplate(Blueprint $table)
    {
        $table->timestamp('created_at')->useCurrent();
        $table->timestamp('updated_at')->useCurrent();
        $table->softDeletes();
    }

    /**
     * Reverse the migrations.
     *
     * @param Blueprint $table
     * @return void
     */
    protected function defaultDownTemplate(Blueprint $table)
    {

    }

}
