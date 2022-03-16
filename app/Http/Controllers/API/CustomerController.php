<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    public function exportCsv(Request $request) {

        if(
            $request->has('customer_name') && $request->input('customer_name') != '' ||
            $request->has('email') && $request->input('email') != '' ||
            $request->has('address') && $request->input('address') != '' ||
            $request->has('is_active')  && $request->input('is_active') != ''
        )
        {
            $customers = Customer::Name($request)
                ->Email($request)
                ->IsActive($request)
                ->Address($request)
                ->orderBy('customer_id', 'DESC')
                ->get();
        }
        else {
            $customers = Customer::orderBy('customer_id', 'DESC')
                ->take(10)
                ->get();
        }
        $fileName = 'customer.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        $columns = array('customer_name', 'email', 'tel_num', 'address');
        $callback = function() use($customers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($customers as $customer) {
                $row['customer_name']  = $customer->customer_name;
                $row['email']    = $customer->email;
                $row['tel_num']    = $customer->tel_num;
                $row['address']  = $customer->address;

                fputcsv($file, array($row['customer_name'], $row['email'], $row['tel_num'], $row['address']));
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 200, $headers);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function importCsv(Request $request) {

        if($request->hasFile('file')){

            $ext = $request->file('file')->getClientOriginalExtension();

            if($ext !== 'csv') {
                return response()->json([
                    'status' => 422,
                    'message' => 'File không đúng định dạng'
                ]);
            }

            $datas = convertCsvToArray($request->file('file'));
//            return response()->json([
//                'status' => 200,
//                'message' => 'File không đúng định dạng',
//                'customers' => $datas
//            ]);

//            if(!$datas[0]) {
//                return response()->json([
//                    'status' => 401,
//                    'message' => $datas[1]
//                ]);
//            }
            $customers = Customer::all();
            $tel_nums = $customers->pluck('tel_num')->toArray();

            $err_line_data = [];
            $err_line_exist = [];
            $numSuccess = 0;
            $len = count($datas);

            if($len > 0) {
                for($i = 0; $i < $len; $i++){
                    if(
                        in_array($datas[$i]['tel_num'], $tel_nums)
                    )
                    {
                        array_push($err_line_exist, $i+2);
                    }
                    else if(
                        strlen($datas[$i]['tel_num']) > 14  || $datas[$i]['tel_num'] == '' || strlen($datas[$i]['tel_num']) < 8  ||
                        strlen($datas[$i]['address']) > 255 || $datas[$i]['address'] == '' ||
                        strlen($datas[$i]['email']) > 255 || $datas[$i]['email'] == '' || checkEmail($datas[$i]['email']) == false ||
                        strlen($datas[$i]['customer_name']) > 255 || $datas[$i]['customer_name'] == ''
                    ) {
                        array_push($err_line_data, $i+2);
                    }
                    else{

                        Customer::create($datas[$i]);
                        $numSuccess++;
                    }
                }
            }
            else{

                return response()->json([
                    'status' => 401,
                    'message' => 'Không có dữ liệu.'
                ]);
            }
            $message = '';
            $lenLineExist = count($err_line_exist);
            $lenLineError = count($err_line_data);
            $strLineExist = implode(",", $err_line_exist);
            $strLineError = implode(",", $err_line_data);
            if($lenLineExist > 0) {

                $message.= 'Dữ liệu đã tồn tại trong hệ thống ở dòng '.$strLineExist .'. ';
            }
            if($lenLineError) {

                $message.= 'Dữ liệu dòng '.$strLineError.' không hợp lệ. ';
            }
            $message.= 'Lưu thành công '.$numSuccess.' khách hàng.';

            return response()->json([
                'status' => 200,
                'message' => $message,
                'customers' => $datas,
            ]);
        }

        return response()->json([
            'status' => 500,
            'message' => 'Import thất bại'
        ]);
    }

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
