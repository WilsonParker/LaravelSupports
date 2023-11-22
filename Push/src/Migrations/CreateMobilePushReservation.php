<?php

namespace LaravelSupports\Push\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 푸시 예약 테이블
 *
 * @author  WilsonParker
 * @added   2019-09-05
 * @updated 2019-09-05
 */
class CreateMobilePushReservation extends Migration
{
    private $tableName = "mobile_push_reservation";

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
            $table->char('push_send', 1)->nullable(false)->default("n")->comment('푸시 발송 여부 (r:에약|y:완료|n:실패)');
            $table->dateTime('push_reservation_time')->nullable(false)->comment('예약 시간');
            $table->dateTime('created_at')->nullable(false)->useCurrent()->comment('생성 일');
            $table->dateTime("updated_at")->nullable(false)->useCurrent()->comment("업데이트 일");

            $table->index(["push_reserve_time", "push_title"]);
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
