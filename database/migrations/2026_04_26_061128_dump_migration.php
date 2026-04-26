<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->char('hash', 32);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('token', 64);
            $table->string('number', 10)->nullable();

            $table->tinyInteger('status')->default(1)->comment('1-новый,2-оплачен,3-собран,4-отправлен,5-доставлен,6-отменен');
            $table->tinyInteger('step')->default(1)->comment('Шаг оформления');

            $table->string('client_name', 255)->nullable();
            $table->string('client_surname', 255)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('company_name', 255)->nullable();

            $table->char('currency', 3)->default('EUR');
            $table->decimal('cur_rate', 10, 4)->default(1.0000);
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->smallInteger('discount')->nullable();

            $table->timestamp('create_date')->useCurrent();
            $table->timestamp('update_date')->nullable()->useCurrentOnUpdate();
            $table->softDeletes('deleted_at');
            $table->index('user_id');
            $table->index('hash');
            $table->index('number');
            $table->index('status');
            $table->index('create_date');
            $table->index(['status', 'create_date']);
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->char('code', 2)->unique(); // IT, FR, DE и т.д.
            $table->string('name', 100);
            $table->string('phone_code', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('order_delivery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            $table->decimal('delivery_cost', 10, 2)->nullable();
            $table->decimal('delivery_cost_eur', 10, 2)->nullable();
            $table->tinyInteger('delivery_type')->default(0);
            $table->tinyInteger('calculate_type')->default(0);

            // Адрес
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('region', 50)->nullable();
            $table->string('city', 200)->nullable();
            $table->string('postal_index', 20)->nullable();
            $table->string('address_line', 300)->nullable();
            $table->string('building', 200)->nullable();
            $table->string('apartment', 30)->nullable();
            $table->string('phone_code', 20)->nullable();
            $table->string('phone', 20)->nullable();

            // Сроки
            $table->date('time_min')->nullable();
            $table->date('time_max')->nullable();
            $table->date('time_confirm_min')->nullable();
            $table->date('time_confirm_max')->nullable();
            $table->date('time_fast_pay_min')->nullable();
            $table->date('time_fast_pay_max')->nullable();
            $table->date('time_old_min')->nullable();
            $table->date('time_old_max')->nullable();

            // Логистика
            $table->string('tracking_number', 50)->nullable();
            $table->string('carrier_name', 50)->nullable();
            $table->string('carrier_contacts', 255)->nullable();
            $table->decimal('weight_gross', 10, 2)->nullable();

            // Даты
            $table->datetime('proposed_date')->nullable();
            $table->datetime('ship_date')->nullable();
            $table->datetime('fact_date')->nullable();
            $table->datetime('cancel_date')->nullable();
            $table->datetime('offset_date')->nullable();
            $table->tinyInteger('offset_reason')->nullable();

            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            $table->index('order_id');
            $table->index('country_id');
            $table->index('tracking_number');
            $table->index(['time_min', 'time_max']);
        });

        Schema::create('order_payment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            $table->tinyInteger('pay_type');
            $table->datetime('pay_date_execution')->nullable();
            $table->date('full_payment_date')->nullable();
            $table->boolean('bank_transfer_requested')->nullable();
            $table->boolean('accept_pay')->nullable();
            $table->boolean('payment_euro')->default(false);
            $table->boolean('spec_price')->nullable();
            $table->tinyInteger('vat_type')->default(0);
            $table->string('vat_number', 100)->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->json('bank_details')->nullable();
            $table->timestamps();

            $table->index('pay_type');
            $table->index('full_payment_date');
        });

        Schema::create('order_management', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            $table->string('manager_name', 20)->nullable();
            $table->string('manager_email', 30)->nullable();
            $table->string('manager_phone', 20)->nullable();
            $table->boolean('address_equal')->default(true);
            $table->unsignedInteger('address_payer_id')->nullable();
            $table->tinyInteger('mirror')->nullable();
            $table->boolean('process')->nullable();
            $table->boolean('show_msg')->nullable();
            $table->boolean('product_review')->nullable();
            $table->tinyInteger('entrance_review')->nullable();
            $table->json('warehouse_data')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            $table->index('manager_name');
            $table->index('mirror');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->unsignedInteger('article_id');
            $table->string('article_sku', 100)->nullable();
            $table->decimal('amount', 10, 3)->default(0);
            $table->decimal('packaging_count', 10, 3)->default(1);
            $table->decimal('weight', 10, 3);
            $table->decimal('packaging', 10, 3);
            $table->decimal('pallet', 10, 3);
            $table->decimal('price', 12, 2);
            $table->decimal('price_eur', 12, 2)->nullable();
            $table->char('currency', 3)->nullable();
            $table->char('measure', 2)->nullable();
            $table->date('delivery_time_min')->nullable();
            $table->date('delivery_time_max')->nullable();
            $table->tinyInteger('multiple_pallet')->nullable();
            $table->boolean('swimming_pool')->default(false);

            $table->index('order_id');
            $table->index('article_id');
            $table->index(['order_id', 'article_id']);
        });


        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->tinyInteger('old_status');
            $table->tinyInteger('new_status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('comment', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('order_id');
            $table->index('created_at');
            $table->index(['old_status', 'new_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('
            DROP TABLE IF EXISTS order_status_history;
            DROP TABLE IF EXISTS order_items;
            DROP TABLE IF EXISTS order_management;
            DROP TABLE IF EXISTS countries;
            DROP TABLE IF EXISTS order_payment;
            DROP TABLE IF EXISTS order_delivery;
            DROP TABLE IF EXISTS orders;
        ');
    }
};
