<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\Entity;
use App\Status;
use App\StatusOption;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categorystatus",
     *     operationId="/api/categorystatus#get",
     *     tags={"Status"},
     *     summary="Retrieves all category status",
     *     description="This lists available statuses for the category dynamically.",
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

        return response()->json($data);
    }

    /**
     * @OA\Get(
     *     path="/api/productstatus",
     *     operationId="/api/productstatus#get",
     *     tags={"Status"},
     *     summary="Retrieves all product status",
     *     description="This lists available statuses for the product dynamically.",
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

        return response()->json($data);
    }
}

