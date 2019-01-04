<?php

namespace App\Http\Controllers;

use App\View;
use App\Product;
use App\News;
use App\Entity;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/productview",
     *     operationId="productViewAdd",
     *     tags={"View"},
     *     summary="Adds view to product",
     *     description="Adds view to product.",
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
    public function productViewAdd(Request $request)
    {
        $product = Product::where('id', $request->product_id)->whereNull('deleted_at')->first();
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        } else if (empty($request->user_id) || $request->user_id < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user id',
            ], 400);
        }

        $productEntity = Entity::where('name', $product->getTable())->first();

        $request->request->add([
            'entity' => $productEntity->id,
            'entity_id' => $product->id,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $view = View::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'View added',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/productview/{product_id}",
     *     operationId="productViewGet",
     *     tags={"View"},
     *     summary="Retrieves all product views given the product id",
     *     description="Retrieves all product views given the product id.",
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
    public function productViewGet(int $product_id)
    {
        $product = Product::where('id', $product_id)->whereNull('deleted_at')->first();
        if (empty($product)) {
            return response()->json([
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

    /**
     * @OA\Post(
     *     path="/api/newsview",
     *     operationId="newsViewAdd",
     *     tags={"View"},
     *     summary="Adds view to news",
     *     description="Adds view to news.",
     *     @OA\Parameter(
     *         name="news_id",
     *         in="query",
     *         description="The news id",
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
     *         description="Returns the news view create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news view create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsViewAdd(Request $request)
    {
        $news = News::where('id', $request->news_id)->whereNull('deleted_at')->first();
        if (empty($news)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid news id',
            ], 400);
        } else if (empty($request->user_id) || $request->user_id < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user id',
            ], 400);
        }

        $newsEntity = Entity::where('name', $news->getTable())->first();

        $request->request->add([
            'entity' => $newsEntity->id,
            'entity_id' => $news->id,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $view = View::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'View added',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/newsview/{news_id}",
     *     operationId="newsViewGet",
     *     tags={"View"},
     *     summary="Retrieves all news views given the news id",
     *     description="Retrieves all news views given the news id.",
     *     @OA\Parameter(
     *         name="news_id",
     *         in="path",
     *         description="The news id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns news view total count",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news view get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsViewGet(int $news_id)
    {
        $news = News::where('id', $news_id)->whereNull('deleted_at')->first();
        if (empty($news)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid news id',
            ], 400);
        }

        $newsEntity = Entity::where('name', $news->getTable())->first();

        $viewList = View::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->get();

        return response()->json([
            'count' => count($viewList),
        ], 200);
    }
}

