<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. After INSERT
        DB::unprepared('
            CREATE TRIGGER payments_after_insert_update_order_paid
            AFTER INSERT ON payments
            FOR EACH ROW
            WHEN NEW.status = "completed" AND NEW.deleted_at IS NULL
            BEGIN
                -- Update the paid amount
                UPDATE orders
                SET paid_amount = paid_amount + NEW.amount
                WHERE id = NEW.order_id;

                -- Update Order Status
                UPDATE orders
                SET status = CASE 
                    WHEN paid_amount >= total_amount THEN "completed"
                    WHEN paid_amount > 0 AND paid_amount < total_amount THEN "partially_paid"
                    ELSE "processing"
                END
                WHERE id = NEW.order_id;
            END
        ');

        // 2. After UPDATE
        DB::unprepared('
            CREATE TRIGGER payments_after_update_order_paid
            AFTER UPDATE ON payments
            FOR EACH ROW
            BEGIN
                -- Subtract OLD amount
                UPDATE orders 
                SET paid_amount = paid_amount - OLD.amount 
                WHERE id = OLD.order_id AND OLD.status = "completed" AND OLD.deleted_at IS NULL;

                -- Add NEW amount
                UPDATE orders 
                SET paid_amount = paid_amount + NEW.amount 
                WHERE id = NEW.order_id AND NEW.status = "completed" AND NEW.deleted_at IS NULL;

                -- Update Order Status based on new balance
                UPDATE orders
                SET status = CASE 
                    WHEN paid_amount >= total_amount THEN "completed"
                    WHEN paid_amount > 0 AND paid_amount < total_amount THEN "partially_paid"
                    ELSE "processing"
                END
                WHERE id = NEW.order_id;
            END
        ');

        // 3. After DELETE
        DB::unprepared('
            CREATE TRIGGER payments_after_delete_update_order_paid
            AFTER DELETE ON payments
            FOR EACH ROW
            WHEN OLD.status = "completed" AND OLD.deleted_at IS NULL
            BEGIN
                UPDATE orders
                SET paid_amount = paid_amount - OLD.amount
                WHERE id = OLD.order_id;

                -- Re-evaluate status after deletion
                UPDATE orders
                SET status = CASE 
                    WHEN paid_amount >= total_amount THEN "completed"
                    WHEN paid_amount > 0 AND paid_amount < total_amount THEN "partially_paid"
                    ELSE "processing"
                END
                WHERE id = OLD.order_id;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS payments_after_insert_update_order_paid');
        DB::unprepared('DROP TRIGGER IF EXISTS payments_after_update_order_paid');
        DB::unprepared('DROP TRIGGER IF EXISTS payments_after_delete_update_order_paid');
    }
};
