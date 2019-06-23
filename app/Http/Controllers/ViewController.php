<?php

namespace App\Http\Controllers;

use App\View;
use App\Product;
use App\Blog;
use App\Order;
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
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $request->product_id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                ->whereNotNull('shop_payment_method_map.id')
                ->groupBy('product.id')
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();

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
    public function productViewGet(int $product_id, Request $request)
    {
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $product_id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                ->whereNotNull('shop_payment_method_map.id')
                ->groupBy('product.id')
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();

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
        $blogQuery = \DB::table('blog')
            ->leftJoin('shop', 'shop.id', '=', 'blog.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('blog.*')
            ->where('blog.id', $request->blog_id)
            ->whereNull('blog.deleted_at');

        if ($request->filter_inactive == true) {
            $blogQuery
                ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                ->whereNotNull('shop_payment_method_map.id')
                ->groupBy('blog.id')
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $blog = $blogQuery->first();

        if (empty($blog)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid blog id',
            ], 400);
        }

        $blog = Blog::where('id', $blog->id)->whereNull('deleted_at')->first();

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
    public function blogViewGet(int $blog_id, Request $request)
    {
        $blogQuery = \DB::table('blog')
            ->leftJoin('shop', 'shop.id', '=', 'blog.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('blog.*')
            ->where('blog.id', $blog_id)
            ->whereNull('blog.deleted_at');

        if ($request->filter_inactive == true) {
            $blogQuery
                ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                ->whereNotNull('shop_payment_method_map.id')
                ->groupBy('blog.id')
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $blog = $blogQuery->first();

        if (empty($blog)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid blog id',
            ], 400);
        }

        $blog = Blog::where('id', $blog->id)->whereNull('deleted_at')->first();

        $blogEntity = Entity::where('name', $blog->getTable())->first();

        $viewList = View::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->get();

        return response()->json([
            'count' => count($viewList),
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/orderview",
     *     operationId="orderViewAdd",
     *     tags={"View"},
     *     summary="Adds view to order",
     *     description="Adds view to order.",
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         description="The access token for authentication",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="order_id_list",
     *         in="query",
     *         description="The order id (JSON string of order ids. Examples: [123], [123,234])",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the order view create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the order view create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function orderViewAdd(Request $request = null)
    {
        $orderIdList = json_decode(str_replace(' ', '', $request->order_id_list));
        if ($orderIdList == null) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order id list format',
            ], 400);
        }

        $invalidOrderIds = [];

        foreach ($orderIdList as $orderId) {
            $order = Order::where('id', $orderId)->whereNull('deleted_at')->first();

            if (empty($order)) {
                $invalidOrderIds[] = $orderId;
                continue;
            }

            $orderEntity = Entity::where('name', $order->getTable())->first();

            $view = View::where('entity', $orderEntity->id)->where('entity_id', $order->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

            if (!empty($view)) {
                continue;
            }

            $request->request->add([
                'entity' => $orderEntity->id,
                'entity_id' => $order->id,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            $view = View::create($request->only([
                'entity',
                'entity_id',
                'ip_address',
                'created_by',
                'updated_by',
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => 'View added',
            'warning' => 'Except for the following invalid ids: ' . implode(', ', $invalidOrderIds),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orderview/{order_id}",
     *     operationId="orderViewGet",
     *     tags={"View"},
     *     summary="Retrieves all order views given the order id",
     *     description="Retrieves all order views given the order id.",
     *     @OA\Parameter(
     *         name="order_id",
     *         in="path",
     *         description="The order id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns order view total count",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the order view get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function orderViewGet(int $order_id)
    {
        $order = Order::where('id', $order_id)->whereNull('deleted_at')->first();

        if (empty($order)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order id',
            ], 400);
        }

        $orderEntity = Entity::where('name', $order->getTable())->first();

        $viewList = View::where('entity', $orderEntity->id)->where('entity_id', $order->id)->whereNull('deleted_at')->get();

        return response()->json($viewList, 200);
    }
}

