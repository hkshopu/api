<?php

namespace App\Http\Controllers;

use App\Category;
use App\CategoryMap;
use App\CategoryLevel;
use App\Product;
use App\Entity;
use App\Status;
use App\StatusMap;
use App\StatusOption;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductCategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/productcategory",
     *     operationId="/api/productcategory#get",
     *     tags={"Category"},
     *     @OA\Response(
     *         response="200",
     *         description="Returns all product category",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function list(Request $request)
    {
        $product = new Product();
        $productEntity = Entity::where('name', $product->getTable())->first();
        $category = new Category();
        $categoryEntity = Entity::where('name', $category->getTable())->first();

        $elements = [];
        $categoryLevelList = CategoryLevel::all();
        foreach ($categoryLevelList as $categoryLevelKey => $categoryLevel) {
            $category = Category::where('id', $categoryLevel->category_id)->where('entity', $productEntity->id)->whereNull('deleted_at')->first();
            if (!empty($category)) {
                $statusMap = StatusMap::where('entity', $categoryEntity->id)->where('entity_id', $category->id)->whereNull('deleted_at')->first();
                $status = Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first();
                $category['status'] = $status->name;
                $elements[$categoryLevelKey]['category'] = $category;
                $elements[$categoryLevelKey]['parent_category_id'] = $categoryLevel->parent_category_id;
            }
        }

        $data = CategoryLevel::buildTree($elements, 0);

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/productcategory",
     *     operationId="/api/productcategory#post",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="The product category name",
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
     *     @OA\Parameter(
     *         name="parent_category_id",
     *         in="query",
     *         description="The parent category id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the product category created",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product category create failure reason",
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

        $product = new Product();
        $productEntity = Entity::where('name', $product->getTable())->first();

        $request->request->add([
            'entity' => $productEntity->id,
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

        $request->request->add([
            'category_id' => $category->id,
        ]);

        if ($request->parent_category_id) {
            if ($request->parent_category_id < 1) {
                $request->parent_category_id = 0;
            } else if (empty(Category::where('id', $request->parent_category_id)->whereNull('deleted_at')->first())) {
                $category->delete();
                $statusMap->delete();
                return response([
                    'success' => false,
                    'message' => 'Invalid category id',
                ], 400);
            } else if (empty(Category::where('entity', $productEntity->id)->where('id', $request->parent_category_id)->whereNull('deleted_at')->first())) {
                $category->delete();
                $statusMap->delete();
                return response([
                    'success' => false,
                    'message' => 'Invalid category for the product',
                ], 400);
            }

            $request->request->add([
                'parent_category_id' => $request->parent_category_id,
            ]);
        }

        $categoryLevel = CategoryLevel::create($request->all());

        $category['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;
        $category['parent_category_id'] = $categoryLevel->parent_category_id == 0 ? 0 : Category::where('id', $categoryLevel->parent_category_id)->whereNull('deleted_at')->first();

        return response()->json($category, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/productcategory/{id}",
     *     operationId="/api/productcategory/{id}#get",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product category id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the product category",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product category get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function fetch(int $id)
    {
        $product = new Product();
        $productEntity = Entity::where('name', $product->getTable())->first();

        $category = Category::where('id', $id)->whereNull('deleted_at')->first();

        if (empty($category)) {
            return response([
                'success' => false,
                'message' => 'Invalid category id',
            ], 400);
        } else if ($category->entity <> $productEntity->id) {
            return response([
                'success' => false,
                'message' => 'Invalid category for the product',
            ], 400);
        }

        $categoryEntity = Entity::where('name', $category->getTable())->first();

        $statusMap = StatusMap::where('entity', $categoryEntity->id)->where('entity_id', $category->id)->whereNull('deleted_at')->first();
        $category['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;

        $categoryLevel = CategoryLevel::where('category_id', $category->id)->whereNull('deleted_at')->first();
        $category['parent_category_id'] = $categoryLevel->parent_category_id == 0 ? 0 : Category::where('id', $categoryLevel->parent_category_id)->whereNull('deleted_at')->first();

        return response()->json($category, 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/productcategory/{id}",
     *     operationId="/api/productcategory/{id}#put",
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
     *         description="The product category name",
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
     *     @OA\Parameter(
     *         name="parent_category_id",
     *         in="query",
     *         description="The parent category id (Use 0 to dissociate category from its parent)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the product category updated",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product category update failure reason",
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

        $product = new Product();
        $productEntity = Entity::where('name', $product->getTable())->first();
        $categoryEntity = Entity::where('name', $category->getTable())->first();
        $statusMap = StatusMap::where('entity', $categoryEntity->id)->where('entity_id', $category->id)->whereNull('deleted_at')->first();
        $categoryLevel = CategoryLevel::where('category_id', $category->id)->whereNull('deleted_at')->first();

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

        if (isset($request->parent_category_id)) {
            if ($request->parent_category_id < 1) {
                $request->request->add([
                    'parent_category_id' => 0,
                ]);
            } else if (empty(Category::where('id', $request->parent_category_id)->whereNull('deleted_at')->first())) {
                return response([
                    'success' => false,
                    'message' => 'Invalid category id',
                ], 400);
            } else if (empty(Category::where('entity', $productEntity->id)->where('id', $request->parent_category_id)->whereNull('deleted_at')->first())) {
                return response([
                    'success' => false,
                    'message' => 'Invalid category for the product',
                ], 400);
            }

            $request->request->add([
                'category_id' => $category->id,
            ]);

            $categoryLevel->update($request->all());
        }

        $category['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;
        $category['parent_category_id'] = $categoryLevel->parent_category_id == 0 ? 0 : Category::where('id', $categoryLevel->parent_category_id)->whereNull('deleted_at')->first();

        return response()->json($category, 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/productcategory/{id}",
     *     operationId="/api/productcategory/{id}#delete",
     *     tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product category id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the product category delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product category delete failure reason",
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

        $product = new Product();
        $productEntity = Entity::where('name', $product->getTable())->first();
        $category = Category::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($category)) {
            return response([
                'success' => false,
                'message' => 'Invalid category id',
            ], 400);
        } else if ($category->entity <> $productEntity->id) {
            return response([
                'success' => false,
                'message' => 'Invalid category for the product',
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
                'message' => 'Currently associated with product',
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

