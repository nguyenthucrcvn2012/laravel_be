<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Cassandra\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
{
    private $modal;
    public function __construct(Product $product) {
        $this->modal = $product;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products =  $this->modal->orderBy('created_at', 'DESC')->where('is_delete', 0)->paginate(10);

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
            'product_name' => 'required|max:255',
            'product_price' => 'required|numeric|min:0',
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
                'product_image' => 'mimes:jpg,jpeg,png|max:1024',
            ]);

            if($validator->fails()){

                return response()->json([
                    'validation_errors' => $validator->messages()
                ]);
            }

            $file = $request->file('product_image');
            $file_name = rand(5,100).'-'.time().'.'.$file->getClientOriginalExtension();
            $file->move('uploads/products/', $file_name);
            $data = $data + array('product_image' => $file_name);
        }

        if($this->modal->create($data)){

            return response()->json([
                'status' => 200,
                'message' => 'Thêm mới thành công',
            ]);
        }
        else {

            return response()->json([
                'status' => 401,
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

        $product = $this->modal->find($id);
        if($product) {

            $validator = Validator::make($request->all(), [
                'product_name' => 'required|max:255',
                'product_price' => 'required',
                'is_sales' => 'required',
            ]);

            if($validator->fails()){

                return response()->json([
                    'validation_errors' => $validator->messages()
                ]);
            }
            else {
                $data = [
                    'product_name' => $request->product_name,
                    'product_price' => $request->product_price,
                    'is_sales' => $request->is_sales,
                    'description' => $request->description
                ];
                if($this->modal->where('product_id', $product->product_id)->update($data)){

                    return response()->json([
                        'status' => 200,
                        'message' => 'Cập nhật thành công',
                    ]);
                }
                else {

                    return response()->json([
                        'status' => 401,
                        'message' => 'Vui lòng thử lại sau!',
                    ]);
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $query = $this->modal->where('product_id', $id)->update(['is_delete' => 1]);
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
