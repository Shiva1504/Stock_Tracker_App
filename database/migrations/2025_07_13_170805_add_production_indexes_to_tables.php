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
        // Add indexes to stock table for better performance
        Schema::table('stock', function (Blueprint $table) {
            $table->index('product_id', 'idx_stock_product_id');
            $table->index('retailer_id', 'idx_stock_retailer_id');
            $table->index('in_stock', 'idx_stock_in_stock');
            $table->index(['product_id', 'in_stock'], 'idx_stock_product_in_stock');
            $table->index(['retailer_id', 'in_stock'], 'idx_stock_retailer_in_stock');
            $table->index('updated_at', 'idx_stock_updated_at');
        });

        // Add indexes to price_alerts table
        Schema::table('price_alerts', function (Blueprint $table) {
            $table->index('product_id', 'idx_price_alerts_product_id');
            $table->index('is_active', 'idx_price_alerts_is_active');
            $table->index(['product_id', 'is_active'], 'idx_price_alerts_product_active');
            $table->index('target_price', 'idx_price_alerts_target_price');
            $table->index('last_triggered_at', 'idx_price_alerts_last_triggered');
        });

        // Add indexes to stock_history table
        Schema::table('stock_history', function (Blueprint $table) {
            $table->index('stock_id', 'idx_stock_history_stock_id');
            $table->index('created_at', 'idx_stock_history_created_at');
            $table->index(['stock_id', 'created_at'], 'idx_stock_history_stock_created');
        });

        // Add indexes to activity_log table
        Schema::table('activity_log', function (Blueprint $table) {
            $table->index('user_id', 'idx_activity_log_user_id');
            $table->index('subject_type', 'idx_activity_log_subject_type');
            $table->index('action', 'idx_activity_log_action');
            $table->index('created_at', 'idx_activity_log_created_at');
            $table->index(['subject_type', 'subject_id'], 'idx_activity_log_subject');
        });

        // Add indexes to notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('notifiable_type', 'idx_notifications_notifiable_type');
            $table->index('notifiable_id', 'idx_notifications_notifiable_id');
            $table->index('read_at', 'idx_notifications_read_at');
            $table->index(['notifiable_type', 'notifiable_id'], 'idx_notifications_notifiable');
            $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'idx_notifications_notifiable_read');
        });

        // Add indexes to products table
        Schema::table('products', function (Blueprint $table) {
            $table->index('name', 'idx_products_name');
            $table->index('created_at', 'idx_products_created_at');
        });

        // Add indexes to retailers table
        Schema::table('retailers', function (Blueprint $table) {
            $table->index('name', 'idx_retailers_name');
            $table->index('created_at', 'idx_retailers_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from stock table
        Schema::table('stock', function (Blueprint $table) {
            $table->dropIndex('idx_stock_product_id');
            $table->dropIndex('idx_stock_retailer_id');
            $table->dropIndex('idx_stock_in_stock');
            $table->dropIndex('idx_stock_product_in_stock');
            $table->dropIndex('idx_stock_retailer_in_stock');
            $table->dropIndex('idx_stock_updated_at');
        });

        // Remove indexes from price_alerts table
        Schema::table('price_alerts', function (Blueprint $table) {
            $table->dropIndex('idx_price_alerts_product_id');
            $table->dropIndex('idx_price_alerts_is_active');
            $table->dropIndex('idx_price_alerts_product_active');
            $table->dropIndex('idx_price_alerts_target_price');
            $table->dropIndex('idx_price_alerts_last_triggered');
        });

        // Remove indexes from stock_history table
        Schema::table('stock_history', function (Blueprint $table) {
            $table->dropIndex('idx_stock_history_stock_id');
            $table->dropIndex('idx_stock_history_created_at');
            $table->dropIndex('idx_stock_history_stock_created');
        });

        // Remove indexes from activity_log table
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('idx_activity_log_user_id');
            $table->dropIndex('idx_activity_log_subject_type');
            $table->dropIndex('idx_activity_log_action');
            $table->dropIndex('idx_activity_log_created_at');
            $table->dropIndex('idx_activity_log_subject');
        });

        // Remove indexes from notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_notifiable_type');
            $table->dropIndex('idx_notifications_notifiable_id');
            $table->dropIndex('idx_notifications_read_at');
            $table->dropIndex('idx_notifications_notifiable');
            $table->dropIndex('idx_notifications_notifiable_read');
        });

        // Remove indexes from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_name');
            $table->dropIndex('idx_products_created_at');
        });

        // Remove indexes from retailers table
        Schema::table('retailers', function (Blueprint $table) {
            $table->dropIndex('idx_retailers_name');
            $table->dropIndex('idx_retailers_created_at');
        });
    }
};
