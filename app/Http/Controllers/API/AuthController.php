<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Auth;
use Carbon\Carbon;

class AuthController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(){

        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * @param Request $request email, password
     * @return \Illuminate\Http\JsonResponse
     *
     */
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
            $remember = $request->has('remember') ? true : false;
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'is_active' => 1, 'is_delete' => 0], $remember )) {

                $user = User::where('email', $request->email)->first();
                $updateUser = [
                    'last_login_at' => Carbon::now()->toDateTimeString(),
                    'last_login_ip' => $request->getClientIp()
                ];

                User::where('email', $request->email)->update($updateUser);
                $createToken = $user->createToken($user->email.'_Token');
                $token = $createToken->plainTextToken;
                $user->token = $token;
                $user->expired_at = Carbon::now()->addDay(1)->toDateTimeString();
                return response()->json([
                    'status' => 200,
                    'user' => $user,
                    'message' => 'Đăng nhập thành công',
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInfo(Request $request) {

        return response()->json($request->user());
    }
}
