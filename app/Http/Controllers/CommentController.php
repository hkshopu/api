<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Shop;
use App\Entity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CommentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/shopcomment",
     *     operationId="shopCommentAdd",
     *     tags={"Comment"},
     *     summary="Adds comment to shop",
     *     description="Adds comment to shop.",
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

        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $request->request->add([
            'entity' => $shopEntity->id,
            'entity_id' => $shop->id,
            'rate' => $request->comment,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $comment = Comment::create($request->all());
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

        $commentList = Comment::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

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
            'deleted_by' => 1,
        ]);

        $comment = Comment::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first();
        $comment->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Comment removed',
        ], 200);
    }
}

