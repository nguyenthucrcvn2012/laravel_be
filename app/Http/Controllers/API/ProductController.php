<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::orderBy('product_id', 'DESC')->paginate(10);

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
                'product_id' => 14,
                'product_name' => $request->product_name,
                'product_price' => $request->product_price,
                'is_sales' => $request->is_sales,
                'description' => $request->description
            ];
            if(Product::create($data)){

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

        $product = Product::find($id);
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
                if(Product::where('product_id', $product->product_id)->update($data)){

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
        //
    }
}
