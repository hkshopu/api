<?php

namespace App\Http\Controllers;

use App\Image;
use App\Product;
use App\Shop;
use App\Entity;
use Illuminate\Http\Request;
use Cloudder;

class ImageController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/productimage/{id}",
     *     operationId="productImageAdd",
     *     tags={"Image"},
     *     summary="Adds image to the product",
     *     description="Associates the image to the product using the file_url from the image upload endpoint.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="image_url",
     *         in="query",
     *         description="The image url",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the product image add status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product image add failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productImageAdd(int $id, Request $request)
    {
        $product = Product::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $productEntity = Entity::where('name', $product->getTable())->orderBy('id', 'DESC')->first();

        $image = Image::where('entity', $productEntity->id)->where('entity_id', $product->id)->where('sort', '<>', 0)->whereNull('deleted_at')->orderBy('sort', 'DESC')->first();

        $sort = 1;
        if (!empty($image)) {
            $sort = $image->sort + 1;
        }

        $request->request->add([
            'entity' => $productEntity->id,
            'entity_id' => $product->id,
            'url' => $request->image_url,
            'sort' => $sort,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Image::create($request->all());
        return response()->json([
            'succcess' => true,
            'message' => 'Product image added successfully',
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/shopimage/{id}",
     *     operationId="shopImageAdd",
     *     tags={"Image"},
     *     summary="Adds image to the shop",
     *     description="Associates the image to the shop using the file_url from the image upload endpoint.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The shop id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="image_url",
     *         in="query",
     *         description="The image url",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the shop image add status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop image add failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopImageAdd(int $id, Request $request)
    {
        $shop = Shop::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $shopEntity = Entity::where('name', $shop->getTable())->orderBy('id', 'DESC')->first();

        $image = Image::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->where('sort', '<>', 0)->whereNull('deleted_at')->orderBy('sort', 'DESC')->first();

        $sort = 1;
        if (!empty($image)) {
            $sort = $image->sort + 1;
        }

        $request->request->add([
            'entity' => $shopEntity->id,
            'entity_id' => $shop->id,
            'url' => $request->image_url,
            'sort' => $sort,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Image::create($request->all());
        return response()->json([
            'succcess' => true,
            'message' => 'Shop image added successfully',
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/uploadimage",
     *     operationId="uploadImage",
     *     tags={"Image"},
     *     summary="Uploads an image",
     *     description="Uploads the image to the cloud server.",
     *     @OA\RequestBody(
     *         description="The image to upload",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="base64",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the image url uploaded",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the image upload failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()){
            $cloudder = Cloudder::upload($request->file('image')->getRealPath());
            $uploadResult = $cloudder->getResult();
            $file_url = $uploadResult["url"];
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image',
            ], 400);
        }

        return response()->json(['file_url' => $file_url], 201);
    }
}
