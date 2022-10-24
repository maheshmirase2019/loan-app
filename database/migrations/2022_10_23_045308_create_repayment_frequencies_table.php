<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repayment_frequencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_application_id');
            $table->foreign('loan_application_id')->references('id')->on('loan_applications');
            $table->tinyInteger('term_count');
            $table->date('term_date');
            $table->decimal('term_amount', 10, 2)->comment('Amount for this term.');
            $table->decimal('actual_payment', 10, 2)->nullable()->comment('Amount paid by user for this term.');
            $table->unsignedBigInteger('transation_id')->nullable();
            $table->foreign('transation_id')->references('id')->on('transactions');
            $table->tinyInteger('status')->comment('1:Pending, 3:Paid')->default(1);        
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
        Schema::dropIfExists('repayment_frequencies');
    }
};
