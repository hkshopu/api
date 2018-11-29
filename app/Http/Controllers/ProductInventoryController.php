<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductAttribute;
use App\ProductInventory;
use App\Size;
use App\Color;
use Illuminate\Http\Request;
use App\Http\Controllers\ProductController;

class ProductInventoryController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/productstockadd/{id}",
     *     operationId="/api/productstockadd/{id}#post",
     *     tags={"Product"},
     *     summary="Adds stock to product inventory given the id",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="stock",
     *         in="query",
     *         description="The product stock",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="size_id",
     *         in="query",
     *         description="The product size (Optional)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="color_id",
     *         in="query",
     *         description="The product color (Optional)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="other",
     *         in="query",
     *         description="The product remarks (Optional)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the product with the updated stocks",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product stock add failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function add(int $id, Request $request)
    {
        $request->request->add([
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $product = Product::find($id);
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid id',
            ], 400);
        }

        if (empty($request->stock) || $request->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid stock value',
            ], 400);
        }

        $productAttribute = new ProductAttribute();
        $attribute = [];

        if (!empty($request->size_id)) {
            $size = Size::find($request->size_id);
            if (empty($size)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid size id',
                ], 400);
            } else {
                $attribute['size_id'] = $size->id;
                $productAttribute = $productAttribute->where('size_id', $size->id);
            }
        }

        if (!empty($request->color_id)) {
            $color = Color::find($request->color_id);
            if (empty($color)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid color id',
                ], 400);
            } else {
                $attribute['color_id'] = $color->id;
                $productAttribute = $productAttribute->where('color_id', $color->id);
            }
        }

        if (!empty($request->other)) {
            $attribute['other'] = $request->other;
            $productAttribute = $productAttribute->where('other', $request->other);
        }

        if (!empty($attribute)) {
            foreach ($attribute as $key => $value) {
                $request->request->add([$key => $value]);
            }
            $productAttribute = $productAttribute->first();
            if (empty($productAttribute)) {
                $productAttribute = ProductAttribute::create($request->all());
            }
            $attribute['id'] = $productAttribute->id;
        } else {
            $attribute['id'] = null;
        }

        $request->request->add([
            'product_id' => $product->id,
            'attribute_id' => $attribute['id'],
            'stock' => abs($request->stock),
        ]);

        ProductInventory::create($request->all());

        $productController = new ProductController();
        return response()->json($productController->fetch($product->id)->getData(), 201);
    }

    /**
     * @OA\Post(
     *     path="/api/productstockremove/{id}",
     *     operationId="/api/productstockremove/{id}#post",
     *     tags={"Product"},
     *     summary="Removes stock to product inventory given the id",
     *     description="To be used as negative record for sold items",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="stock",
     *         in="query",
     *         description="The product stock (Should be NEGATIVE value)",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="attribute_id",
     *         in="query",
     *         description="The product attribute id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the product with the updated stocks",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product stock remove failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function remove(int $id, Request $request)
    {
        $request->request->add([
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $product = Product::find($id);
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid id',
            ], 400);
        }

        if (empty($request->stock) || $request->stock > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid stock value',
            ], 400);
        }

        if (!empty($request->attribute_id) && empty(ProductAttribute::find($request->attribute_id))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attribute id',
            ], 400);
        } else if (!empty($request->attribute_id) && empty(ProductInventory::where('product_id', $product->id)->where('attribute_id', $request->attribute_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attribute for the product',
            ], 400);
        } else if (empty($request->attribute_id) && empty(ProductInventory::where('product_id', $product->id)->whereNull('attribute_id')->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Product is associated with attribute',
            ], 400);
        }

        $request->request->add([
            'product_id' => $product->id,
            'attribute_id' => $request->attribute_id,
            'stock' => abs($request->stock) * -1,
        ]);

        ProductInventory::create($request->all());

        $productController = new ProductController();
        return response()->json($productController->fetch($product->id)->getData(), 201);
    }
}

