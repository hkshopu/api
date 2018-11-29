<?php

namespace App\Http\Controllers;

use App\Following;
use App\Product;
use App\Image;
use App\Entity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FollowingController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/productfollowing",
     *     operationId="/api/productfollowing#post",
     *     tags={"Following"},
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

        if (!empty(Following::where('entity', $productEntity->id)->where('entity_id', $product->id)->where('user_id', $request->user_id)->whereNull('deleted_at')->first())) {
            return response([
                'success' => false,
                'message' => 'Following already exists',
            ], 400);
        }

        $request->request->add([
            'entity' => $productEntity->id,
            'entity_id' => $product->id,
        ]);

        $following = Following::create($request->all());
        return response([
            'success' => true,
            'message' => 'Following added',
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/productfollowing/{product_id}",
     *     operationId="/api/productfollowing/{product_id}#get",
     *     tags={"Following"},
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
            return response([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $productEntity = Entity::where('name', $product->getTable())->first();

        $followingList = Following::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->get();

        return response()->json($followingList);
    }

    /**
     * @OA\Delete(
     *     path="/api/productfollowing/{id}",
     *     operationId="/api/productfollowing/{id}#delete",
     *     tags={"Following"},
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
        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => 1,
        ]);

        $product = new Product();
        $productEntity = Entity::where('name', $product->getTable())->first();

        if (empty(Following::where('id', $id)->whereNull('deleted_at')->first())) {
            return response([
                'success' => false,
                'message' => 'Invalid following id',
            ], 400);
        } else if (empty(Following::where('id', $id)->where('entity', $productEntity->id)->whereNull('deleted_at')->first())) {
            return response([
                'success' => false,
                'message' => 'Invalid following id for the product',
            ], 400);
        }

        $following = Following::where('id', $id)->where('entity', $productEntity->id)->whereNull('deleted_at')->first();
        $following->update($request->all());

        return response([
            'success' => true,
            'message' => 'Following removed',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/imagefollowing",
     *     operationId="/api/imagefollowing#post",
     *     tags={"Following"},
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
        $request->request->add([
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $image = Image::where('id', $request->image_id)->whereNull('deleted_at')->first();
        if (empty($image)) {
            return response([
                'success' => false,
                'message' => 'Invalid image id',
            ], 400);
        } else if (empty($request->user_id) || $request->user_id < 1) {
            return response([
                'success' => false,
                'message' => 'Invalid user id',
            ], 400);
        }

        $imageEntity = Entity::where('name', $image->getTable())->first();

        if (!empty(Following::where('entity', $imageEntity->id)->where('entity_id', $image->id)->where('user_id', $request->user_id)->whereNull('deleted_at')->first())) {
            return response([
                'success' => false,
                'message' => 'Following already exists',
            ], 400);
        }

        $request->request->add([
            'entity' => $imageEntity->id,
            'entity_id' => $image->id,
        ]);

        $following = Following::create($request->all());
        return response([
            'success' => true,
            'message' => 'Following added',
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/imagefollowing/{image_id}",
     *     operationId="/api/imagefollowing/{image_id}#get",
     *     tags={"Following"},
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
            return response([
                'success' => false,
                'message' => 'Invalid image id',
            ], 400);
        }

        $imageEntity = Entity::where('name', $image->getTable())->first();

        $followingList = Following::where('entity', $imageEntity->id)->where('entity_id', $image->id)->whereNull('deleted_at')->get();

        return response()->json($followingList);
    }

    /**
     * @OA\Delete(
     *     path="/api/imagefollowing/{id}",
     *     operationId="/api/imagefollowing/{id}#delete",
     *     tags={"Following"},
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
        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => 1,
        ]);

        $request->request->add([
            'deleted_by' => 1,
        ]);

        $image = new Image();
        $imageEntity = Entity::where('name', $image->getTable())->first();

        if (empty(Following::where('id', $id)->whereNull('deleted_at')->first())) {
            return response([
                'success' => false,
                'message' => 'Invalid following id',
            ], 400);
        } else if (empty(Following::where('id', $id)->where('entity', $imageEntity->id)->whereNull('deleted_at')->first())) {
            return response([
                'success' => false,
                'message' => 'Invalid following id for the image',
            ], 400);
        }

        $following = Following::where('id', $id)->where('entity', $imageEntity->id)->whereNull('deleted_at')->first();
        $following->update($request->all());

        return response([
            'success' => true,
            'message' => 'Following removed',
        ], 200);
    }
}

