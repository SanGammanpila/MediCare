<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiptCheckupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipt_checkups', function (Blueprint $table) {
            $table->bigInteger('receipt_id');
            $table->bigInteger('checkup_id');
            $table->enum('status',array('done','pending','NA'))->default('pending');
            $table->primary(array('receipt_id','checkup_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipt_checkups');
    }
}
