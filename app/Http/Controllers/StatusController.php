<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\Shop;
use App\Comment;
use App\Entity;
use App\Status;
use App\StatusOption;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categorystatus",
     *     operationId="categoryStatusList",
     *     tags={"Status"},
     *     summary="Retrieves all category status",
     *     description="This provides available statuses to the category for frontend dynamically.",
     *     @OA\Response(
     *         response="200",
     *         description="Returns available category status",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function categoryStatusList()
    {
        $category = new Category();
        $categoryEntity = Entity::where('name', $category->getTable())->first();

        $statusList = [];
        $statusOptionList = StatusOption::where('entity', $categoryEntity->id)->whereNull('deleted_at')->get();
        foreach ($statusOptionList as $statusOption) {
            $statusList[] = Status::where('id', $statusOption->status_id)->whereNull('deleted_at')->first();
        }

        $data = $statusList;

        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/productstatus",
     *     operationId="productStatusList",
     *     tags={"Status"},
     *     summary="Retrieves all product status",
     *     description="This provides available statuses to the product for frontend dynamically.",
     *     @OA\Response(
     *         response="200",
     *         description="Returns available product status",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productStatusList()
    {
        $product = new Product();
        $productEntity = Entity::where('name', $product->getTable())->first();

        $statusList = [];
        $statusOptionList = StatusOption::where('entity', $productEntity->id)->whereNull('deleted_at')->get();
        foreach ($statusOptionList as $statusOption) {
            $statusList[] = Status::where('id', $statusOption->status_id)->whereNull('deleted_at')->first();
        }

        $data = $statusList;

        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/shopstatus",
     *     operationId="shopStatusList",
     *     tags={"Status"},
     *     summary="Retrieves all shop status",
     *     description="This provides available statuses to the shop for frontend dynamically.",
     *     @OA\Response(
     *         response="200",
     *         description="Returns available shop status",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function shopStatusList()
    {
        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $statusList = [];
        $statusOptionList = StatusOption::where('entity', $shopEntity->id)->whereNull('deleted_at')->get();
        foreach ($statusOptionList as $statusOption) {
            $statusList[] = Status::where('id', $statusOption->status_id)->whereNull('deleted_at')->first();
        }

        $data = $statusList;

        return response()->json($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/commentstatus",
     *     operationId="commentStatusList",
     *     tags={"Status"},
     *     summary="Retrieves all comment status",
     *     description="This provides available statuses to the comment for frontend dynamically.",
     *     @OA\Response(
     *         response="200",
     *         description="Returns available comment status",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function commentStatusList()
    {
        $comment = new Comment();
        $commentEntity = Entity::where('name', $comment->getTable())->first();

        $statusList = [];
        $statusOptionList = StatusOption::where('entity', $commentEntity->id)->whereNull('deleted_at')->get();
        foreach ($statusOptionList as $statusOption) {
            $statusList[] = Status::where('id', $statusOption->status_id)->whereNull('deleted_at')->first();
        }

        $data = $statusList;

        return response()->json($data, 200);
    }
}

