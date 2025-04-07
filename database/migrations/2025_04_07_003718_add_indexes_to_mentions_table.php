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
        Schema::table('mentions', function (Blueprint $table) {
            // Add index for user_id and created_at for faster filtering by user and time window
            $table->index(['user_id', 'created_at'], 'mentions_user_created_index');
            
            // Add index for text column for faster keyword searches
            $table->index('text', 'mentions_text_index');
            
            // Add index for sentiment for faster sentiment-based queries
            $table->index('sentiment', 'mentions_sentiment_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentions', function (Blueprint $table) {
            $table->dropIndex('mentions_user_created_index');
            $table->dropIndex('mentions_text_index');
            $table->dropIndex('mentions_sentiment_index');
        });
    }
}; 