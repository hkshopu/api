<?php

namespace App\Http\Controllers;

use App\Category;
use App\CategoryMap;
use App\CategoryLevel;
use App\Shop;
use App\Entity;
use App\Status;
use App\StatusMap;
use App\StatusOption;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShopCategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/shopcategory",
     *     operationId="/api/shopcategory#get",
     *     tags={"Category"},
     *     @OA\Response(
     *         response="200",
     *         description="Returns all shop category",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function list(Request $request)
    {
        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();
        $category = new Category();
        $categoryEntity = Entity::where('name', $category->getTable())->first();

        $categoryList = Category::where('entity', $shopEntity->id)->whereNull('deleted_at')->get();
        foreach ($categoryList as $categoryKey => $category) {
            $statusMap = StatusMap::where('entity', $categoryEntity->id)->where('entity_id', $category->id)->whereNull('deleted_at')->first();
            $status = Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first();
            $categoryList[$categoryKey]['status'] = $status->name;
        }

        $data = $categoryList;

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/shopcategory",
     *     operationId="/api/shopcategory#post",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="The shop category name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status_id",
     *         in="query",
     *         description="The status id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the shop category created",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop category create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function create(Request $request)
    {
        $request->request->add([
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $request->request->add([
            'entity' => $shopEntity->id,
        ]);

        $category = Category::create($request->all());
        $categoryEntity = Entity::where('name', $category->getTable())->first();

        if (empty(Status::where('id', $request->status_id)->whereNull('deleted_at')->first())) {
            $category->delete();
            return response([
                'success' => false,
                'message' => 'Invalid status id',
            ], 400);
        } else if (empty(StatusOption::where('entity', $categoryEntity->id)->where('status_id', $request->status_id)->whereNull('deleted_at')->first())) {
            $category->delete();
            return response([
                'success' => false,
                'message' => 'Invalid status for category',
            ], 400);
        }

        $request->request->add([
            'entity' => $categoryEntity->id,
            'entity_id' => $category->id,
        ]);

        $statusMap = StatusMap::create($request->all());

        $category['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;

        return response()->json($category, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/shopcategory/{id}",
     *     operationId="/api/shopcategory/{id}#get",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The shop category id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the shop category",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop category get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function fetch(int $id)
    {
        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $category = Category::where('id', $id)->whereNull('deleted_at')->first();

        if (empty($category)) {
            return response([
                'success' => false,
                'message' => 'Invalid category id',
            ], 400);
        } else if ($category->entity <> $shopEntity->id) {
            return response([
                'success' => false,
                'message' => 'Invalid category for the shop',
            ], 400);
        }

        $categoryEntity = Entity::where('name', $category->getTable())->first();

        $statusMap = StatusMap::where('entity', $categoryEntity->id)->where('entity_id', $category->id)->whereNull('deleted_at')->first();
        $category['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;

        return response()->json($category, 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/shopcategory/{id}",
     *     operationId="/api/shopcategory/{id}#post",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The category id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="The shop category name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status_id",
     *         in="query",
     *         description="The status id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the shop category updated",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop category update failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function update(int $id, Request $request)
    {
        $request->request->add([
            'updated_by' => 1,
        ]);

        $category = Category::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($category)) {
            return response([
                'success' => false,
                'message' => 'Invalid category id',
            ], 400);
        }

        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();
        $categoryEntity = Entity::where('name', $category->getTable())->first();
        $statusMap = StatusMap::where('entity', $categoryEntity->id)->where('entity_id', $category->id)->whereNull('deleted_at')->first();

        if ($request->name) {
            $category->update($request->all());
        }

        if ($request->status_id) {
            if (empty(Status::where('id', $request->status_id)->whereNull('deleted_at')->first())) {
                return response([
                    'success' => false,
                    'message' => 'Invalid status id',
                ], 400);
            } else if (empty(StatusOption::where('entity', $categoryEntity->id)->where('status_id', $request->status_id)->whereNull('deleted_at')->first())) {
                return response([
                    'success' => false,
                    'message' => 'Invalid status for category',
                ], 400);
            }

            $request->request->add([
                'entity' => $categoryEntity->id,
                'entity_id' => $category->id,
            ]);

            $statusMap->update($request->all());
        }

        $category['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;

        return response()->json($category, 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/shopcategory/{id}",
     *     operationId="/api/shopcategory/{id}#delete",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The shop category id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the shop category delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the shop category delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function delete($id, Request $request)
    {
        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => 1,
        ]);

        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();
        $category = Category::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($category)) {
            return response([
                'success' => false,
                'message' => 'Invalid category id',
            ], 400);
        } else if ($category->entity <> $shopEntity->id) {
            return response([
                'success' => false,
                'message' => 'Invalid category for the shop',
            ], 400);
        } else if (
            !empty(CategoryLevel::where('parent_category_id', $id)->whereNull('deleted_at')->first())
        ) {
            return response([
                'success' => false,
                'message' => 'Currently associated with sub category',
            ], 400);
        } else if (
            !empty(CategoryMap::where('category_id', $id)->whereNull('deleted_at')->first())
        ) {
            return response([
                'success' => false,
                'message' => 'Currently associated with shop',
            ], 400);
        }

        $category->update($request->all());
        $categoryLevel = CategoryLevel::where('category_id', $category->id)->whereNull('deleted_at')->first();
        if (!empty($categoryLevel)) {
            $categoryLevel->update($request->all());
        }

        $statusMap = StatusMap::where('entity_id', $category->id)->whereNull('deleted_at')->first();
        if (!empty($statusMap)) {
            $statusMap->update($request->all());
        }

        return response([
            'success' => true,
            'message' => 'Deleted successfully',
        ], 200);
    }
}

