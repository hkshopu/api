<?php

namespace App\Http\Controllers;

use App\Image;
use App\Product;
use App\Shop;
use App\Blog;
use App\User;
use App\Entity;
use Illuminate\Http\Request;
use Cloudder;
use Carbon\Carbon;

class ImageController extends Controller
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
     *     path="/api/productimage/{id}",
     *     operationId="productImageAdd",
     *     tags={"Image"},
     *     summary="Adds image to the product",
     *     description="Associates the image to the product using the file_url from the image upload endpoint.",
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
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $id)
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
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        Image::create($request->all());
        return response()->json([
            'succcess' => true,
            'message' => 'Product image added successfully',
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/productimage/{id}",
     *     operationId="productImageDelete",
     *     tags={"Image"},
     *     summary="Removes image to the product",
     *     description="Deassociates the image to the product using the file_url from the image upload endpoint.",
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
     *         description="Returns the product image delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product image delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productImageDelete(int $id, Request $request)
    {
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $id)
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

        $productEntity = Entity::where('name', $product->getTable())->orderBy('id', 'DESC')->first();

        $image = Image::where('entity', $productEntity->id)->where('entity_id', $product->id)->where('url', $request->image_url)->whereNull('deleted_at')->first();
        if (empty($image)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product image',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $image->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json([
            'succcess' => true,
            'message' => 'Product image removed successfully',
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
        $shopQuery = \DB::table('shop')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('shop.*')
            ->where('shop.id', $id)
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
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        Image::create($request->all());
        return response()->json([
            'succcess' => true,
            'message' => 'Shop image added successfully',
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/shopimage/{id}",
     *     operationId="shopImageDelete",
     *     tags={"Image"},
     *     summary="Removes image to the shop",
     *     description="Deassociates the image to the shop using the file_url from the image upload endpoint.",
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
     *         description="Returns the shop image delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop image delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopImageDelete(int $id, Request $request)
    {
        $shopQuery = \DB::table('shop')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('shop.*')
            ->where('shop.id', $id)
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

        $shopEntity = Entity::where('name', $shop->getTable())->orderBy('id', 'DESC')->first();

        $image = Image::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->where('url', $request->image_url)->whereNull('deleted_at')->first();
        if (empty($image)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop image',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $image->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json([
            'succcess' => true,
            'message' => 'Shop image removed successfully',
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/blogimage/{id}",
     *     operationId="blogImageAdd",
     *     tags={"Image"},
     *     summary="Adds image to the blog",
     *     description="Associates the image to the blog using the file_url from the image upload endpoint.",
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
     *         description="The blog id",
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
     *         description="Returns the blog image add status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog image add failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogImageAdd(int $id, Request $request)
    {
        $blogQuery = \DB::table('blog')
            ->leftJoin('shop', 'shop.id', '=', 'blog.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('blog.*')
            ->where('blog.id', $id)
            ->whereNull('blog.deleted_at');

        if ($request->filter_inactive == true) {
            $blogQuery
                ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                ->whereNotNull('shop_payment_method_map.id')
                ->whereNull('shop_payment_method_map.deleted_at')
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

        $blogEntity = Entity::where('name', $blog->getTable())->orderBy('id', 'DESC')->first();

        $image = Image::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->where('sort', '<>', 0)->whereNull('deleted_at')->orderBy('sort', 'DESC')->first();

        $sort = 1;
        if (!empty($image)) {
            $sort = $image->sort + 1;
        }

        $request->request->add([
            'entity' => $blogEntity->id,
            'entity_id' => $blog->id,
            'url' => $request->image_url,
            'sort' => $sort,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        Image::create($request->all());
        return response()->json([
            'succcess' => true,
            'message' => 'Blog image added successfully',
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/blogimage/{id}",
     *     operationId="blogImageDelete",
     *     tags={"Image"},
     *     summary="Removes image to the blog",
     *     description="Deassociates the image to the blog using the file_url from the image upload endpoint.",
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
     *         description="The blog id",
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
     *         description="Returns the blog image delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog image delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogImageDelete(int $id, Request $request)
    {
        $blogQuery = \DB::table('blog')
            ->leftJoin('shop', 'shop.id', '=', 'blog.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('blog.*')
            ->where('blog.id', $id)
            ->whereNull('blog.deleted_at');

        if ($request->filter_inactive == true) {
            $blogQuery
                ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                ->whereNotNull('shop_payment_method_map.id')
                ->whereNull('shop_payment_method_map.deleted_at')
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

        $blogEntity = Entity::where('name', $blog->getTable())->orderBy('id', 'DESC')->first();

        $image = Image::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->where('url', $request->image_url)->whereNull('deleted_at')->first();
        if (empty($image)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid blog image',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $image->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json([
            'succcess' => true,
            'message' => 'Blog image removed successfully',
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/userimage/{id}",
     *     operationId="userImageAdd",
     *     tags={"Image"},
     *     summary="Adds image to the user",
     *     description="Associates the image to the user using the file_url from the image upload endpoint.",
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
     *         description="The user id",
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
     *         description="Returns the user image add status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the user image add failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function userImageAdd(int $id, Request $request)
    {
        $user = User::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user id',
            ], 400);
        }

        $userEntity = Entity::where('name', $user->getTable())->orderBy('id', 'DESC')->first();

        $image = Image::where('entity', $userEntity->id)->where('entity_id', $user->id)->where('sort', '<>', 0)->whereNull('deleted_at')->orderBy('sort', 'DESC')->first();

        $sort = 1;
        if (!empty($image)) {
            $sort = $image->sort + 1;
        }

        $request->request->add([
            'entity' => $userEntity->id,
            'entity_id' => $user->id,
            'url' => $request->image_url,
            'sort' => $sort,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        Image::create($request->all());
        return response()->json([
            'succcess' => true,
            'message' => 'User image added successfully',
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/userimage/{id}",
     *     operationId="userImageDelete",
     *     tags={"Image"},
     *     summary="Removes image to the user",
     *     description="Deassociates the image to the user using the file_url from the image upload endpoint.",
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
     *         description="The user id",
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
     *         description="Returns the user image delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the user image delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function userImageDelete(int $id, Request $request)
    {
        $user = User::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user id',
            ], 400);
        }

        $userEntity = Entity::where('name', $user->getTable())->orderBy('id', 'DESC')->first();

        $image = Image::where('entity', $userEntity->id)->where('entity_id', $user->id)->where('url', $request->image_url)->whereNull('deleted_at')->first();
        if (empty($image)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user image',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $image->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json([
            'succcess' => true,
            'message' => 'User image removed successfully',
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/uploadimage",
     *     operationId="uploadImage",
     *     tags={"Image"},
     *     summary="Uploads an image",
     *     description="Uploads the image to the cloud server.",
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         description="The access token for authentication",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
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

