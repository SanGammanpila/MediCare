<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicalReceiptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medical_receipt', function (Blueprint $table) {
            $table->bigInteger('patient_id');
            $table->bigInteger('reciept_id');
            $table->bigInteger('medicine_id');
            $table->string('dose')->default('NA');

            $table->timestamp('added_on')->useCurrent();

            $table->primary(array('reciept_id','medicine_id','patient_id'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medical_receipt');
    }
}
