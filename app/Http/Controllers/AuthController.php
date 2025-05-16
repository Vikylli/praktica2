<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class AuthController extends Controller
{
   
    public function register(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:3',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid fields',
                'errors'  => $validator->errors()
            ], 422);
        }
        
   
        User::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);
        
        return response()->json(['success' => true], 201);
    }
    
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data',
                'errors'  => $validator->errors(),
            ], 422);
        }
        
       
        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid data',
                'errors'  => ['email' => ['Invalid data']],
            ], 422);
        }
        
        
        $token = $user->createToken('api-token')->plainTextToken;
        
        return response()->json(['token' => $token], 200);
    }

}
