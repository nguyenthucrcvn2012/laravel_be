<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;

class UserController extends Controller
{

    private $model;
    public function __construct(User $user) {
        $this->model = $user;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function active($id) {

        $user = $this->model->find($id);
        if($user){
            $active = 0;
            if($user->is_active == 0){

                $active = 1;
            }

            $query = $this->model->where('id', $id)->update(['is_active' => $active]);
            if($query){

                return response()->json([
                    'status' => 200,
                    'message' => 'Cập nhật thành công!'
                ]);
            }
            else{

                return response()->json([
                    'status' => 500,
                    'message' => 'Lỗi thử lại sau!'
                ]);
            }
        }
        else{

            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy dữ liệu!'
            ]);
        }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        dd('ok');
        $arrayIDAdmin = ['nguyen.thuc.rcvn2012@gmail.com'];

        $users = $this->model->Name($request)
            ->Email($request)
            ->IsActive($request)
            ->GroupRole($request)
            ->orderBy('id', 'DESC')
            ->where('is_delete', 0)
            ->whereNotIn('email', $arrayIDAdmin)
            ->paginate(10);

//        $users->appends(['name' => $request->input('name')]);
//        $users->appends(['email' => $request->input('email')]);
//        $users->appends(['group_role' => $request->input('group_role')]);
//        $users->appends(['is_active' => $request->input('is_active')]);

        if($users){
            return response()->json([
                'status' => 200,
                'users' => $users
            ]);
        };

        return response()->json([
            'status' => 500,
            'users' => [],
            'message' => 'Lỗi, thử lại sau!'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {

//        var_dump('ok');
        $arrayIDAdmin = ['nguyen.thuc.rcvn2012@gmail.com'];

        $users = $this->model->Name($request)
            ->Email($request)
            ->IsActive($request)
            ->GroupRole($request)
            ->orderBy('id', 'DESC')
            ->where('is_delete', 0)
            ->whereNotIn('email', $arrayIDAdmin)
            ->paginate(10);

        $users->appends(['name' => $request->input('name')]);
        $users->appends(['email' => $request->input('email')]);
        $users->appends(['group_role' => $request->input('group_role')]);
        $users->appends(['is_active' => $request->input('is_active')]);

        if($users){
            return response()->json([
                'status' => 200,
                'users' => $users
            ]);
        };

        return response()->json([
            'status' => 500,
            'users' => [],
            'message' => 'Lỗi, thử lại sau'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:App\Models\User,email',
                'regex:/^\w+[-\.\w]*@(?!(?:outlook|myemail|yahoo)\.com$)\w+[-\.\w]*?\.\w{2,4}$/'
            ],
            'password' => 'required|min:5|max:15',
            'password_confirm' => 'required|min:5|same:password|max:15',
            'name' => 'required|max:254',
            'group_role' => 'required|max:70',
        ]);

        if($validator->fails()){

            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        }
        else{

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'group_role' => $request->group_role,
                'is_active' => $request->is_active

            ];
            if($this->model->create($data)){

                return response()->json([
                    'status' => 200,
                    'message' => 'Thêm mới thành công',
                ]);
            }
            else {

                return response()->json([
                    'status' => 500,
                    'message' => 'Vui lòng thử lại sau!',
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->model->find($id);
        if($user) {

            return response()->json([
                'status' => 200,
                'user' => $user
            ]);
        }
        else {

            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy dữ liệu'
            ]);
        }
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
        $user = $this->model->find($id);
        if($user) {
            $validator = Validator::make($request->all(), [
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'regex:/^\w+[-\.\w]*@(?!(?:outlook|myemail|yahoo)\.com$)\w+[-\.\w]*?\.\w{2,4}$/'
                ],
                'name' => 'required|max:254',
                'group_role' => 'required|max:70',
            ]);

            if($validator->fails()){

                return response()->json([
                    'validation_errors' => $validator->messages()
                ]);
            }

            $arrayEmail = $this->model->whereNotIn('id', [$id])->pluck('email')->toArray();

            if(in_array($request->email, $arrayEmail)){

                return response()->json([
                    'validation_errors' => [
                        'email' => 'The email has already been token'
                    ]
                ]);
            }
            else{

                $data = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'group_role' => $request->group_role,
                    'is_active' => $request->is_active
                ];

                if($this->model->where('id', $id)->update($data)){

                    return response()->json([
                        'status' => 200,
                        'message' => 'Cập nhật thành công',
                    ]);
                }
                else {

                    return response()->json([
                        'status' => 500,
                        'message' => 'Vui lòng thử lại sau!',
                    ]);
                }
            }
        }

        return response()->json([
            'status' => 404,
            'message' => 'Không tìm thấy dữ liệu!',
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {

        $query = $this->model->where('id', $id)->update(['is_delete' => 1]);
        if($query){

            return response()->json([
                'status' => 200,
                'message' => 'Xóa thành công!'
            ]);
        }
        else{

            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy dử liệu!'
            ]);
        }
    }
}
