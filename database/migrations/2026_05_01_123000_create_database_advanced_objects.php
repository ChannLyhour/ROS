<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. VIEW: Daily Revenue Summary
        // Joins payments and orders to give a high-level daily report
        DB::unprepared("
            CREATE OR REPLACE VIEW view_daily_revenue_summary AS
            SELECT 
                DATE(paid_at) as sale_date,
                COUNT(id) as total_transactions,
                SUM(total_amount) as gross_revenue,
                SUM(paid_amount) as actual_cash_collected,
                payment_method
            FROM payments
            WHERE status = 'paid'
            GROUP BY DATE(paid_at), payment_method;
        ");

        // 2. TRIGGERS: Auto-Update Order Totals
        // This ensures the order total is always correct when items are added, updated, or removed.
        
        // After Insert
        DB::unprepared("
            CREATE TRIGGER tr_after_order_item_insert
            AFTER INSERT ON order_items
            FOR EACH ROW
            BEGIN
                UPDATE orders 
                SET subtotal = (SELECT IFNULL(SUM(subtotal), 0) FROM order_items WHERE order_id = NEW.order_id),
                    total_amount = (SELECT IFNULL(SUM(subtotal), 0) FROM order_items WHERE order_id = NEW.order_id) + tax
                WHERE id = NEW.order_id;
            END;
        ");

        // After Update
        DB::unprepared("
            CREATE TRIGGER tr_after_order_item_update
            AFTER UPDATE ON order_items
            FOR EACH ROW
            BEGIN
                UPDATE orders 
                SET subtotal = (SELECT IFNULL(SUM(subtotal), 0) FROM order_items WHERE order_id = NEW.order_id),
                    total_amount = (SELECT IFNULL(SUM(subtotal), 0) FROM order_items WHERE order_id = NEW.order_id) + tax
                WHERE id = NEW.order_id;
            END;
        ");

        // After Delete
        DB::unprepared("
            CREATE TRIGGER tr_after_order_item_delete
            AFTER DELETE ON order_items
            FOR EACH ROW
            BEGIN
                UPDATE orders 
                SET subtotal = (SELECT IFNULL(SUM(subtotal), 0) FROM order_items WHERE order_id = OLD.order_id),
                    total_amount = (SELECT IFNULL(SUM(subtotal), 0) FROM order_items WHERE order_id = OLD.order_id) + tax
                WHERE id = OLD.order_id;
            END;
        ");

        // 3. STORED PROCEDURE: Atomic Checkout
        // Handles the full payment flow in a single database transaction
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_complete_order;
            CREATE PROCEDURE sp_complete_order(
                IN p_order_id INT, 
                IN p_method VARCHAR(20), 
                IN p_amount DECIMAL(10,2)
            )
            BEGIN
                DECLARE v_total DECIMAL(10,2);
                DECLARE v_table_id INT;

                SELECT total_amount, table_id INTO v_total, v_table_id FROM orders WHERE id = p_order_id;

                -- Record Payment
                INSERT INTO payments (order_id, payment_method, total_amount, paid_amount, change_amount, status, paid_at, created_at, updated_at)
                VALUES (p_order_id, p_method, v_total, p_amount, (p_amount - v_total), 'paid', NOW(), NOW(), NOW());

                -- Update Order status to completed
                UPDATE orders SET status = 'completed' WHERE id = p_order_id;

                -- Release Table status to available
                IF v_table_id IS NOT NULL THEN
                    UPDATE tables SET status = 'available' WHERE id = v_table_id;
                END IF;
            END;
        ");

        // 4. EVENT: Cleanup Abandoned Orders
        
        // Automatically cancels 'pending' orders older than 24 hours
        DB::statement("SET GLOBAL event_scheduler = ON;");
        DB::unprepared("
            DROP EVENT IF EXISTS ev_cleanup_unpaid_orders;
            CREATE EVENT ev_cleanup_unpaid_orders
            ON SCHEDULE EVERY 1 DAY
            STARTS CURRENT_TIMESTAMP
            DO
                UPDATE orders 
                SET status = 'cancelled' 
                WHERE status = 'pending' 
                AND created_at < NOW() - INTERVAL 1 DAY;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS view_daily_revenue_summary");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_after_order_item_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_after_order_item_update");
        DB::unprepared("DROP TRIGGER IF EXISTS tr_after_order_item_delete");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_complete_order");
        DB::unprepared("DROP EVENT IF EXISTS ev_cleanup_unpaid_orders");
    }
};


// View (view_daily_revenue_summary): Provides real-time daily sales reports.
// Triggers (tr_after_order_item_...): Automatically updates orders.total_amount whenever an item is added, changed, or deleted.
// Stored Procedure (sp_complete_order): A single, safe command to handle payments and close orders.
// Event (ev_cleanup_unpaid_orders): Automatically cancels old unpaid orders every 24 hours.