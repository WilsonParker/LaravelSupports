<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RecipeP\Models\OpenAI\OpenAiKey;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('open_ai_key_stacks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OpenAiKey::class)
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->onDelete('no action');
            $table->unsignedInteger('call')->comment('호출 횟수');
            $table->date('date')->comment('날짜');
            $table->timestamps();
            $table->unique(['open_ai_key_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('open_ai_key_stacks', function (Blueprint $table) {
            $table->dropForeignIdFor(OpenAiKey::class);
            $table->dropIfExists();
        });
    }
};
