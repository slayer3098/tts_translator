<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->text('original_text');
            $table->string('source_language', 10)->default('en');
            $table->string('target_language', 10);
            $table->text('translated_text');
            $table->string('audio_file_path')->nullable();
            $table->string('voice_type', 50)->default('female');
            $table->decimal('pitch', 3, 1)->default(1.0);
            $table->decimal('speed', 3, 1)->default(1.0);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->index(['created_at', 'ip_address']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('translations');
    }
};