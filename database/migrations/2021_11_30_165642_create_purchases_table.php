<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['GENERAL', 'CONVECTION'])->default('GENERAL');
            $table->string('number');
            $table->date('date');
            $table->date('due')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('vendor_id');
            $table->timestamps();

            $table->foreign('vendor_id')->on('vendors')->references('id')->onDelete('RESTRICT');
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id');
            $table->string('name')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('purchase_id')->on('purchases')->references('id')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
    }
}
