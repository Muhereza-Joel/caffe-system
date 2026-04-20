<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. After INSERT
        DB::unprepared('
            CREATE TRIGGER order_items_after_insert
            AFTER INSERT ON order_items
            FOR EACH ROW
            BEGIN
                UPDATE orders
                SET total_amount = (
                    SELECT COALESCE(SUM(quantity * price), 0)
                    FROM order_items
                    WHERE order_id = NEW.order_id AND deleted_at IS NULL
                )
                WHERE id = NEW.order_id;
            END
        ');

        // 2. After UPDATE (Price/Quantity changes)
        DB::unprepared('
            CREATE TRIGGER order_items_after_update
            AFTER UPDATE ON order_items
            FOR EACH ROW
            BEGIN
                UPDATE orders
                SET total_amount = (
                    SELECT COALESCE(SUM(quantity * price), 0)
                    FROM order_items
                    WHERE order_id = NEW.order_id AND deleted_at IS NULL
                )
                WHERE id = NEW.order_id;
            END
        ');

        // 3. After DELETE (Hard delete)
        DB::unprepared('
            CREATE TRIGGER order_items_after_delete
            AFTER DELETE ON order_items
            FOR EACH ROW
            BEGIN
                UPDATE orders
                SET total_amount = (
                    SELECT COALESCE(SUM(quantity * price), 0)
                    FROM order_items
                    WHERE order_id = OLD.order_id AND deleted_at IS NULL
                )
                WHERE id = OLD.order_id;
            END
        ');

        // Note: For Soft Deletes/Restore, SQLite uses the WHEN clause for efficiency
        // rather than IF statements inside the BEGIN block.
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS order_items_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS order_items_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS order_items_after_delete');
    }
};
