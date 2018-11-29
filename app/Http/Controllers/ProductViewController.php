<?php

namespace App\Http\Controllers;

use App\View;
use App\Product;
use App\Entity;
use Illuminate\Http\Request;

class ProductViewController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/productview",
     *     operationId="/api/productview#post",
     *     tags={"View"},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="The user id (Just enter any random integer, yah as in random ;)",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the product view create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product view create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function create(Request $request)
    {
        $request->request->add([
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $product = Product::where('id', $request->product_id)->whereNull('deleted_at')->first();
        if (empty($product)) {
            return response([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        } else if (empty($request->user_id) || $request->user_id < 1) {
            return response([
                'success' => false,
                'message' => 'Invalid user id',
            ], 400);
        }

        $productEntity = Entity::where('name', $product->getTable())->first();

        $request->request->add([
            'entity' => $productEntity->id,
            'entity_id' => $product->id,
        ]);

        $view = View::create($request->all());
        return response([
            'success' => true,
            'message' => 'View added',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/productview/{product_id}",
     *     operationId="/api/productview/{product_id}#get",
     *     tags={"View"},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns product view total count",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product view get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function fetch(int $product_id)
    {
        $product = Product::where('id', $product_id)->whereNull('deleted_at')->first();
        if (empty($product)) {
            return response([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $productEntity = Entity::where('name', $product->getTable())->first();

        $viewList = View::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->get();

        return response()->json([
            'count' => count($viewList),
        ], 200);
    }
}

