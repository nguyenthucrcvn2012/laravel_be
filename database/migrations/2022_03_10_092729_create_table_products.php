<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_id', 20)->unique()->index();
            $table->string('product_name', 240);
            $table->string('product_image')->nullable();
            $table->decimal('product_price', 14, 2)->default(0);
            $table->tinyInteger('is_sales')->default(1)->default(1)
                ->comment('0: Dừng bán hoặc dừng sản xuất, 1: Có bán hàng');
            $table->tinyInteger('is_delete')->default(0);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('product');
    }
}
