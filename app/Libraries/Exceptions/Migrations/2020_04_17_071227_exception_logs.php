<?php

use Illuminate\Database\Schema\Blueprint;
use LaravelSupports\Libraries\Supports\Databases\Migrations\BaseMigration;

class ExceptionLogs extends BaseMigration
{

    protected $table = "exception_logs";

    /**
     * Run the migrations.
     * @param Blueprint $table
     * @return void
     */
    function defaultCreateTemplate(Blueprint $table)
    {
        $table->integerIncrements('ix')->comment('고유키');
        $table->string('code')->nullable(false)->comment('에러 코드');
        $table->text('message')->nullable(false)->comment('에러 메시지');
        $table->string('url')->nullable(false)->comment('에러가 발생한 주소');
        $table->string('file')->nullable(false)->comment('에러가 발생한 파일 이름');
        $table->string('class')->nullable(false)->comment('에러가 발생한 클래스');
        $table->text('trace')->nullable(false)->comment('에러 내용');

        $table->timestamp('created_at')->useCurrent();
        $table->timestamp('updated_at')->useCurrent();
    }


}
