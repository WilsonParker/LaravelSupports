<?php

namespace LaravelSupports\Push\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 푸시 결과 테이블
 *
 * @author  WilsonParker
 * @added   2019-09-05
 * @updated 2019-09-05
 */
class CreateMobilePushResultLog extends Migration
{

    private $tableName = "mobile_push_result_log";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('ix')->comment('고유키');
            $table->string('push_title', 128)->nullable(false)->default("")->comment('푸쉬 제목');
            $table->string('push_contents', 255)->nullable(false)->default("")->comment('푸시 내용');
            $table->string('push_type', 10)->nullable(false)->default("txt")->comment('이미지/텍스트/ALL (txt|img|noti_img)');
            $table->string('push_link', 255)->nullable(false)->default("")->comment('푸시 링크');
            $table->string('push_replace_text', 128)->nullable(true)->default("")->comment('대체 텍스트');
            $table->string('imageUrl', 255)->nullable(true)->default("")->comment('이미지 URL');
            $table->string('push_result', 128)->nullable(true)->default("")->comment('전송 결과');
            $table->char('app_type', 1)->nullable(false)->default("a")->comment('앱 구분 (a:Android|i:IOS)');
            $table->dateTime('created_at')->nullable(false)->useCurrent()->comment('생성 일');

            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
