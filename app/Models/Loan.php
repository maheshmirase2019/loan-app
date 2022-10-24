<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'load_type_id', 'loan_amount', 'loan_terms', 'status', 'loan_approved_date', 'loan_approved_by', 'interest', 'calculated_final_amount'
    ];

    protected $table = 'loan_applications';

    public const INTEREST = 10; // Default interest 10%

    public const PENDING = 1;
    public const APPROVED = 2;
    public const PAID = 3;
    public const REJECTED = 4;
}
