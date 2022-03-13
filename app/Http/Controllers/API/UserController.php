<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Auth;

class UserController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function active($id) {

        $user = User::find($id);
        if($user){
            $active = 0;
            if($user->is_active == 0){

                $active = 1;
            }

            $query = User::where('id', $id)->update(['is_active' => $active]);
            if($query){

                return response()->json([
                    'status' => 200,
                    'message' => 'Cập nhật thành công!'
                ]);
            }
            else{

                return response()->json([
                    'status' => 404,
                    'users' => [],
                    'message' => 'Lỗi thử lại sau!'
                ]);
            }
        }
        else{

            return response()->json([
                'status' => 404,
                'users' => [],
                'message' => 'Lỗi thử lại sau!'
            ]);
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $arrayIDAdmin = ['nguyen.thuc.rcvn2012@gmail.com'];
        $users = User::orderBy('id', 'DESC')->where('is_delete', 0)
            ->whereNotIn('email', $arrayIDAdmin)->paginate(10);
//        UserResource::collection($query);

        if($users->count() > 0){
            return response()->json([
                'status' => 200,
                'users' => $users
            ]);
        };
        return response()->json([
            'status' => 401,
            'users' => [],
            'message' => 'Không tìm thấy dữ liệu'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $query = User::where('id', $id)->update(['is_delete' => 1]);
        if($query){

            return response()->json([
                'status' => 200,
                'message' => 'Xóa thành công!'
            ]);
        }
        else{

            return response()->json([
                'status' => 404,
                'users' => [],
                'message' => 'Lỗi thử lại sau!'
            ]);
        }
    }
}
