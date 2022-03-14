<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->integer('order_id')->unique()->index()->autoIncrement();
            $table->string('order_shop', 40);
            $table->integer('customer_id');
            $table->integer('total_price');
            $table->string('payment_method')->comment('1:COD, 2: Paypal, 3:GMO');
            $table->integer('ship_charge');
            $table->integer('tax');
            $table->dateTime('order_date');
            $table->dateTime('shipment_date');
            $table->dateTime('cancel_date');
            $table->tinyInteger('order_status');
            $table->string('note_customer', 255);
            $table->string('error_code_api', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order');
    }
}
