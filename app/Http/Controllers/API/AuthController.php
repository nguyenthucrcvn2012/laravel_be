<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|max:191',
            'password' => 'required|min:8',
        ]);

        if($validator->failed()){

            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        }
        else{
            $user = User::where('email', $request->email)->first();

            if(!$user || Hash::check($request->password, $user->password)){

                return response()->json([
                    'status' => 401,
                    'message' => 'Sai tài khoản hoặc mật khẩu'
                ]);
            }
            else{
                $token = $user->createToken($user->email.'_Token')->plainTextToken;

                return response()->json([
                    'status' => 200,
                    'usename' => $user->name,
                    'message' => 'Đăng nhập thành công',
                    'token' => $token
                ]);
            }
        }
    }
}
