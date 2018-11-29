<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductPricing;
use App\ProductDiscount;
use App\ProductShipping;
use App\ProductInventory;
use App\ProductAttribute;
use App\Color;
use App\ProductColorMap;
use App\Size;
use App\ProductSizeMap;
use App\Entity;
use App\Category;
use App\CategoryMap;
use App\CategoryLevel;
use App\Image;
use App\Following;
use App\Status;
use App\StatusMap;
use App\StatusOption;
use App\View;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/product",
     *     operationId="/api/product#get",
     *     tags={"Product"},
     *     summary="Retrieves all product",
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="The category id",
     *         required=false,
     *         @OA\Schema(type="int")
     *     ),
     *     @OA\Parameter(
     *         name="name_en",
     *         in="query",
     *         description="The product name (in English)",
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
     *         description="Returns all product, filterable by category id AND product name (in English), with pagination",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product list failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function list(Request $request = null)
    {
        $product = new Product();
        $productEntity = Entity::where('name', $product->getTable())->whereNull('deleted_at')->first();

        if (!empty($request->name_en)) {
            $productList = Product::where('name_en', 'LIKE', '%' . $request->name_en . '%')->get();
        } else {
            $productList = Product::all();
        }

        if (!empty($request->category_id) && $productList->whereNull('deleted_at')->first()) {
            $productFilteredList = [];
            $category = Category::where('id', $request->category_id)->whereNull('deleted_at')->first();
            if (empty($category)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category id',
                ], 400);
            }
            foreach ($productList as $product) {
                if (!empty(CategoryMap::where('entity', $productEntity->id)
                        ->where('category_id', $category->id)
                        ->where('entity_id', $product->id)->whereNull('deleted_at')->first())) {
                    $productFilteredList[] = $product;
                }
            }
            $productList = $productFilteredList;
        }

        $pageNumber = (empty($request->page_number) || $request->page_number <= 0) ? 1 : (int) $request->page_number;
        $pageSize = (empty($request->page_size) || $request->page_size <= 0) ? 25 : (int) $request->page_size;

        $pageStart = ($pageNumber - 1) * $pageSize;
        $pageEnd = $pageNumber * $pageSize - 1;

        $productListPaginated = [];
        foreach ($productList as $productKey => $product) {
            if ($productKey >= $pageStart && $productKey <= $pageEnd) {
                $productListPaginated[] = $product;
            }
        }

        $productList = $productListPaginated;

        foreach ($productList as $productKey => $product) {
            $productList[$productKey] = self::fetch($product->id)->getData();
        }

        return response()->json($productList, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/product",
     *     operationId="/api/product#post",
     *     tags={"Product"},
     *     summary="Creates new product",
     *     @OA\Parameter(
     *         name="sku",
     *         in="query",
     *         description="The product sku",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name_en",
     *         in="query",
     *         description="The product name (in English)",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name_tc",
     *         in="query",
     *         description="The product name (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name_sc",
     *         in="query",
     *         description="The product name (in Simplified Chinese)",
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
     *         name="price_original",
     *         in="query",
     *         description="The product original price",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="price_discounted",
     *         in="query",
     *         description="The product discounted price",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="shipping_price",
     *         in="query",
     *         description="The product individual shipping price (Empty means FREE)",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="description_en",
     *         in="query",
     *         description="The product description (in English)",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description_tc",
     *         in="query",
     *         description="The product description (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description_sc",
     *         in="query",
     *         description="The product description (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
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
     *         response="201",
     *         description="Returns the product created",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product create failure reason",
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

        if (empty($request->price_original) || $request->price_original <= 0.00) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid original price',
            ], 400);
        }

        if (!empty($request->price_discounted) && ($request->price_discounted >= $request->price_original || $request->price_discounted <= 0.00)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid discounted price',
            ], 400);
        }

        if (!empty($request->shipping_price) && $request->shipping_price < 0.00) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shipping price',
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
            $size = Size::where('id', $request->size_id)->whereNull('deleted_at')->first();
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
            $color = Color::where('id', $request->color_id)->whereNull('deleted_at')->first();
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

        $product = Product::create($request->all());
        $productEntity = Entity::where('name', $product->getTable())->whereNull('deleted_at')->first();

        $request->request->add([
            'entity' => $productEntity->id,
            'entity_id' => $product->id,
            'category_id' => $request->category_id,
        ]);

        CategoryMap::create($request->all());

        $request->request->add([
            'status_id' => $request->status_id,
        ]);

        StatusMap::create($request->all());

        $request->request->add([
            'product_id' => $product->id,
            'price' => abs($request->price_original),
        ]);

        ProductPricing::create($request->all());

        if (!empty($request->price_discounted)) {
            if ($request->price_discounted == 0) {
                $discountedAmount = 0.00;
            } else {
                $discountedAmount = $request->price_original - $request->price_discounted;
            }

            $request->request->add([
                'product_id' => $product->id,
                'type' => 'fixed',
                'amount' => abs($discountedAmount()),
            ]);

            ProductDiscount::create($request->all());
        }

        $request->request->add([
            'product_id' => $product->id,
            'amount' => abs($request->shipping_price),
        ]);

        ProductShipping::create($request->all());

        if (!empty($attribute)) {
            foreach ($attribute as $key => $value) {
                $request->request->add([$key => $value]);
            }
            if (!empty($productAttribute->whereNull('deleted_at')->first())) {
                $productAttribute = $productAttribute->whereNull('deleted_at')->first();
            } else {
                $productAttribute = ProductAttribute::create($request->all());
            }
            $attribute['id'] = $productAttribute->id;
        } else {
            $attribute['id'] = null;
        }

        $request->request->add([
            'attribute_id' => $attribute['id'],
            'stock' => abs($request->stock),
        ]);

        ProductInventory::create($request->all());

        return response()->json(self::fetch($product->id)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/product/{id}",
     *     operationId="/api/product/{id}#get",
     *     tags={"Product"},
     *     summary="Retrieves the product given the id",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the product given the id",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function fetch($id)
    {
        $product = Product::where('id', $id)->whereNull('deleted_at')->first();

        if (!empty($product)) {
            $productEntity = Entity::where('name', $product->getTable())->whereNull('deleted_at')->first();

            $categoryMap = CategoryMap::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->first();
            if (!empty($categoryMap)) {
                $product['category'] = Category::where('id', $categoryMap->category_id)->whereNull('deleted_at')->first();
                $product['category_root'] = CategoryLevel::buildRoot([
                    'category' => Category::where('id', $categoryMap->category_id)->whereNull('deleted_at')->first()->toArray(),
                ]);
            } else {
                $product['category'] = null;
                $product['category_root'] = null;
            }

            $productPricing = ProductPricing::where('product_id', $product->id)->orderBy('id', 'DESC')->first();
            if (!empty($productPricing)) {
                $product['price_original'] = $productPricing->price;
            } else {
                $product['price_original'] = 0.00;
            }

            $productDiscount = ProductDiscount::where('product_id', $product->id)->orderBy('id', 'DESC')->first();
            if (!empty($productDiscount) && $productDiscount->amount <> 0) {
                if ($productDiscount->type == 'fixed') {
                    $product['price_discounted'] = $product['price_original'] - $productDiscount->amount;
                } else if ($productDiscount->type == 'percentage') {
                    $product['price_discounted'] = $product['price_original'] * (1 - abs($productDiscount->amount));
                }
            } else {
                $product['price_discounted'] = null;
            }

            $productShipping = ProductShipping::where('product_id', $product->id)->orderBy('id', 'DESC')->first();
            if (!empty($productShipping)) {
                $product['shipping_price'] = $productShipping->amount;
            } else {
                $product['shipping_price'] = 0.00;
            }

            $product['sell'] = 0;
            $product['stock'] = 0;

            $statusMap = StatusMap::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->first();
            if (!empty($statusMap)) {
                $product['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;
            } else {
                $product['status'] = null;
            }

            $productInventory = ProductInventory::select('attribute_id', \DB::raw('SUM(stock) AS stock'))
                ->groupBy('attribute_id')
                ->where('product_id', $product->id)
                ->get();

            $productAttributeList = [];
            $productStock = 0;
            foreach ($productInventory as $productInventoryItem) {
                $productAttribute = ProductAttribute::where('id', $productInventoryItem->attribute_id)->whereNull('deleted_at')->first();
                if (!empty($productAttribute)) {
                    $productAttributeOther = $productAttribute->other;
                    unset($productAttribute->other);

                    $productAttribute->stock = (int) $productInventoryItem->stock;
                    $productStock += $productAttribute->stock;

                    $productAttribute->size = null;
                    if (!empty($productAttribute->size_id)) {
                        $productAttribute->size = Size::where('id', $productAttribute->size_id)->whereNull('deleted_at')->first();
                    }
                    unset($productAttribute->size_id);

                    $productAttribute->color = null;
                    if (!empty($productAttribute->color_id)) {
                        $productAttribute->color = Color::where('id', $productAttribute->color_id)->whereNull('deleted_at')->first();
                    }
                    unset($productAttribute->color_id);

                    $productAttribute->other = $productAttributeOther;
                    $productAttributeList[] = $productAttribute;
                } else {
                    $productStock += $productInventoryItem->stock;
                }
            }

            $product['stock'] = $productStock;
            $product['attributes'] = $productAttributeList;

            $image = new Image();
            $imageEntity = Entity::where('name', $image->getTable())->whereNull('deleted_at')->first();
            $imageList = Image::where('entity', $productEntity->id)->where('entity_id', $product->id)->where('sort', '<>', 0)->orderBy('sort', 'ASC')->get();
            $product['image'] = $imageList;
            foreach ($imageList as $imageKey => $imageItem) {
                $imageFollowingList = Following::where('entity', $imageEntity->id)->where('entity_id', $imageItem->id)->get();
                $product['image'][$imageKey]['followers'] = count($imageFollowingList);
            }
            
            $productFollowingList = Following::where('entity', $productEntity->id)->where('entity_id', $product->id)->get();
            $product['followers'] = count($productFollowingList);

            $productViewList = View::where('entity', $productEntity->id)->where('entity_id', $product->id)->get();
            $product['views'] = count($productViewList);
            
        }

        return response()->json($product, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/product/{id}",
     *     operationId="/api/product/{id}#delete",
     *     tags={"Product"},
     *     summary="Deletes the product given the id",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the product delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product delete failure reason",
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

        $product = Product::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid id',
            ], 400);
        }

        $product->update($request->all());
        $productEntity = Entity::where('name', $product->getTable())->whereNull('deleted_at')->first();

        $categoryMap = CategoryMap::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($categoryMap)) {
            $categoryMap->update($request->all());
        }

        $statusMap = StatusMap::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($statusMap)) {
            $statusMap->update($request->all());
        }

        $productInventory = ProductInventory::where('product_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($productInventory)) {
            $productInventory->update($request->all());
        }

        $productPricing = ProductPricing::where('product_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($productPricing)) {
            $productPricing->update($request->all());
        }

        $productDiscount = ProductDiscount::where('product_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($productDiscount)) {
            $productDiscount->update($request->all());
        }

        $productShipping = ProductShipping::where('product_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($productShipping)) {
            $productShipping->update($request->all());
        }

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/product/{id}",
     *     operationId="/api/product/{id}#patch",
     *     tags={"Product"},
     *     summary="Updates the product given the id with only defined fields",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sku",
     *         in="query",
     *         description="The product sku",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name_en",
     *         in="query",
     *         description="The product name (in English)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name_tc",
     *         in="query",
     *         description="The product name (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name_sc",
     *         in="query",
     *         description="The product name (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="The product category id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status_id",
     *         in="query",
     *         description="The product status id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="price_original",
     *         in="query",
     *         description="The product original price",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="price_discounted",
     *         in="query",
     *         description="The product discounted price (Put ZERO to remove discount)",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="shipping_price",
     *         in="query",
     *         description="The product individual shipping price (Put ZERO to set as FREE)",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="description_en",
     *         in="query",
     *         description="The product description (in English)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description_tc",
     *         in="query",
     *         description="The product description (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description_sc",
     *         in="query",
     *         description="The product description (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the product updated",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product update failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function update($id, Request $request)
    {
        $request->request->add([
            'updated_by' => 1,
        ]);

        $product = Product::where('id', $id)->whereNull('deleted_at')->first();

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid id',
            ], 400);
        }

        if (!empty($request->sku)) {
            $request->request->add(['sku' => $request->sku]);
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

        $productEntity = Entity::where('name', $product->getTable())->whereNull('deleted_at')->first();

        if (!empty($request->category_id)) {
            if (empty(Category::where('id', $request->category_id)->whereNull('deleted_at')->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category id',
                ], 400);
            } else if (empty(Category::where('entity', $productEntity->id)->where('id', $request->category_id)->whereNull('deleted_at')->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category for product',
                ], 400);
            }

            $categoryMap = CategoryMap::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->first();
            $categoryMap->update($request->all());
        }

        if (!empty($request->status_id)) {
            if (empty(Status::where('id', $request->status_id)->whereNull('deleted_at')->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status id',
                ], 400);
            } else if (empty(StatusOption::where('entity', $productEntity->id)->where('status_id', $request->status_id)->whereNull('deleted_at')->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status for product',
                ], 400);
            }

            $statusMap = StatusMap::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->first();
            $statusMap->update($request->all());
        }

        if (!empty($request->price_original)) {
            if ($request->price_original < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid original price',
                ], 400);
            }

            $request->request->add([
                'product_id' => $product->id,
                'price' => abs($request->price_original),
            ]);

            ProductPricing::create($request->all());
        }

        if (!empty($request->price_discounted) || $request->price_discounted == 0) {
            if ($request->price_discounted == 0) {
                $discountedAmount = 0.00;
            } else {
                $productPricing = ProductPricing::where('product_id', $product->id)->orderBy('id', 'DESC')->first();
                if ($request->price_discounted < 0 || $productPricing->price <= $request->price_discounted) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid discounted price',
                    ], 400);
                }
    
                $discountedAmount = $productPricing->price - $request->price_discounted;
            }

            $request->request->add([
                'product_id' => $product->id,
                'type' => 'fixed',
                'amount' => abs($discountedAmount),
            ]);

            ProductDiscount::create($request->all());
        }

        if (!empty($request->shipping_price) || $request->shipping_price == 0) {
            if ($request->shipping_price < 0.00) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid shipping price',
                ], 400);
            }

            $request->request->add([
                'product_id' => $product->id,
                'amount' => abs($request->shipping_price),
            ]);

            ProductShipping::create($request->all());
        }

        $product->update($request->all());
        $product = self::fetch($id)->getData();
        return response()->json($product, 201);
    }
}

