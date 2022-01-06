<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receives', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->date('date');
            $table->text('description')->nullable();
            $table->foreignId('vendor_id');
            $table->timestamps();

            $table->foreign('vendor_id')->on('vendors')->references('id')->onDelete('RESTRICT');
        });

        Schema::create('receive_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receive_id');
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('notes')->nullable();
            $table->foreignId('purchase_item_id')->nullable();
            $table->timestamps();

            $table->foreign('purchase_item_id')->on('purchase_items')->references('id')->onDelete('RESTRICT');
            $table->foreign('receive_id')->on('receives')->references('id')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receive_items');
        Schema::dropIfExists('receives');
    }
}
