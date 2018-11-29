<?php

namespace App\Http\Controllers;

use App\Category;
use App\CategoryLevel;
use App\Product;
use App\Entity;
use App\Status;
use App\StatusMap;
use Illuminate\Http\Request;

class ProductCategoryParentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/productcategoryparent/{id}",
     *     operationId="/api/productcategoryparent/{id}#get",
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
     *         description="Returns the product category parent",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product category parent get failure reason",
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

        $element = ['category' => $category->toArray()];
        $data = CategoryLevel::buildRoot($element);

        return response()->json($data, 200);
    }
}

