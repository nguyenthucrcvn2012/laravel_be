<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    private $model;
    public function __construct(Customer $customer) {
        $this->model = $customer;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Request $request) {

        //Kiem tra xem có filter khong
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
                ->orderBy('customer_id', 'DESC')
                ->take(10)
                ->get();
        }

        //CSV
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
                $row['customer_name']   = $customer->customer_name;
                $row['email']           = $customer->email;
                $row['tel_num']         = $customer->tel_num;
                $row['address']         = $customer->address;

                fputcsv($file, array($row['customer_name'],$row['email'],$row['tel_num'],$row['address']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

            $datasCsv = convertCsvToArray($request->file('file'));

//            return response()->json([
//                'status' => 200,
//                'message' => $datasCsv[2],
//                'customers' => $datasCsv[1]
//            ]);

            if($datasCsv[0] === false) {
                return response()->json([
                    'status' => 401,
                    'message' => $datasCsv[1]
                ]);
            }

            $datas = $datasCsv[1];

            $customers = $this->model->all();
            $emails = $customers->pluck('email')->toArray();

            $err_line_error = [];
            $err_line_exist = [];
            $numSuccess = 0;
            $len = count($datas);

            for($i = 0; $i < $len; $i++){
                if(
                    $datas[$i] == false ||
                    $datas[$i] == null ||
                    count($datas[$i]) !== 4 ||
                    strlen($datas[$i][0]) > 255  || $datas[$i][0] == '' || // check name
                    strlen($datas[$i][1]) > 255  || $datas[$i][1] == '' || checkEmail($datas[$i][1]) == false || // check email
                    strlen($datas[$i][2]) > 14  || $datas[$i][2] == '' || strlen($datas[$i][2]) < 8 || // check tel
                    strlen($datas[$i][3]) > 255  || $datas[$i][3] == ''  // check address
                ) {
                    array_push($err_line_error, $i+2);
                }
                elseif(
                    in_array($datas[$i][1], $emails)
                )
                {
                    array_push($err_line_exist, $i+2);
                }
                else{
                    $newData = [
                        'customer_name' => $datas[$i][0],
                        'email' => $datas[$i][1],
                        'tel_num' => $datas[$i][2],
                        'address' => $datas[$i][3],
                    ];
                    $this->model->create($newData);
                    $numSuccess++;
                }
            }

            $message = '';
            $lenLineExist = count($err_line_exist);
            $lenLineError = count($err_line_error);
            $strLineExist = implode(",", $err_line_exist);
            $strLineError = implode(",", $err_line_error);
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


//        if($request->hasFile('file')){
//
//            $ext = $request->file('file')->getClientOriginalExtension();
//
//            if($ext !== 'csv') {
//                return response()->json([
//                    'status' => 422,
//                    'message' => 'File không đúng định dạng'
//                ]);
//            }
//
//            $datasCsv = convertCsvToArray($request->file('file'));
//
//            if($datasCsv[0] === false) {
//
//                return response()->json([
//                    'status' => 401,
//                    'message' => $datasCsv[1]
//                ]);
//            }
//
//            $datas = $datasCsv[1];
//
//            $customers = Customer::all();
//            $emails = $customers->pluck('email')->toArray();
//
//            $err_line_data = [];
//            $err_line_exist = [];
//            $numSuccess = 0;
//            $len = count($datas);
//
//            if($len > 0) {
//                $j = 0;
//                for($i = 0; $i < $len; $i++){
//                    if ($j === 0) {
//                        $j++;
//                        continue;
//                    }
//                    if(
//                        in_array($datas[$i]['email'], $emails)
//                    )
//                    {
//                        array_push($err_line_exist, $i+1);
//                    }
//                    else if(
//                        !isset($datas[$i]['tel_num']) || strlen($datas[$i]['tel_num']) > 14  || $datas[$i]['tel_num'] == '' || strlen($datas[$i]['tel_num']) < 8  ||
//                        !isset($datas[$i]['address']) || strlen($datas[$i]['address']) > 255 || $datas[$i]['address'] == '' ||
//                        !isset($datas[$i]['email']) ||  strlen($datas[$i]['email']) > 255 || $datas[$i]['email'] == '' || checkEmail($datas[$i]['email']) == false ||
//                        !isset($datas[$i]['customer_name']) ||  strlen($datas[$i]['customer_name']) > 255 || $datas[$i]['customer_name'] == ''
//                    ) {
//                        array_push($err_line_data, $i+1);
//
//                    }
//                    else{
//                        Customer::create($datas[$i]);
//                        $numSuccess++;
//                    }
//
//                }
//            }
//            else{
//
//                return response()->json([
//                    'status' => 401,
//                    'message' => 'Không có dữ liệu.'
//                ]);
//            }
//            $message = '';
//            $lenLineExist = count($err_line_exist);
//            $lenLineError = count($err_line_data);
//            $strLineExist = implode(",", $err_line_exist);
//            $strLineError = implode(",", $err_line_data);
//            if($lenLineExist > 0) {
//
//                $message.= 'Dữ liệu đã tồn tại trong hệ thống ở dòng '.$strLineExist .'. ';
//            }
//            if($lenLineError) {
//
//                $message.= 'Dữ liệu dòng '.$strLineError.' không hợp lệ. ';
//            }
//            $message.= 'Lưu thành công '.$numSuccess.' khách hàng.';
//
//            return response()->json([
//                'status' => 200,
//                'message' => $message,
//                'customers' => $datas,
//            ]);
//        }

        return response()->json([
            'status' => 401,
            'message' => 'Không thấy file'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = $this->model->orderBy('customer_id', 'DESC')->paginate(10);

        if($customers){

            return response()->json([
                'status' => 200,
                'customers' => $customers
            ]);
        }

        return response()->json([
            'status' => 500,
            'customers' => [],
            'message' => 'Lỗi, thử lại sau!'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {
        $customers = $this->model->Name($request)
            ->Email($request)
            ->IsActive($request)
            ->Address($request)
            ->orderBy('customer_id', 'DESC')
            ->paginate(10);

        $customers->appends(['customer_name' => $request->input('customer_name')]);
        $customers->appends(['email' => $request->input('email')]);
        $customers->appends(['address' => $request->input('address')]);
        $customers->appends(['is_active' => $request->input('is_active')]);

        if($customers){

            return response()->json([
                'status' => 200,
                'customers' => $customers
            ]);
        }

        return response()->json([
            'status' => 500,
            'customers' => [],
            'message' => 'Lỗi thử lại sau'
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
            if($this->model->create($data)){

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
        $customer = $this->model->find($id);
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
        $customer = $this->model->find($id);
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

            $arrayTelNum = $this->model->whereNotIn('customer_id', [$id])->pluck('tel_num')->toArray();

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
                if($this->model->where('customer_id', $id)->update($data)){

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
