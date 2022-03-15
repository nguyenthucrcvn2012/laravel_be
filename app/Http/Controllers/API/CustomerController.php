<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::orderBy('customer_id', 'DESC')->paginate(10);

        if($customers->count() > 0){
            return response()->json([
                'status' => 200,
                'customers' => $customers
            ]);
        };

        return response()->json([
            'status' => 401,
            'customers' => [],
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {
        $customers = Customer::Name($request)
            ->Email($request)
            ->IsActive($request)
            ->Address($request)
            ->orderBy('customer_id', 'DESC')
            ->paginate(10);

        $customers->appends(['customer_name' => $request->input('customer_name')]);
        $customers->appends(['email' => $request->input('email')]);
        $customers->appends(['address' => $request->input('address')]);
        $customers->appends(['is_active' => $request->input('is_active')]);

        if($customers->count() > 0){
            return response()->json([
                'status' => 200,
                'customers' => $customers
            ]);
        };

        return response()->json([
            'status' => 401,
            'customers' => [],
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:65|email|unique:App\Models\Customer,email',
            'address' => 'required|max:254',
            'tel_num' => 'required|min:9|max:14',
            'customer_name' => 'required|max:254',
        ]);

        if($validator->fails()){

            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        }
        else{

            $data = [
                'customer_name' => $request->customer_name,
                'email' => $request->email,
                'address' => $request->address,
                'tel_num' => $request->tel_num,
                'is_active' => $request->is_active
            ];
            if(Customer::create($data)){

                return response()->json([
                    'status' => 200,
                    'message' => 'Thêm mới thảnh công!',
                ]);
            }
            else{
                return response()->json([
                    'status' => 401,
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
        $customer = Customer::find($id);
        if($customer) {

            return response()->json([
                'status' => 200,
                'customer' => $customer
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
        $customer = Customer::find($id);
        if($customer){
            $validator = Validator::make($request->all(), [
                'email' => 'required|max:65|email',
                'address' => 'required|max:254',
                'tel_num' => 'required|min:9|max:14',
                'customer_name' => 'required|max:254',
            ]);

            if($validator->fails()){

                return response()->json([
                    'validation_errors' => $validator->messages()
                ]);
            }

            $arrayTelNum = Customer::whereNotIn('customer_id', [$id])->pluck('tel_num')->toArray();

            if(in_array($request->tel_num, $arrayTelNum)){

                return response()->json([
                    'validation_errors' => [
                        'tel_num' => 'The phone has already been token'
                    ]
                ]);
            }

            else{

                $data = [
                    'customer_name' => $request->customer_name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'tel_num' => $request->tel_num,
                    'is_active' => $request->is_active
                ];
                if(Customer::where('customer_id', $id)->update($data)){

                    return response()->json([
                        'status' => 200,
                        'message' => 'Cập nhật thảnh công!',
                    ]);
                }
                else{
                    return response()->json([
                        'status' => 401,
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
    public function destroy($id)
    {
        //
    }
}
