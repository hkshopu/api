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
     * @OA\Post(
     *     path="/api/productfollowing",
     *     operationId="productFollowingAdd",
     *     tags={"Following"},
     *     summary="Adds follower to product",
     *     description="Adds follower to product.",
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
        } else if (empty($request->user_id) || $request->user_id < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user id',
            ], 400);
        }

        $productEntity = Entity::where('name', $product->getTable())->first();

        if (!empty(Following::where('entity', $productEntity->id)->where('entity_id', $product->id)->where('user_id', $request->user_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Following already exists',
            ], 400);
        }

        $request->request->add([
            'entity' => $productEntity->id,
            'entity_id' => $product->id,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $following = Following::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Following added',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/productfollowing/{product_id}",
     *     operationId="productFollowingGet",
     *     tags={"Following"},
     *     summary="Retrieves all product followers given the product id",
     *     description="Retrieves all product followers given the product id.",
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

        return response()->json($followingList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/productfollowing/{id}",
     *     operationId="productFollowingDelete",
     *     tags={"Following"},
     *     summary="Unfollows user to product",
     *     description="Unfollows user to product.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product following id",
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
    public function productFollowingDelete($id, Request $request)
    {
        $product = new Product();
        $productEntity = Entity::where('name', $product->getTable())->first();

        if (empty(Following::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid following id',
            ], 400);
        } else if (empty(Following::where('id', $id)->where('entity', $productEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid following id for the product',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => 1,
        ]);

        $following = Following::where('id', $id)->where('entity', $productEntity->id)->whereNull('deleted_at')->first();
        $following->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Following removed',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/imagefollowing",
     *     operationId="imageFollowingAdd",
     *     tags={"Following"},
     *     summary="Adds follower to image",
     *     description="Adds follower to image.",
     *     @OA\Parameter(
     *         name="image_id",
     *         in="query",
     *         description="The image id",
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
        } else if (empty($request->user_id) || $request->user_id < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user id',
            ], 400);
        }

        $imageEntity = Entity::where('name', $image->getTable())->first();

        if (!empty(Following::where('entity', $imageEntity->id)->where('entity_id', $image->id)->where('user_id', $request->user_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Following already exists',
            ], 400);
        }

        $request->request->add([
            'entity' => $imageEntity->id,
            'entity_id' => $image->id,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $following = Following::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Following added',
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

        return response()->json($followingList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/imagefollowing/{id}",
     *     operationId="imageFollowingDelete",
     *     tags={"Following"},
     *     summary="Unfollows user to image",
     *     description="Unfollows user to image.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The image following id",
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
    public function imageFollowingDelete($id, Request $request)
    {
        $image = new Image();
        $imageEntity = Entity::where('name', $image->getTable())->first();

        if (empty(Following::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid following id',
            ], 400);
        } else if (empty(Following::where('id', $id)->where('entity', $imageEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid following id for the image',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => 1,
        ]);

        $following = Following::where('id', $id)->where('entity', $imageEntity->id)->whereNull('deleted_at')->first();
        $following->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Following removed',
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
     *         name="shop_id",
     *         in="query",
     *         description="The shop id",
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
        } else if (empty($request->user_id) || $request->user_id < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user id',
            ], 400);
        }

        $shopEntity = Entity::where('name', $shop->getTable())->first();

        if (!empty(Following::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->where('user_id', $request->user_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Following already exists',
            ], 400);
        }

        $request->request->add([
            'entity' => $shopEntity->id,
            'entity_id' => $shop->id,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $following = Following::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Following added',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/shopfollowing/{shop_id}",
     *     operationId="shopFollowingGet",
     *     tags={"Following"},
     *     summary="Retrieves all shop followers given the shop id",
     *     description="Retrieves all shop followers given the shop id.",
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

        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $followingList = Following::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        return response()->json($followingList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/shopfollowing/{id}",
     *     operationId="shopFollowingDelete",
     *     tags={"Following"},
     *     summary="Unfollows user to shop",
     *     description="Unfollows user to shop.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The shop following id",
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
    public function shopFollowingDelete($id, Request $request)
    {
        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        if (empty(Following::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid following id',
            ], 400);
        } else if (empty(Following::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid following id for the shop',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => 1,
        ]);

        $following = Following::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first();
        $following->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Following removed',
        ], 200);
    }
}
