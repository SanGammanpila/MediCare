<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiptMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_medicines', function (Blueprint $table) {
            $table->bigInteger('receipt_id');
            $table->bigInteger('medicine_id');
            $table->string('dose')->default('NA');

            $table->primary(array('receipt_id','medicine_id'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipt_medicines');
    }
}
