<?php

namespace App\Http\Controllers;

use App\Shop;
use App\Product;
use App\Entity;
use App\Category;
use App\CategoryMap;
use App\Image;
use App\Following;
use App\Status;
use App\StatusMap;
use App\StatusOption;
use App\Rating;
use App\Comment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShopController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/shop",
     *     operationId="shopList",
     *     tags={"Shop"},
     *     summary="Retrieves all shop",
     *     description="Retrieves all shop, filterable by shop name (in English), with pagination.",
     *     @OA\Parameter(
     *         name="name_en",
     *         in="query",
     *         description="The shop name (in English)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page_number",
     *         in="query",
     *         description="Result page number, default is 1",
     *         required=false,
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Parameter(
     *         name="page_size",
     *         in="query",
     *         description="Result page size, default is 25",
     *         required=false,
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns all shop",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop list failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopList(Request $request = null)
    {
        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        if (!empty($request->name_en)) {
            $shopList = Shop::where('name_en', 'LIKE', '%' . $request->name_en . '%')->whereNull('deleted_at')->get();
        } else {
            $shopList = Shop::whereNull('deleted_at')->get();
        }

        $pageNumber = (empty($request->page_number) || $request->page_number <= 0) ? 1 : (int) $request->page_number;
        $pageSize = (empty($request->page_size) || $request->page_size <= 0) ? 25 : (int) $request->page_size;
        $pageStart = ($pageNumber - 1) * $pageSize;
        $pageEnd = $pageNumber * $pageSize - 1;

        $shopListPaginated = [];
        foreach ($shopList as $shopKey => $shop) {
            if ($shopKey >= $pageStart && $shopKey <= $pageEnd) {
                $shopListPaginated[] = $shop;
            }
        }

        $shopList = $shopListPaginated;

        foreach ($shopList as $shopKey => $shop) {
            $shopList[$shopKey] = self::shopGet($shop->id)->getData();
        }

        return response()->json($shopList, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/shop",
     *     operationId="shopCreate",
     *     tags={"Shop"},
     *     summary="Creates new shop",
     *     description="Creates new shop.",
     *     @OA\Parameter(
     *         name="name_en",
     *         in="query",
     *         description="The shop name (in English)",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name_tc",
     *         in="query",
     *         description="The shop name (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name_sc",
     *         in="query",
     *         description="The shop name (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="The category id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status_id",
     *         in="query",
     *         description="The status id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="description_en",
     *         in="query",
     *         description="The shop description (in English)",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description_tc",
     *         in="query",
     *         description="The shop description (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description_sc",
     *         in="query",
     *         description="The shop description (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the shop created",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopCreate(Request $request)
    {
        if (empty($request->category_id) || empty(Category::where('id', $request->category_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category id',
            ], 400);
        }

        if (empty($request->status_id) || empty(Status::where('id', $request->status_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status id',
            ], 400);
        }

        $request->request->add([
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $shop = Shop::create($request->all());
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $request->request->add([
            'entity' => $shopEntity->id,
            'entity_id' => $shop->id,
            'category_id' => $request->category_id,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        CategoryMap::create($request->all());

        $request->request->add([
            'status_id' => $request->status_id,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        StatusMap::create($request->all());

        return response()->json(self::shopGet($shop->id)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/shop/{id}",
     *     operationId="shopGet",
     *     tags={"Shop"},
     *     summary="Retrieves the shop given the id",
     *     description="Retrieves the shop given the id.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The shop id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the shop given the id",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopGet($id)
    {
        $shop = Shop::where('id', $id)->whereNull('deleted_at')->first();

        if (!empty($shop)) {
            $shopEntity = Entity::where('name', $shop->getTable())->first();

            $categoryMap = CategoryMap::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($categoryMap)) {
                $shop['category'] = Category::where('id', $categoryMap->category_id)->whereNull('deleted_at')->first();
            } else {
                $shop['category'] = null;
            }

            $statusMap = StatusMap::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($statusMap)) {
                $shop['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;
            } else {
                $shop['status'] = null;
            }

            $image = new Image();
            $imageEntity = Entity::where('name', $image->getTable())->first();
            $imageList = Image::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->where('sort', '<>', 0)->orderBy('sort', 'ASC')->get();
            $shop['image'] = $imageList;

            $shopRatingList = Rating::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
            $shopRatingTotal = 0;
            foreach ($shopRatingList as $shopRatingItem) {
                $shopRatingTotal += $shopRatingItem->rate;
            }
            $shopRating = [
                'average' => $shopRatingTotal / (count($shopRatingList) ?: 1),
                'count' => count($shopRatingList),
            ];
            $shop['rating'] = $shopRating;

            
            $shopFollowingList = Following::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
            $shop['followers'] = count($shopFollowingList);

            $shop['orders'] = 0;

            $shopCommentList = Comment::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->get();
            $shop['comments'] = count($shopCommentList);
            
        }

        return response()->json($shop, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/shop/{id}",
     *     operationId="shopDelete",
     *     tags={"Shop"},
     *     summary="Deletes the shop given the id",
     *     description="Deletes the shop given the id.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The shop id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the shop delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopDelete($id, Request $request)
    {
        $shop = Shop::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid id',
            ], 400);
        } else if (!empty(Product::where('shop_id', $shop->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Shop still has existing product',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => 1,
        ]);

        $shop->update($request->all());
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $categoryMap = CategoryMap::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->first();
        if (!empty($categoryMap)) {
            $categoryMap->update($request->all());
        }

        $statusMap = StatusMap::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->first();
        if (!empty($statusMap)) {
            $statusMap->update($request->all());
        }

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/shop/{id}",
     *     operationId="shopModify",
     *     tags={"Shop"},
     *     summary="Modifies the shop given the id with only defined fields",
     *     description="Modifies the shop given the id with only defined fields.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The shop id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="name_en",
     *         in="query",
     *         description="The shop name (in English)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name_tc",
     *         in="query",
     *         description="The shop name (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name_sc",
     *         in="query",
     *         description="The shop name (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="The shop category id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status_id",
     *         in="query",
     *         description="The shop status id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="description_en",
     *         in="query",
     *         description="The shop description (in English)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description_tc",
     *         in="query",
     *         description="The shop description (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description_sc",
     *         in="query",
     *         description="The shop description (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the shop updated",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop update failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopModify($id, Request $request)
    {
        $shop = Shop::where('id', $id)->whereNull('deleted_at')->first();

        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid id',
            ], 400);
        }

        if (!empty($request->name_en)) {
            $request->request->add(['name_en' => $request->name_en]);
        }

        if (!empty($request->name_tc)) {
            $request->request->add(['name_tc' => $request->name_tc]);
        }

        if (!empty($request->name_sc)) {
            $request->request->add(['name_sc' => $request->name_sc]);
        }

        if (!empty($request->description_en)) {
            $request->request->add(['description_en' => $request->description_en]);
        }

        if (!empty($request->description_tc)) {
            $request->request->add(['description_tc' => $request->description_tc]);
        }

        if (!empty($request->description_sc)) {
            $request->request->add(['description_sc' => $request->description_sc]);
        }

        $shopEntity = Entity::where('name', $shop->getTable())->first();

        if (!empty($request->category_id) || $request->category_id === "0") {
            if (empty(Category::where('id', $request->category_id)->whereNull('deleted_at')->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category id',
                ], 400);
            } else if (empty(Category::where('entity', $shopEntity->id)->where('id', $request->category_id)->whereNull('deleted_at')->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category for shop',
                ], 400);
            }

            $request->request->add([
                'entity' => $shopEntity->id,
                'entity_id' => $shop->id,
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            $categoryMap = CategoryMap::create($request->all());
            $request->request->remove('created_by');
            $request->request->remove('updated_by');
        }

        if (!empty($request->status_id)) {
            if (empty(Status::where('id', $request->status_id)->whereNull('deleted_at')->first())) {
                if (!empty($categoryMap)) {
                    $categoryMap->delete();
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status id',
                ], 400);
            } else if (empty(StatusOption::where('entity', $shopEntity->id)->where('status_id', $request->status_id)->whereNull('deleted_at')->first())) {
                if (!empty($categoryMap)) {
                    $categoryMap->delete();
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status for shop',
                ], 400);
            }

            $request->request->add([
                'entity' => $shopEntity->id,
                'entity_id' => $shop->id,
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            $statusMap = StatusMap::create($request->all());
            $request->request->remove('created_by');
            $request->request->remove('updated_by');
        }

        $shop->update($request->all());
        $shop = self::shopGet($id)->getData();
        return response()->json($shop, 201);
    }
}