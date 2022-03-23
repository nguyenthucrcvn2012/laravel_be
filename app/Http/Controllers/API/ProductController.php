<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use File;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|max:255|min:6',
            'product_price' => 'required|numeric|min:0|digits_between:1,12',
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
            'product_price' => (float)$request->product_price,
            'is_sales' => $request->is_sales,
            'description' => $request->description
        ];

        if($request->hasFile('product_image')) {

            $validator = Validator::make($request->all(), [
                'product_image' => 'image|mimes:jpg,jpeg,png|max:1024',
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
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'product_name' => 'required|max:255|min:6',
            'product_price' => 'required|numeric|min:0|digits_between:1,12',
            'is_sales' => 'required',
        ]);

        if($validator->fails()){

            return response()->json([
                'validation_errors' => $validator->messages()
            ]);
        }

        $product = $this->model->find($id);

        if($product) {
            $data = [
                'product_name' => $request->input('product_name'),
                'product_price' => (float) $request->input('product_price'),
                'is_sales' => $request->input('is_sales'),
                'description' => $request->input('description')
            ];
            if($request->file('product_image')) {
                $validator = Validator::make($request->all(), [
                    'product_image' => 'image|mimes:jpg,jpeg,png|max:1024',
                ]);
                if ($validator->fails()) {

                    return response()->json([
                        'validation_errors' => $validator->messages()
                    ]);
                }
                //xóa hình cũ
                if (File::exists($product->product_image)) {
                    File::delete($product->product_image);
                }

                $file = $request->file('product_image');
                $fileName = rand(5,100).'-'.time().'.'.$file->getClientOriginalExtension();
                $file->move('uploads/products/', $fileName);
                $data = $data + array('product_image' => $fileName);
            }
            if($request->is_delete_image == true) {
                $data = $data + array('product_image' => null);
            }

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
            'status' => 404,
            'message' => 'Không tìm thấy dữ liệu!'
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
        $product = $this->model->find($id);
        if($product) {
            $query = $this->model->where('product_id', $id)->update(['is_delete' => 1]);
            if($query){

                return response()->json([
                    'status' => 200,
                    'message' => 'Xóa thành công!'
                ]);
            }
            else{

                return response()->json([
                    'status' => 500,
                    'message' => 'Thử lại sau!'
                ]);
            }
        }

        return response()->json([
            'status' => 404,
            'message' => 'Không tìm thấy dữ liệu!'
        ]);

    }
}
