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
        Schema::table('alerts', function (Blueprint $table) {
            // Add index for user_id and is_active for faster filtering of active alerts
            $table->index(['user_id', 'is_active'], 'alerts_user_active_index');
            
            // Add index for type for faster filtering by alert type
            $table->index('type', 'alerts_type_index');
            
            // Add index for last_triggered_at for faster frequency checks
            $table->index('last_triggered_at', 'alerts_last_triggered_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropIndex('alerts_user_active_index');
            $table->dropIndex('alerts_type_index');
            $table->dropIndex('alerts_last_triggered_index');
        });
    }
}; 