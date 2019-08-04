<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Shop;
use App\Blog;
use App\Entity;
use App\Status;
use App\StatusMap;
use App\User;
use App\Image;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CommentController extends Controller
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

    public function commentGet(int $comment_id)
    {
        $comment = Comment::where('id', $comment_id)->whereNull('deleted_at')->first();
        if (empty($comment)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id',
            ], 400);
        }

        $commentEntity = Entity::where('name', $comment->getTable())->first();

        $itemTemp = [];
        $postedDate = new Carbon($comment['created_at']);
        $itemTemp['comment_id'] = $comment['id'];
        $itemTemp['content'] = $comment['content'];
        $itemTemp['posted_date'] = $postedDate->format('Y-m-d');
        $user = User::where('id', $comment['created_by'])->whereNull('deleted_at')->first();
        if (!empty($user)) {
            $userEntity = Entity::where('name', $user->getTable())->first();
            $userImage = Image::where('entity', $userEntity->id)->where('entity_id', $user->id)->whereNull('deleted_at')->where('sort', '<>', 0)->orderBy('sort', 'ASC')->first();
            $itemTemp['user_name'] = $user->username;
            $itemTemp['user_profile_image'] = !empty($userImage) ? $userImage->url : null;
        } else {
            $itemTemp['user_name'] = null;
            $itemTemp['user_profile_image'] = null;
        }

        $statusMap = StatusMap::where('entity', $commentEntity->id)->where('entity_id', $comment->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
        if (!empty($statusMap)) {
            $status = Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first();
            $itemTemp['status'] = (!empty($status)) ? $status->name : null;
        } else {
            $itemTemp['status'] = null;
        }

        $comment = $itemTemp;

        return response()->json($comment, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/shopcomment",
     *     operationId="shopCommentAdd",
     *     tags={"Comment"},
     *     summary="Adds comment to shop",
     *     description="Adds comment to shop.",
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
     *         name="content",
     *         in="query",
     *         description="The shop comment",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the shop comment create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop comment create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopCommentAdd(Request $request)
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

        // Setting ACTIVE status for comment
        $status = Status::where('name', 'active')->whereNull('deleted_at')->first();

        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $request->request->add([
            'entity' => $shopEntity->id,
            'entity_id' => $shop->id,
            'rate' => $request->comment,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $comment = Comment::create($request->all());
        $commentEntity = Entity::where('name', $comment->getTable())->first();

        $request->request->add([
            'entity' => $commentEntity->id,
            'entity_id' => $comment->id,
            'status_id' => $status->id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $statusMap = StatusMap::create($request->all());

        return response()->json(self::commentGet($comment->id, $request)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/shopcomment/{shop_id}",
     *     operationId="shopCommentGet",
     *     tags={"Comment"},
     *     summary="Retrieves all shop comments given the shop id",
     *     description="Retrieves all shop comments given the shop id.",
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
     *         description="Returns all shop comment",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop comment get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopCommentGet(int $shop_id, Request $request = null)
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

        $comment = new Comment();
        $commentEntity = Entity::where('name', $comment->getTable())->first();

        $commentList = Comment::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->get();
        $listTemp = [];
        foreach ($commentList as $key => $comment) {
            $listTemp[$key] = self::commentGet($comment->id, $request)->getData();
        }

        $commentList = $listTemp;

        return response()->json($commentList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/shopcomment/{id}",
     *     operationId="shopCommentDelete",
     *     tags={"Comment"},
     *     summary="Removes user comment to shop",
     *     description="Removes user comment to shop.",
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
     *         description="The shop comment id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the shop comment delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop comment delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopCommentDelete($id, Request $request)
    {
        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        if (empty(Comment::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id',
            ], 400);
        } else if (empty(Comment::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id for the shop',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $comment = Comment::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first();
        $comment->update($request->all());

        $commentEntity = Entity::where('name', $comment->getTable())->first();
        $statusMap = StatusMap::where('entity', $commentEntity->id)->where('entity_id', $comment->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
        $statusMap->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Comment removed',
        ], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/shopcommentenable/{id}",
     *     operationId="shopCommentEnable",
     *     tags={"Comment"},
     *     summary="Enables user comment to shop",
     *     description="Enables user comment to shop.",
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
     *         description="The shop comment id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the shop comment enable status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop comment enable failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopCommentEnable($id, Request $request)
    {
        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        if (empty(Comment::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id',
            ], 400);
        } else if (empty(Comment::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id for the shop',
            ], 400);
        }

        $comment = Comment::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first();
        $commentEntity = Entity::where('name', $comment->getTable())->first();

        // Setting ACTIVE status for comment
        $status = Status::where('name', 'active')->whereNull('deleted_at')->first();
        $statusMap = StatusMap::where('entity', $commentEntity->id)->where('entity_id', $comment->id)->whereNull('deleted_at')->first();

        if ($statusMap->status_id == $status->id) {
            return response()->json([
                'success' => false,
                'message' => 'Comment status is already active',
            ], 400);
        }

        $request->request->add([
            'status_id' => $status->id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $statusMap->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Comment status changed to active',
        ], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/shopcommentdisable/{id}",
     *     operationId="shopCommentDisable",
     *     tags={"Comment"},
     *     summary="Disables user comment to shop",
     *     description="Disables user comment to shop.",
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
     *         description="The shop comment id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the shop comment disable status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop comment disable failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopCommentDisable($id, Request $request)
    {
        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        if (empty(Comment::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id',
            ], 400);
        } else if (empty(Comment::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id for the shop',
            ], 400);
        }

        $comment = Comment::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first();
        $commentEntity = Entity::where('name', $comment->getTable())->first();

        // Setting ACTIVE status for comment
        $status = Status::where('name', 'disable')->whereNull('deleted_at')->first();
        $statusMap = StatusMap::where('entity', $commentEntity->id)->where('entity_id', $comment->id)->whereNull('deleted_at')->first();

        if ($statusMap->status_id == $status->id) {
            return response()->json([
                'success' => false,
                'message' => 'Comment status is already disable',
            ], 400);
        }

        $request->request->add([
            'status_id' => $status->id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $statusMap->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Comment status changed to disable',
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/blogcomment",
     *     operationId="blogCommentAdd",
     *     tags={"Comment"},
     *     summary="Adds comment to blog",
     *     description="Adds comment to blog.",
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
     *     @OA\Parameter(
     *         name="content",
     *         in="query",
     *         description="The blog comment",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the blog comment create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog comment create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogCommentAdd(Request $request)
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

        // Setting ACTIVE status for comment
        $status = Status::where('name', 'active')->whereNull('deleted_at')->first();

        $blogEntity = Entity::where('name', $blog->getTable())->first();

        $request->request->add([
            'entity' => $blogEntity->id,
            'entity_id' => $blog->id,
            'rate' => $request->comment,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $comment = Comment::create($request->all());
        $commentEntity = Entity::where('name', $comment->getTable())->first();

        $request->request->add([
            'entity' => $commentEntity->id,
            'entity_id' => $comment->id,
            'status_id' => $status->id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $statusMap = StatusMap::create($request->all());

        return response()->json(self::commentGet($comment->id, $request)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/blogcomment/{blog_id}",
     *     operationId="blogCommentGet",
     *     tags={"Comment"},
     *     summary="Retrieves all blog comments given the blog id",
     *     description="Retrieves all blog comments given the blog id.",
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
     *         description="Returns all blog comment",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog comment get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogCommentGet(int $blog_id, Request $request)
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

        $comment = new Comment();
        $commentEntity = Entity::where('name', $comment->getTable())->first();

        $commentList = Comment::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->get();
        $listTemp = [];
        foreach ($commentList as $key => $comment) {
            $listTemp[$key] = self::commentGet($comment->id, $request)->getData();
        }

        $commentList = $listTemp;

        return response()->json($commentList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/blogcomment/{id}",
     *     operationId="blogCommentDelete",
     *     tags={"Comment"},
     *     summary="Removes user comment to blog",
     *     description="Removes user comment to blog.",
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
     *         description="The blog comment id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the blog comment delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog comment delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogCommentDelete($id, Request $request)
    {
        $blog = new Blog();
        $blogEntity = Entity::where('name', $blog->getTable())->first();

        if (empty(Comment::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id',
            ], 400);
        } else if (empty(Comment::where('id', $id)->where('entity', $blogEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id for the blog',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $comment = Comment::where('id', $id)->where('entity', $blogEntity->id)->whereNull('deleted_at')->first();
        $comment->update($request->all());

        $commentEntity = Entity::where('name', $comment->getTable())->first();
        $statusMap = StatusMap::where('entity', $commentEntity->id)->where('entity_id', $comment->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
        $statusMap->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Comment removed',
        ], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/blogcommentenable/{id}",
     *     operationId="blogCommentEnable",
     *     tags={"Comment"},
     *     summary="Enables user comment to blog",
     *     description="Enables user comment to blog.",
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
     *         description="The blog comment id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the blog comment enable status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog comment enable failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogCommentEnable($id, Request $request)
    {
        $blog = new Blog();
        $blogEntity = Entity::where('name', $blog->getTable())->first();

        if (empty(Comment::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id',
            ], 400);
        } else if (empty(Comment::where('id', $id)->where('entity', $blogEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id for the blog',
            ], 400);
        }

        $comment = Comment::where('id', $id)->where('entity', $blogEntity->id)->whereNull('deleted_at')->first();
        $commentEntity = Entity::where('name', $comment->getTable())->first();

        // Setting ACTIVE status for comment
        $status = Status::where('name', 'active')->whereNull('deleted_at')->first();
        $statusMap = StatusMap::where('entity', $commentEntity->id)->where('entity_id', $comment->id)->whereNull('deleted_at')->first();

        if ($statusMap->status_id == $status->id) {
            return response()->json([
                'success' => false,
                'message' => 'Comment status is already active',
            ], 400);
        }

        $request->request->add([
            'status_id' => $status->id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $statusMap->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Comment status changed to active',
        ], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/blogcommentdisable/{id}",
     *     operationId="blogCommentDisable",
     *     tags={"Comment"},
     *     summary="Disables user comment to blog",
     *     description="Disables user comment to blog.",
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
     *         description="The blog comment id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the blog comment disable status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog comment disable failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogCommentDisable($id, Request $request)
    {
        $blog = new Blog();
        $blogEntity = Entity::where('name', $blog->getTable())->first();

        if (empty(Comment::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id',
            ], 400);
        } else if (empty(Comment::where('id', $id)->where('entity', $blogEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id for the blog',
            ], 400);
        }

        $comment = Comment::where('id', $id)->where('entity', $blogEntity->id)->whereNull('deleted_at')->first();
        $commentEntity = Entity::where('name', $comment->getTable())->first();

        // Setting ACTIVE status for comment
        $status = Status::where('name', 'disable')->whereNull('deleted_at')->first();
        $statusMap = StatusMap::where('entity', $commentEntity->id)->where('entity_id', $comment->id)->whereNull('deleted_at')->first();

        if ($statusMap->status_id == $status->id) {
            return response()->json([
                'success' => false,
                'message' => 'Comment status is already disable',
            ], 400);
        }

        $request->request->add([
            'status_id' => $status->id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $statusMap->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Comment status changed to disable',
        ], 200);
    }
}

 