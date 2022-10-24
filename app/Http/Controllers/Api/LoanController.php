<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Loan;
use App\Models\RepaymentFrequency;

class LoanController extends Controller
{
    /**
     * Display a listing of the requested loans userwise.
     * Params : userId
     */
    public function index(int $userId)
    {
        try {
            $loanRecords = Loan::select('*')        
            ->where('user_id', $userId)                
            ->get();

            return response()->json([               
                'status' => true,
                'loanList' => $loanRecords->toArray() 
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([               
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        
    }

    /**
     * Create/Submit loan request
     * @param Request $request
     * @return 
     */
    public function createLoanRequest(Request $request)
    {        
        try {
            // Validate request params
            $validateLoanParams = Validator::make($request->all(),
            [
                'user_id'       => 'required',
                'load_type_id'  => 'required',
                'loan_amount'   => 'required',
                'loan_terms'    => 'required',                
            ]);

            // Return error if validation fails
            if($validateLoanParams->fails()){
                return response()->json([               
                    'status'    => false,
                    'message'   => 'Validation errors',
                    'error'     => $validateLoanParams->errors()
                ], 401);
            }

            // Check if already applied for loan with same loan type by user
            $loanRecord = Loan::where('user_id', $request->user_id)->first(); 
                      
            if( isset($loanRecord->load_type_id) && $loanRecord->load_type_id == $request->load_type_id){
                return response()->json([               
                    'status'    => false,
                    'message'   => 'User already applied for this loan',                    
                ], 401);
            }
            
            // Calculate loan amount with interest. INTEREST rate defined in LOAN model as a constant.
            $calculated_final_amount = (Loan::INTEREST/100) * $request->loan_amount + $request->loan_amount;

            // Insert records in table
            $createLoan = Loan::create([
                'user_id'       => $request->user_id,
                'load_type_id'  => $request->load_type_id,
                'loan_amount'   => $request->loan_amount,
                'loan_terms'    => $request->loan_terms,  
                'calculated_final_amount' => $calculated_final_amount           
            ]);

            return response()->json([               
                'status' => true,
                'message' => 'Loan request submitted successfully',                                               
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([               
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @param  int  $loanId, $status, $adminId
     */
    public function approveRejectLoanByAdmin($loanId, $status, $adminId)
    {
        try {
            // Update loan status
            Loan::where('id', $loanId)
                ->update([
                    'status' => $status,                
                ]);

            // If loan approved by admin, then create term frequencies for repayment
            // Loan::APPROVED : statuses defined in Loan model          
            if($status == Loan::APPROVED){

                // Update loan approved date & approved by
                $todaysDate = date('y-m-d');
                
                Loan::where('id', $loanId)
                ->update([                   
                    'loan_approved_date' => $todaysDate,
                    'loan_approved_by'   => $adminId,
                ]);

                // Get loan record based on loan id
                $loanRecord = Loan::where('id', $loanId)->first();

                $calculated_final_amount = $loanRecord->calculated_final_amount;
                $loan_terms     = $loanRecord->loan_terms;

                // Calculate term amount for all frequency
                $term_amount    = $calculated_final_amount/$loan_terms;

                for($i=1; $i<=$loan_terms; $i++){
                    // Increase frequency by a week(7 Days)
                    $repeat = strtotime("+7 day",strtotime($todaysDate));                   
                    $todaysDate = date('y-m-d',$repeat);                    
                    
                    $repayment = RepaymentFrequency::create([
                        'loan_application_id'   => $loanId,
                        'term_count'            => $i,
                        'term_date'             => $todaysDate,
                        'term_amount'           => $term_amount,                        
                    ]);                     
                }                
            }

            return response()->json([               
                'status' => true,
                'message' => 'Loan status updated successfully',                                               
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([               
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        
    }

}
