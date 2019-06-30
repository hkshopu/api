<?php

namespace App\Http\Controllers;

use App\Like;
use App\Blog;
use App\Entity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LikeController extends Controller
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
     *     path="/api/bloglike",
     *     operationId="blogLikeAdd",
     *     tags={"Like"},
     *     summary="Likes user to blog",
     *     description="Likes user to blog.",
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
     *         name="blog_id",
     *         in="query",
     *         description="The blog id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the blog like create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog like create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogLikeAdd(Request $request)
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

        $blogEntity = Entity::where('name', $blog->getTable())->first();
        $like = Like::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

        if (!empty($like)) {
            return response()->json([
                'success' => false,
                'message' => 'Blog already liked',
            ], 400);
        }

        $request->request->add([
            'entity' => $blogEntity->id,
            'entity_id' => $blog->id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $like = Like::create($request->only([
            'entity',
            'entity_id',
            'created_by',
            'updated_by',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Blog liked',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/bloglike/{blog_id}",
     *     operationId="blogLikeGet",
     *     tags={"Like"},
     *     summary="Retrieves all blog likes given the blog id",
     *     description="Retrieves all blog likes given the blog id.",
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
     *         name="blog_id",
     *         in="path",
     *         description="The blog id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns blog like total count",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog like get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogLikeGet(int $blog_id, Request $request)
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

        $blogEntity = Entity::where('name', $blog->getTable())->first();

        $likeList = Like::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->get();
        foreach ($likeList as $key => $like) {
            $likeList[$key]['user_id'] = $like['created_by'];
            unset($likeList[$key]['created_by']);
        }

        return response()->json($likeList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/bloglike/{blog_id}",
     *     operationId="blogLikeDelete",
     *     tags={"Like"},
     *     summary="Unlikes user to blog",
     *     description="Unlikes user to blog.",
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
     *         name="blog_id",
     *         in="path",
     *         description="The blog id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the blog like delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog like delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogLikeDelete($blog_id, Request $request)
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

        $blogEntity = Entity::where('name', $blog->getTable())->first();
        $like = Like::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

        if (empty($like)) {
            return response()->json([
                'success' => false,
                'message' => 'Nothing to unlike',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $like->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Blog unliked',
        ], 200);
    }
}

