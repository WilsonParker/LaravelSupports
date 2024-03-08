<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('open_ai_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key', 128)->nullable(false)->comment('api key');
            $table->string('description', 128)->nullable(true)->comment('설명');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('open_ai_keys', function (Blueprint $table) {
            $table->dropIfExists();
        });
    }
};
