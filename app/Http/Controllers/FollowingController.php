<?php

namespace App\Http\Controllers;

use App\Following;
use App\Product;
use App\Image;
use App\Shop;
use App\Entity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FollowingController extends Controller
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
     *     path="/api/productfollowing",
     *     operationId="productFollowingAdd",
     *     tags={"Following"},
     *     summary="Adds follower to product",
     *     description="Adds follower to product.",
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
     *     @OA\Response(
     *         response="201",
     *         description="Returns the product following create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product following create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productFollowingAdd(Request $request)
    {
        $product = Product::where('id', $request->product_id)->whereNull('deleted_at')->first();
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $productEntity = Entity::where('name', $product->getTable())->first();
        $following = Following::where('entity', $productEntity->id)->where('entity_id', $product->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

        if (!empty($following)) {
            return response()->json([
                'success' => false,
                'message' => 'Product already followed',
            ], 400);
        }

        $request->request->add([
            'entity' => $productEntity->id,
            'entity_id' => $product->id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $following = Following::create($request->only([
            'entity',
            'entity_id',
            'created_by',
            'updated_by',
        ]));

        return response()->json(app('App\Http\Controllers\ProductController')->productGet($product->id, $request)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/productfollowing/{product_id}",
     *     operationId="productFollowingGet",
     *     tags={"Following"},
     *     summary="Retrieves all product followers given the product id",
     *     description="Retrieves all product followers given the product id.",
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
     *         description="Returns all product following",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product following get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productFollowingGet(int $product_id)
    {
        $product = Product::where('id', $product_id)->whereNull('deleted_at')->first();
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $productEntity = Entity::where('name', $product->getTable())->first();

        $followingList = Following::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
        foreach ($followingList as $key => $following) {
            $followingList[$key]['user_id'] = $following['created_by'];
            unset($followingList[$key]['created_by']);
        }

        return response()->json($followingList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/productfollowing/{product_id}",
     *     operationId="productFollowingDelete",
     *     tags={"Following"},
     *     summary="Unfollows user to product",
     *     description="Unfollows user to product.",
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
     *         description="Returns the product following delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product following delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productFollowingDelete($product_id, Request $request)
    {
        $product = Product::where('id', $request->product_id)->whereNull('deleted_at')->first();
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $productEntity = Entity::where('name', $product->getTable())->first();
        $following = Following::where('entity', $productEntity->id)->where('entity_id', $product->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

        if (empty($following)) {
            return response()->json([
                'success' => false,
                'message' => 'Nothing to unfollow',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $following->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json(app('App\Http\Controllers\ProductController')->productGet($product->id, $request)->getData(), 201);
    }

    /**
     * @OA\Post(
     *     path="/api/imagefollowing",
     *     operationId="imageFollowingAdd",
     *     tags={"Following"},
     *     summary="Adds follower to image",
     *     description="Adds follower to image.",
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
     *         name="image_id",
     *         in="query",
     *         description="The image id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the image following create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the image following create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function imageFollowingAdd(Request $request)
    {
        $image = Image::where('id', $request->image_id)->whereNull('deleted_at')->first();
        if (empty($image)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image id',
            ], 400);
        }

        $imageEntity = Entity::where('name', $image->getTable())->first();
        $following = Following::where('entity', $imageEntity->id)->where('entity_id', $image->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

        if (!empty($following)) {
            return response()->json([
                'success' => false,
                'message' => 'Image already followed',
            ], 400);
        }

        $request->request->add([
            'entity' => $imageEntity->id,
            'entity_id' => $image->id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $following = Following::create($request->only([
            'entity',
            'entity_id',
            'created_by',
            'updated_by',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Image followed',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/imagefollowing/{image_id}",
     *     operationId="imageFollowingGet",
     *     tags={"Following"},
     *     summary="Retrieves all image followers given the image id",
     *     description="Retrieves all image followers given the image id.",
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
     *         name="image_id",
     *         in="path",
     *         description="The image id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns all image following",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the image following get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function imageFollowingGet(int $image_id)
    {
        $image = Image::where('id', $image_id)->whereNull('deleted_at')->first();
        if (empty($image)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image id',
            ], 400);
        }

        $imageEntity = Entity::where('name', $image->getTable())->first();

        $followingList = Following::where('entity', $imageEntity->id)->where('entity_id', $image->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
        foreach ($followingList as $key => $following) {
            $followingList[$key]['user_id'] = $following['created_by'];
            unset($followingList[$key]['created_by']);
        }

        return response()->json($followingList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/imagefollowing/{image_id}",
     *     operationId="imageFollowingDelete",
     *     tags={"Following"},
     *     summary="Unfollows user to image",
     *     description="Unfollows user to image.",
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
     *         name="image_id",
     *         in="path",
     *         description="The image id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the image following delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the image following delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function imageFollowingDelete($image_id, Request $request)
    {
        $image = Image::where('id', $request->image_id)->whereNull('deleted_at')->first();
        if (empty($image)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image id',
            ], 400);
        }

        $imageEntity = Entity::where('name', $image->getTable())->first();
        $following = Following::where('entity', $imageEntity->id)->where('entity_id', $image->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

        if (empty($following)) {
            return response()->json([
                'success' => false,
                'message' => 'Nothing to unfollow',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $following->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Image unfollowed',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/shopfollowing",
     *     operationId="shopFollowingAdd",
     *     tags={"Following"},
     *     summary="Adds follower to shop",
     *     description="Adds follower to shop.",
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
     *     @OA\Response(
     *         response="201",
     *         description="Returns the shop following create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop following create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopFollowingAdd(Request $request)
    {
        $shop = Shop::where('id', $request->shop_id)->whereNull('deleted_at')->first();
        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $user = User::where('id', $shop->user_id)->whereNull('deleted_at')->first();
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Shop inactive',
            ], 400);
        }

        $shopEntity = Entity::where('name', $shop->getTable())->first();
        $following = Following::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

        if (!empty($following)) {
            return response()->json([
                'success' => false,
                'message' => 'Shop already followed',
            ], 400);
        }

        $request->request->add([
            'entity' => $shopEntity->id,
            'entity_id' => $shop->id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $following = Following::create($request->only([
            'entity',
            'entity_id',
            'created_by',
            'updated_by',
        ]));

        return response()->json(app('App\Http\Controllers\ShopController')->shopGet($shop->id, $request)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/shopfollowing/{shop_id}",
     *     operationId="shopFollowingGet",
     *     tags={"Following"},
     *     summary="Retrieves all shop followers given the shop id",
     *     description="Retrieves all shop followers given the shop id.",
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
     *         description="Returns all shop following",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop following get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopFollowingGet(int $shop_id)
    {
        $shop = Shop::where('id', $shop_id)->whereNull('deleted_at')->first();
        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $user = User::where('id', $shop->user_id)->whereNull('deleted_at')->first();
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Shop inactive',
            ], 400);
        }

        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $followingList = Following::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
        foreach ($followingList as $key => $following) {
            $followingList[$key]['user_id'] = $following['created_by'];
            unset($followingList[$key]['created_by']);
        }

        return response()->json($followingList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/shopfollowing/{shop_id}",
     *     operationId="shopFollowingDelete",
     *     tags={"Following"},
     *     summary="Unfollows user to shop",
     *     description="Unfollows user to shop.",
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
     *         description="Returns the shop following delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop following delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopFollowingDelete($shop_id, Request $request)
    {
        $shop = Shop::where('id', $request->shop_id)->whereNull('deleted_at')->first();
        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $user = User::where('id', $shop->user_id)->whereNull('deleted_at')->first();
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Shop inactive',
            ], 400);
        }

        $shopEntity = Entity::where('name', $shop->getTable())->first();
        $following = Following::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

        if (empty($following)) {
            return response()->json([
                'success' => false,
                'message' => 'Nothing to unfollow',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $following->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json(app('App\Http\Controllers\ShopController')->shopGet($shop->id, $request)->getData(), 201);
    }
}

