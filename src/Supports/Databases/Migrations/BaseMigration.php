<?php


namespace LaravelSupports\Supports\Databases\Migrations;

use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ForeignKeyDefinition;
use Illuminate\Support\Facades\Schema;

// php artisan migrate --path='./database/migrations/2020_04_26_033358_create_member_overdue_information.php'
abstract class BaseMigration extends Migration
{
    use HasRelationships;

    protected string $table;
    protected bool $needDrop = true;
    protected bool $timestamp = true;
    protected bool $softDelete = true;

    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            if (Schema::hasTable($this->table)) {
                $this->defaultDownTemplate($table);
            }
            if ($this->needDrop) {
                $table->dropIfExists();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @param Blueprint $table
     * @return void
     */
    protected function defaultDownTemplate(Blueprint $table): void
    {
    }

    protected function defaultSet(Blueprint $table): void
    {
//        $table->engine = 'InnoDB';
//        $table->charset = 'utf8mb4';
//        $table->collation = 'utf8mb4_unicode_ci';
//        $table->charset = 'utf8';
//        $table->collation = 'utf8_general_ci';
    }

    protected function defaultTimestampTemplate(Blueprint $table): void
    {
        if ($this->timestamp) {
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        }
        if ($this->softDelete) {
            $table->softDeletes();
        }
    }

    /**
     * Foreign for table where pk is code
     *
     * @param \Illuminate\Database\Schema\Blueprint $blueprint
     * @param array|string $columns
     * @param string $table
     * @param array|string $references
     * @return \Illuminate\Database\Schema\ForeignKeyDefinition
     * @author  WilsonParker
     * @added   2023/02/10
     * @updated 2023/02/10
     */
    protected function foreignCode(
        Blueprint    $blueprint,
        array|string $columns,
        string       $table,
        array|string $references = 'code',
        int          $size = 32,
    ): ForeignKeyDefinition
    {
        /**
         * @var \Illuminate\Database\Eloquent\Model $referTable
         */
        $referTable = new $table;
        $blueprint->string($columns, $size);
        return $blueprint->foreign($columns)->references($references)->on($referTable->getTable());
    }

    /**
     * @param \Illuminate\Database\Schema\Blueprint $blueprint
     * @param string $table
     * @param string $name
     * @return \Illuminate\Database\Schema\ForeignKeyDefinition
     * @author  WilsonParker
     * @added   2023/02/12
     * @updated 2023/02/12
     */
    protected function foreignIdForWithName(
        Blueprint $blueprint,
        string    $table,
        string    $name = ''
    ): ForeignKeyDefinition
    {
        /**
         * @var \Illuminate\Database\Eloquent\Model $model
         */
        $model = new $table;
        return $this->foreignId($blueprint, $model, $model->getForeignKey(), $blueprint->getTable() . '_' . $name . '_fk');
    }

    /**
     * Create a new unsigned big integer (8-byte) column on the table.
     *
     * @param \Illuminate\Database\Schema\Blueprint $blueprint
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $column
     * @param string $index
     * @return \Illuminate\Database\Schema\ForeignKeyDefinition
     */
    protected function foreignId(
        Blueprint $blueprint,
        Model     $model,
        string    $column,
        string    $index
    ): ForeignKeyDefinition
    {
        $blueprint->unsignedBigInteger($column)->nullable(false);
        return $blueprint->foreign($column, $index)->references($model->getKeyName())->on($model->getTable());
    }
}
