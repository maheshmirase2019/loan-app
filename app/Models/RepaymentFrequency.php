<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepaymentFrequency extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_application_id', 'term_count', 'term_date', 'term_amount', 'actual_payment', 'transation_id', 'status'
    ];

    protected $table = 'repayment_frequencies';

    public const PAID = 3;
}
