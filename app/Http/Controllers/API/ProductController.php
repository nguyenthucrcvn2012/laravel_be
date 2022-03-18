<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProductController extends Controller
{

    public $model;
    public function __construct(Product $product) {
        $this->model = $product;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request) {

        $products = $this->model->ProductName($request)
            ->ProductPrice($request)
            ->IsSales($request)
            ->orderBy('created_at', 'DESC')
            ->where('is_delete', 0)
            ->paginate(10);

//        $products->appends(['product_name' => $request->input('product_name')]);
//        $products->appends(['is_sales' => $request->input('is_sales')]);

        if($products){

            return response()->json([
                'status' => 200,
                'products' => $products
            ]);
        }

        return response()->json([
            'status' => 500,
            'message' => 'Lỗi thử lại sau'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = $this->model
            ->where('is_delete', 0)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        if($products->count() > 0){
            return response()->json([
                'status' => 200,
                'products' => $products
            ]);
        };
        return response()->json([
            'status' => 401,
            'products' => [],
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
            'product_name' => 'required|max:255|min:6',
            'product_price' => 'required|numeric|min:0|digits_between:1,11',
            'is_sales' => 'required',
        ]);

        if($validator->fails()){

            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        }

        $data = [
            'product_id' => getIdProduct($request->product_name),
            'product_name' => $request->product_name,
            'product_price' => $request->product_price,
            'is_sales' => $request->is_sales,
            'description' => $request->description
        ];

        if($request->hasFile('product_image')) {

            $validator = Validator::make($request->all(), [
                'product_image' => 'image|mimes:jpg,jpeg,png|max:2048',
            ]);
//            |dimensions:max_width=1024,max_height=1024

            if($validator->fails()){

                return response()->json([
                    'validation_errors' => $validator->messages()
                ]);
            }

            $file = $request->file('product_image');
            $file_name = rand(5,100).'-'.time().'.'.$file->getClientOriginalExtension();
            $request->file('product_image')->move('uploads/products/', $file_name);
            $data = $data + array('product_image' => $file_name);
        }

        if(Product::create($data)){

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
        $product = $this->model->find($id);
        if($product) {

            return response()->json([
                'status' => 200,
                'product' => $product
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

        if($request->hasFile('product_image')) {
            return response()->json([
                'status' => 200,
                'message' => 'Có hình',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|max:255|min:6',
            'product_price' => 'required|numeric|min:0|digits_between:1,11',
            'is_sales' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'validation_errors' => $validator->messages(),
                'product_name' => $request->product_name
            ]);
        }
//        else {
            $product = $this->model->find($id);
            if($product){
                $data = [
                    'product_name' => $request->input('product_name'),
                    'product_price' => $request->input('product_price'),
                    'is_sales' => $request->input('is_sales'),
                    'description' => $request->input('description')
                ];
//                if($request->hasFile('product_image')) {
//                    return response()->json([
//                        'status' => 200,
//                        'message' => 'Có hình',
//                    ]);
//
//                    $validator = Validator::make($request->all(), [
//                        'product_image' => 'image|mimes:jpg,jpeg,png|max:2048|dimensions:max_width=1024,max_height=1024',
//                    ]);
//
//                    if($validator->fails()){
//
//                        return response()->json([
//                            'validation_errors' => $validator->messages()
//                        ]);
//                    }
//
//                    $file = $request->file('product_image');
//                    $file_name = rand(5,100).'-'.time().'.'.$file->getClientOriginalExtension();
//                    $file->move('uploads/products/', $file_name);
//                    $data = $data + array('product_image' => $file_name);
////
//                    //xóa hình cũ
//                    if (File::exists(public_path() . "/uploads/products/" . $product->product_image)) {
//                        File::delete(public_path() . "/uploads/products/" . $product->product_image);
//                    }
//                }
                if(Product::where('product_id', $id)->update($data)){

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
            return response()->json([
                'status' => 401,
                'message' => 'Không tìm thây sản phẩm!',
            ]);
//        }




    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $query = $this->model->where('product_id', $id)->update(['is_delete' => 1]);
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
