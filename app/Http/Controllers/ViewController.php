<?php

namespace App\Http\Controllers;

use App\View;
use App\Product;
use App\Blog;
use App\Entity;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    /**
     * Explicit constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

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
        }

        $productEntity = Entity::where('name', $product->getTable())->first();

        $request->request->add([
            'entity' => $productEntity->id,
            'entity_id' => $product->id,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
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
     *     path="/api/blogview",
     *     operationId="blogViewAdd",
     *     tags={"View"},
     *     summary="Adds view to blog",
     *     description="Adds view to blog.",
     *     @OA\Parameter(
     *         name="blog_id",
     *         in="query",
     *         description="The blog id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the blog view create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog view create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogViewAdd(Request $request)
    {
        $blog = Blog::where('id', $request->blog_id)->whereNull('deleted_at')->first();
        if (empty($blog)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid blog id',
            ], 400);
        }

        $blogEntity = Entity::where('name', $blog->getTable())->first();

        $request->request->add([
            'entity' => $blogEntity->id,
            'entity_id' => $blog->id,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
        ]);

        $view = View::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'View added',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/blogview/{blog_id}",
     *     operationId="blogViewGet",
     *     tags={"View"},
     *     summary="Retrieves all blog views given the blog id",
     *     description="Retrieves all blog views given the blog id.",
     *     @OA\Parameter(
     *         name="blog_id",
     *         in="path",
     *         description="The blog id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns blog view total count",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog view get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogViewGet(int $blog_id)
    {
        $blog = Blog::where('id', $blog_id)->whereNull('deleted_at')->first();
        if (empty($blog)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid blog id',
            ], 400);
        }

        $blogEntity = Entity::where('name', $blog->getTable())->first();

        $viewList = View::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->get();

        return response()->json([
            'count' => count($viewList),
        ], 200);
    }
}

