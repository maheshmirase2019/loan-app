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
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('load_type_id');
            $table->foreign('load_type_id')->references('id')->on('loan_types');            
            $table->decimal('loan_amount', 10, 2)->comment('Loan amount requested by user');
            $table->integer('loan_terms')->comment('In Weeks');
            $table->tinyInteger('status')->comment('1:Pending, 2:Approved, 3:Paid, 4:Rejected')->default(1);
            $table->date('loan_approved_date')->nullable()->comment('Loan approved by admin on this date');
            $table->unsignedBigInteger('loan_approved_by')->nullable();
            $table->foreign('loan_approved_by')->references('id')->on('users');
            $table->decimal('interest', 10, 2)->comment('In %, User has to pay amount with interest if his loan approved.')->default(10);
            $table->decimal('calculated_final_amount', 10, 2)->comment('Amount user has to pay with interest');
            $table->text('comments')->change();            
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
        Schema::dropIfExists('loan_applications');
    }
};
