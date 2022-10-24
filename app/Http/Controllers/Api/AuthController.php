<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User
     */
     public function createUser(Request $request){

        try {            
            $validateUser = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([               
                    'status' => false,
                    'message' => 'Validation errors',
                    'error' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' =>  $request->email,
                'password' => Hash::make($request->password)  
            ]);

            return response()->json([               
                'status' => true,
                'message' => 'User registered successfully',
                'token' => $user->createToken("API Token")->plainTextToken   // Token is already defined for api in User model
            ], 200);


        } catch (\Throwable $th) {            
            return response()->json([               
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

     }

     /**
     * Login User
     * @param Request $request
     * @return Token
     */
     public function loginUser(Request $request)
     {
        try {
            $validateUser = Validator::make($request->all(),
            [               
                'email' => 'required|email',
                'password' => 'required'
            ]);            

            if($validateUser->fails()){
                return response()->json([               
                    'status' => false,
                    'message' => 'Validation errors',
                    'error' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([               
                    'status' => false,
                    'message' => 'Email or Password is Incorrect',                    
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([               
                'status' => true,
                'message' => 'User logged in successfully',
                'token' => $user->createToken("API Token")->plainTextToken   // To use this defined in User model
            ], 200);


        } catch (\Throwable $th) {
            return response()->json([               
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
     }

}
