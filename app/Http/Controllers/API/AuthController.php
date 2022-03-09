<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|max:191|email',
            'password' => 'required|min:8',
        ]);

        if($validator->fails()){
            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        }
        else{

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'is_active' => 1])) {
                $user = User::where('email', $request->email)->first();
                $token = $user->createToken($user->email.'_Token')->plainTextToken;

                return response()->json([
                    'status' => 200,
                    'usename' => $user->name,
                    'message' => 'Đăng nhập thành công',
                    'token' => $token
                ]);
            }
            else{
                return response()->json([
                    'status' => 401,
                    'message' => 'Sai tài khoản hoặc mật khẩu'
                ]);
            }
        }
    }
}
