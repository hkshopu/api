<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Shop;
use App\News;
use App\Entity;
use App\Status;
use App\StatusMap;
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
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="The user id (Just enter any random integer, yah as in random ;)",
     *         required=true,
     *         @OA\Schema(type="integer")
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

        return response()->json([
            'success' => true,
            'message' => 'Comment added',
        ], 201);
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
    public function shopCommentGet(int $shop_id)
    {
        $shop = Shop::where('id', $shop_id)->whereNull('deleted_at')->first();
        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $comment = new Comment();
        $commentEntity = Entity::where('name', $comment->getTable())->first();

        $commentList = Comment::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
        foreach ($commentList as $key => $comment) {
            $statusMap = StatusMap::where('entity', $commentEntity->id)->where('entity_id', $comment->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($statusMap)) {
                $commentList[$key]['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;
            } else {
                $commentList[$key]['status'] = null;
            }
        }

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
     *     path="/api/newscomment",
     *     operationId="newsCommentAdd",
     *     tags={"Comment"},
     *     summary="Adds comment to news",
     *     description="Adds comment to news.",
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
     *         name="news_id",
     *         in="query",
     *         description="The news id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="content",
     *         in="query",
     *         description="The news comment",
     *         required=true,
     *         @OA\Schema(type="string")
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
     *         description="Returns the news comment create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news comment create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsCommentAdd(Request $request)
    {
        $news = News::where('id', $request->news_id)->whereNull('deleted_at')->first();
        if (empty($news)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid news id',
            ], 400);
        } else if (empty($request->user_id) || $request->user_id < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user id',
            ], 400);
        }

        // Setting ACTIVE status for comment
        $status = Status::where('name', 'active')->whereNull('deleted_at')->first();

        $newsEntity = Entity::where('name', $news->getTable())->first();

        $request->request->add([
            'entity' => $newsEntity->id,
            'entity_id' => $news->id,
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

        return response()->json([
            'success' => true,
            'message' => 'Comment added',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/newscomment/{news_id}",
     *     operationId="newsCommentGet",
     *     tags={"Comment"},
     *     summary="Retrieves all news comments given the news id",
     *     description="Retrieves all news comments given the news id.",
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
     *         name="news_id",
     *         in="path",
     *         description="The news id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns all news comment",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news comment get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsCommentGet(int $news_id)
    {
        $news = News::where('id', $news_id)->whereNull('deleted_at')->first();
        if (empty($news)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid news id',
            ], 400);
        }

        $newsEntity = Entity::where('name', $news->getTable())->first();

        $comment = new Comment();
        $commentEntity = Entity::where('name', $comment->getTable())->first();

        $commentList = Comment::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
        foreach ($commentList as $key => $comment) {
            $statusMap = StatusMap::where('entity', $commentEntity->id)->where('entity_id', $comment->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($statusMap)) {
                $commentList[$key]['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;
            } else {
                $commentList[$key]['status'] = null;
            }
        }

        return response()->json($commentList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/newscomment/{id}",
     *     operationId="newsCommentDelete",
     *     tags={"Comment"},
     *     summary="Removes user comment to news",
     *     description="Removes user comment to news.",
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
     *         description="The news comment id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the news comment delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news comment delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsCommentDelete($id, Request $request)
    {
        $news = new News();
        $newsEntity = Entity::where('name', $news->getTable())->first();

        if (empty(Comment::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id',
            ], 400);
        } else if (empty(Comment::where('id', $id)->where('entity', $newsEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id for the news',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $comment = Comment::where('id', $id)->where('entity', $newsEntity->id)->whereNull('deleted_at')->first();
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
     *     path="/api/newscommentenable/{id}",
     *     operationId="newsCommentEnable",
     *     tags={"Comment"},
     *     summary="Enables user comment to news",
     *     description="Enables user comment to news.",
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
     *         description="The news comment id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the news comment enable status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news comment enable failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsCommentEnable($id, Request $request)
    {
        $news = new News();
        $newsEntity = Entity::where('name', $news->getTable())->first();

        if (empty(Comment::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id',
            ], 400);
        } else if (empty(Comment::where('id', $id)->where('entity', $newsEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id for the news',
            ], 400);
        }

        $comment = Comment::where('id', $id)->where('entity', $newsEntity->id)->whereNull('deleted_at')->first();
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
     *     path="/api/newscommentdisable/{id}",
     *     operationId="newsCommentDisable",
     *     tags={"Comment"},
     *     summary="Disables user comment to news",
     *     description="Disables user comment to news.",
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
     *         description="The news comment id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the news comment disable status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news comment disable failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsCommentDisable($id, Request $request)
    {
        $news = new News();
        $newsEntity = Entity::where('name', $news->getTable())->first();

        if (empty(Comment::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id',
            ], 400);
        } else if (empty(Comment::where('id', $id)->where('entity', $newsEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid comment id for the news',
            ], 400);
        }

        $comment = Comment::where('id', $id)->where('entity', $newsEntity->id)->whereNull('deleted_at')->first();
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

