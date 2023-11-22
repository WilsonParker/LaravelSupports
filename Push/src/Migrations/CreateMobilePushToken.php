<?php

namespace LaravelSupports\Push\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobilePushToken extends Migration
{
    private $tableName = "mobile_push_token";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('ix')->comment('고유키');
            $table->string('reference', 128)->nullable(false)->default("")->comment('참조할 데이터, 유저, 지점 등의 고유키 또는 고유키들 이 들어갈 수 있다');
            $table->string('token', 255)->nullable(false)->unique()->comment('fcm token');
            $table->string('device_id', 128)->nullable(false)->default("")->comment('디바이스 고유 id');
            $table->char('app_type', 1)->nullable(false)->default("a")->comment('앱 구분 (a:Android|i:IOS)');
            $table->char('agreement', 1)->nullable(false)->default("y")->comment('푸시 수신 동의 여부 (y:n)');
            $table->dateTime('created_at')->nullable(false)->useCurrent()->comment('생성 일');
            $table->dateTime("updated_at")->nullable(false)->useCurrent()->comment("업데이트 일");

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
