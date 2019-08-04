<?php

namespace App\Http\Controllers;

use App\Rating;
use App\Shop;
use App\User;
use App\Product;
use App\Entity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RatingController extends Controller
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
     *     path="/api/shoprating",
     *     operationId="shopRatingAdd",
     *     tags={"Rating"},
     *     summary="Adds rating to shop",
     *     description="Adds rating to shop.",
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
     *         name="shop_id",
     *         in="query",
     *         description="The shop id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         description="The shop rating, scaling from 1 to 5 only",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the shop rating create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop rating create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopRatingAdd(Request $request)
    {
        $shopQuery = \DB::table('shop')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('shop.*')
            ->where('shop.id', $request->shop_id)
            ->whereNull('shop.deleted_at');

        if ($request->filter_inactive == true) {
            $shopQuery
                ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                ->whereNotNull('shop_payment_method_map.id')
                ->whereNull('shop_payment_method_map.deleted_at')
                ->groupBy('shop.id')
                ->whereNull('user.deleted_at');
        }

        $shop = $shopQuery->first();

        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $shop = Shop::where('id', $shop->id)->whereNull('deleted_at')->first();

        if ($request->rating < 1 || $request->rating > 5) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid rating',
            ], 400);
        }

        $shopEntity = Entity::where('name', $shop->getTable())->first();
        $rating = Rating::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

        if (!empty($rating)) {
            self::shopRatingDelete($rating->id, $request);
        }

        $request->request->add([
            'entity' => $shopEntity->id,
            'entity_id' => $shop->id,
            'rate' => $request->rating,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $rating = Rating::create($request->only([
            'entity',
            'entity_id',
            'rate',
            'created_by',
            'updated_by',
        ]));

        return response()->json(app('App\Http\Controllers\ShopController')->shopGet($shop->id, $request)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/shoprating/{shop_id}",
     *     operationId="shopRatingGet",
     *     tags={"Rating"},
     *     summary="Retrieves all shop ratings given the shop id",
     *     description="Retrieves all shop ratings given the shop id.",
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
     *         name="shop_id",
     *         in="path",
     *         description="The shop id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns all shop rating",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop rating get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopRatingGet(int $shop_id, Request $request)
    {
        $shopQuery = \DB::table('shop')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('shop.*')
            ->where('shop.id', $shop_id)
            ->whereNull('shop.deleted_at');

        if ($request->filter_inactive == true) {
            $shopQuery
                ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                ->whereNotNull('shop_payment_method_map.id')
                ->whereNull('shop_payment_method_map.deleted_at')
                ->groupBy('shop.id')
                ->whereNull('user.deleted_at');
        }

        $shop = $shopQuery->first();

        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $shop = Shop::where('id', $shop->id)->whereNull('deleted_at')->first();

        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $ratingList = Rating::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        return response()->json($ratingList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/shoprating/{id}",
     *     operationId="shopRatingDelete",
     *     tags={"Rating"},
     *     summary="Removes user rating to shop",
     *     description="Removes user rating to shop.",
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
     *         name="id",
     *         in="path",
     *         description="The shop rating id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the shop rating delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop rating delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopRatingDelete($id, Request $request)
    {
        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        if (empty(Rating::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid rating id',
            ], 400);
        } else if (empty(Rating::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid rating id for the shop',
            ], 400);
        }

        $rating = Rating::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first();

        $shopQuery = \DB::table('shop')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('shop.*')
            ->where('shop.id', $rating->entity_id)
            ->whereNull('shop.deleted_at');

        if ($request->filter_inactive == true) {
            $shopQuery
                ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                ->whereNotNull('shop_payment_method_map.id')
                ->whereNull('shop_payment_method_map.deleted_at')
                ->groupBy('shop.id')
                ->whereNull('user.deleted_at');
        }

        $shop = $shopQuery->first();

        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $shop = Shop::where('id', $shop->id)->whereNull('deleted_at')->first();

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);
        
        $rating->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Shop rating removed',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/productrating",
     *     operationId="productRatingAdd",
     *     tags={"Rating"},
     *     summary="Adds rating to product",
     *     description="Adds rating to product.",
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
     *         name="product_id",
     *         in="query",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="rating",
     *         in="query",
     *         description="The product rating, scaling from 1 to 5 only",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the product rating create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product rating create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productRatingAdd(Request $request)
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
                ->whereNull('shop_payment_method_map.deleted_at')
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

        if ($request->rating < 1 || $request->rating > 5) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid rating',
            ], 400);
        }

        $productEntity = Entity::where('name', $product->getTable())->first();
        $rating = Rating::where('entity', $productEntity->id)->where('entity_id', $product->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

        if (!empty($rating)) {
            self::productRatingDelete($rating->id, $request);
        }

        $request->request->add([
            'entity' => $productEntity->id,
            'entity_id' => $product->id,
            'rate' => $request->rating,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $rating = Rating::create($request->only([
            'entity',
            'entity_id',
            'rate',
            'created_by',
            'updated_by',
        ]));

        return response()->json(app('App\Http\Controllers\ProductController')->productGet($product->id, $request)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/productrating/{product_id}",
     *     operationId="productRatingGet",
     *     tags={"Rating"},
     *     summary="Retrieves all product ratings given the product id",
     *     description="Retrieves all product ratings given the product id.",
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
     *         name="product_id",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns all product rating",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product rating get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productRatingGet(int $product_id, Request $request)
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
                ->whereNull('shop_payment_method_map.deleted_at')
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

        $ratingList = Rating::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        return response()->json($ratingList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/productrating/{id}",
     *     operationId="productRatingDelete",
     *     tags={"Rating"},
     *     summary="Removes user rating to product",
     *     description="Removes user rating to product.",
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
     *         name="id",
     *         in="path",
     *         description="The product rating id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the product rating delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product rating delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productRatingDelete($id, Request $request)
    {
        $product = new Product();
        $productEntity = Entity::where('name', $product->getTable())->first();

        if (empty(Rating::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid rating id',
            ], 400);
        } else if (empty(Rating::where('id', $id)->where('entity', $productEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid rating id for the product',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $rating = Rating::where('id', $id)->where('entity', $productEntity->id)->whereNull('deleted_at')->first();
        $rating->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Product rating removed',
        ], 200);
    }
}

 