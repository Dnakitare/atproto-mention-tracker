<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tracked_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('keyword')->index();
            $table->enum('type', ['username', 'hashtag', 'text'])->default('text');
            $table->boolean('is_active')->default(true);
            $table->json('notification_settings')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'keyword', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracked_keywords');
    }
};
