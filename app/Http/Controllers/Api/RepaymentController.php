<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\RepaymentFrequency;
use App\Models\Loan;
use App\Models\Transaction;

class RepaymentController extends Controller
{

    /**
     * Add the specified resource in storage.
     * @param  $repaymentId, $actual_amount, $transactionId, $loan_application_id, $user_id
     */
    public function addRepayment(Request $request)
    {
        try {
            // Validate request params
            $validateRepaymentParams = Validator::make($request->all(),
            [
                'id'             => 'required',
                'actual_payment' => 'required',                  
                'payment_method' => 'required',                                
                'user_id'        => 'required',                                                
                'loan_application_id' => 'required',                              
            ]);

            // Return error if validation fails
            if($validateRepaymentParams->fails()){
                return response()->json([               
                    'status'    => false,
                    'message'   => 'Validation errors',
                    'error'     => $validateRepaymentParams->errors()
                ], 401);
            }

            // Insert payment transaction details    
            $createTransaction = Transaction::create([
                'loan_application_id' => $request->loan_application_id,  
                'payment_method'      => $request->payment_method,  
                'user_id'             => $request->user_id,                
                'amount'              => $request->actual_payment,                                
            ]);

            // Update repayment instalment with amount, transactionID, status
            RepaymentFrequency::where('id', $request->id)
                ->update([                   
                    'actual_payment'   => $request->actual_payment,
                    'transation_id'    => $createTransaction->id,
                    'status'           => Loan::PAID,
                ]);

                // Get all repayments status of particular loan
                $loanRecords = RepaymentFrequency::select('status')        
                ->where('loan_application_id', $request->loan_application_id)                
                ->get();

                // Logic to check if all repayments are PAID
                $flag = 1;  
                foreach($loanRecords as $loanRecord){
                    if($loanRecord->status == Loan::PAID){
                        $flag = $flag + 1;
                    }else{
                        $flag = 0;
                        break;
                    }
                }

                //If all repayments are PAID then update loan table status to PAID
                if($flag == 0){
                    Loan::where('id', $request->loan_application_id)
                    ->update([                                                                   
                        'status' => Loan::PAID,
                    ]);
                }
            return response()->json([               
                'status' => true,
                'message' => 'Repayment instalment added successfully',                                               
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([               
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        
    }
}
