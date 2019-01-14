<?php

namespace App\Http\Controllers;

use App\Rating;
use App\Shop;
use App\Entity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RatingController extends Controller
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
     *     path="/api/shoprating",
     *     operationId="shopRatingAdd",
     *     tags={"Rating"},
     *     summary="Adds rating to shop",
     *     description="Adds rating to shop.",
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
     *         name="rating",
     *         in="query",
     *         description="The shop rating, scaling from 1 to 5 only",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the shop rating create status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop rating create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopRatingAdd(Request $request)
    {
        $shop = Shop::where('id', $request->shop_id)->whereNull('deleted_at')->first();
        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        } else if ($request->rating < 1 || $request->rating > 5) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid rating',
            ], 400);
        }

        $shopEntity = Entity::where('name', $shop->getTable())->first();
        $rating = Rating::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->first();

        if (!empty($rating)) {
            self::shopRatingDelete($rating->id, $request);
        }

        $request->request->add([
            'entity' => $shopEntity->id,
            'entity_id' => $shop->id,
            'rate' => $request->rating,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $rating = Rating::create($request->only([
            'entity',
            'entity_id',
            'rate',
            'created_by',
            'updated_by',
        ]));

        return response()->json(app('App\Http\Controllers\ShopController')->shopGet($shop->id, $request)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/shoprating/{shop_id}",
     *     operationId="shopRatingGet",
     *     tags={"Rating"},
     *     summary="Retrieves all shop ratings given the shop id",
     *     description="Retrieves all shop ratings given the shop id.",
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
     *         description="Returns all shop rating",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop rating get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopRatingGet(int $shop_id)
    {
        $shop = Shop::where('id', $shop_id)->whereNull('deleted_at')->first();
        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $ratingList = Rating::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();

        return response()->json($ratingList, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/shoprating/{id}",
     *     operationId="shopRatingDelete",
     *     tags={"Rating"},
     *     summary="Removes user rating to shop",
     *     description="Removes user rating to shop.",
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
     *         description="The shop rating id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the shop rating delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop rating delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopRatingDelete($id, Request $request)
    {
        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        if (empty(Rating::where('id', $id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid rating id',
            ], 400);
        } else if (empty(Rating::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid rating id for the shop',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $rating = Rating::where('id', $id)->where('entity', $shopEntity->id)->whereNull('deleted_at')->first();
        $rating->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Rating removed',
        ], 200);
    }
}

