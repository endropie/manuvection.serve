<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->date('date');
            $table->date('due')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('vendor_id');
            $table->timestamps();

            $table->foreign('vendor_id')->on('vendors')->references('id')->onDelete('RESTRICT');
        });

        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id');
            $table->decimal('quantity', 10, 2)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('notes')->nullable();
            $table->nullableMorphs('base');
            $table->timestamps();

            $table->foreign('bill_id')->on('bills')->references('id')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('bills');
    }
}
