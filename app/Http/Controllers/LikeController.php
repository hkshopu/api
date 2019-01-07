<?php

namespace App\Http\Controllers;

use App\Like;
use App\News;
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
     *     path="/api/newslike",
     *     operationId="newsLikeAdd",
     *     tags={"Like"},
     *     summary="Adds like to news",
     *     description="Adds like to news.",
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
     *         name="user_id",
     *         in="query",
     *         description="The user id (Just enter any random integer, yah as in random ;)",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the news like create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news like create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsLikeAdd(Request $request)
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

        $newsEntity = Entity::where('name', $news->getTable())->first();

        if (!empty(Like::where('entity', $newsEntity->id)->where('entity_id', $news->id)->where('user_id', $request->user_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Like already exists',
            ], 400);
        }

        $request->request->add([
            'entity' => $newsEntity->id,
            'entity_id' => $news->id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $like = Like::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Like added',
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/newslike/{news_id}",
     *     operationId="newsLikeGet",
     *     tags={"Like"},
     *     summary="Retrieves all news likes given the news id",
     *     description="Retrieves all news likes given the news id.",
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
     *         description="Returns news like total count",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news like get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsLikeGet(int $news_id)
    {
        $news = News::where('id', $news_id)->whereNull('deleted_at')->first();
        if (empty($news)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid news id',
            ], 400);
        }

        $newsEntity = Entity::where('name', $news->getTable())->first();

        $likeList = Like::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->get();

        return response()->json($likeList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/newslike/{id}",
     *     operationId="newsLikeDelete",
     *     tags={"Like"},
     *     summary="Unfollows user to news",
     *     description="Unfollows user to news.",
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
     *         description="The news like id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the news like delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news like delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsLikeDelete($id, Request $request)
    {
        $news = new News();
        $newsEntity = Entity::where('name', $news->getTable())->first();

        if (empty(Like::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid like id',
            ], 400);
        } else if (empty(Like::where('id', $id)->where('entity', $newsEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid like id for the news',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $like = Like::where('id', $id)->where('entity', $newsEntity->id)->whereNull('deleted_at')->first();
        $like->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Like removed',
        ], 200);
    }
}

