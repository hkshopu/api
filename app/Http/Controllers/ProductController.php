<?php

namespace App\Http\Controllers;

use App\Product;
use App\ProductPricing;
use App\ProductDiscount;
use App\ProductShipping;
use App\ProductInventory;
use App\ProductAttribute;
use App\Shop;
use App\Color;
use App\ProductColorMap;
use App\Size;
use App\ProductSizeMap;
use App\Entity;
use App\Category;
use App\CategoryMap;
use App\CategoryLevel;
use App\Image;
use App\Rating;
use App\Following;
use App\Status;
use App\StatusMap;
use App\StatusOption;
use App\View;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductController extends Controller
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
     * @OA\Get(
     *     path="/api/product",
     *     operationId="productList",
     *     tags={"Product"},
     *     summary="Retrieves all product",
     *     description="Retrieves all product, filterable by category id AND product name (in English) or ANY, with pagination.",
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
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
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
     *         name="sort",
     *         in="query",
     *         description="The product sorting (Accepted values: 'popular', 'recent', 'price_lowest', 'price_highest')",
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
     *         description="Returns all product",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product list failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productList(Request $request = null)
    {
        $productFilter = Product::whereNull('deleted_at');

        if (isset($request->shop_id)) {
            if (empty(Shop::where('id', $request->shop_id)->whereNull('deleted_at')->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid shop id',
                ], 400);
            } else {
                $productFilter->where('shop_id', $request->shop_id);
            }
        }

        if (isset($request->name_en)) {
            $productFilter->where('name_en', 'LIKE', '%' . $request->name_en . '%');
        }

        $productList = $productFilter->get();

        if (isset($request->category_id)) {
            $categoryList = Category::where('id', $request->category_id)->whereNull('deleted_at')->get();
            if (empty($categoryList->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category id',
                ], 400);
            }
        } else {
            $categoryList = Category::whereNull('deleted_at')->get();
        }

        $product = new Product();
        $productEntity = Entity::where('name', $product->getTable())->first();

        $productFilteredList = [];
        foreach ($productList as $product) {
            foreach ($categoryList as $category) {
                if (!empty(CategoryMap::where('entity', $productEntity->id)
                        ->where('entity_id', $product->id)
                        ->where('category_id', $category->id)
                        ->whereNull('deleted_at')
                        ->orderBy('id', 'DESC')
                        ->first())) {
                    $productFilteredList[] = $product;
                }
            }
        }

        $sortOrder = null;
        if (isset($request->sort)) {
            if ($request->sort <> 'popular' && $request->sort <> 'recent' && $request->sort <> 'price_lowest' && $request->sort <> 'price_highest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid product sort value',
                ], 400);
            }
            $sortOrder = $request->sort;
        }

        $productSortIdList = [];
        switch ($sortOrder) {
            case 'popular';
                foreach ($productFilteredList as $product) {
                    $productSortIdList[$product->id] = Rating::getAverage($product);
                }
                arsort($productSortIdList);
                break;
            case 'recent';
                foreach ($productFilteredList as $product) {
                    $productSortIdList[$product->id] = $product->created_at;
                }
                arsort($productSortIdList);
                break;
            case 'price_lowest';
                foreach ($productFilteredList as $product) {
                    $productSortIdList[$product->id] = (ProductPricing::where('product_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first())->price;
                }
                asort($productSortIdList);
                break;
            case 'price_highest';
                foreach ($productFilteredList as $product) {
                    $productSortIdList[$product->id] = (ProductPricing::where('product_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first())->price;
                }
                arsort($productSortIdList);
                break;
            default:
                foreach ($productFilteredList as $product) {
                    $productSortIdList[$product->id] = $product->id;
                }
                break;
        }

        $productSortedList = [];

        foreach ($productSortIdList as $key => $value) {
            $productSortedList[] = Product::where('id', $key)->whereNull('deleted_at')->first();
        }

        $productList = $productSortedList;

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
            $productList[$productKey] = self::productGet($product->id, $request)->getData();
        }

        return response()->json($productList, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/product",
     *     operationId="productCreate",
     *     tags={"Product"},
     *     summary="Creates new product",
     *     description="Creates new product.",
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
     *         name="shop_id",
     *         in="query",
     *         description="The product shop id",
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
    public function productCreate(Request $request)
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

        if (empty($request->shop_id) || empty(Shop::where('id', $request->shop_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $productAttribute = ProductAttribute::whereNull('deleted_at');
        $attribute = [];

        if (!empty($request->size_id)) {
            $size = Size::where('id', $request->size_id)->whereNull('deleted_at')->first();
            if (empty($size)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid size id',
                ], 400);
            }

            $attribute['size_id'] = $size->id;
            $productAttribute = $productAttribute->where('size_id', $size->id);
        }

        if (!empty($request->color_id)) {
            $color = Color::where('id', $request->color_id)->whereNull('deleted_at')->first();
            if (empty($color)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid color id',
                ], 400);
            }

            $attribute['color_id'] = $color->id;
            $productAttribute = $productAttribute->where('color_id', $color->id);
        }

        if (!empty($request->other)) {
            $attribute['other'] = $request->other;
            $productAttribute = $productAttribute->where('other', $request->other);
        }

        $request->request->add([
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $product = Product::create($request->all());
        $productEntity = Entity::where('name', $product->getTable())->first();

        $request->request->add([
            'entity' => $productEntity->id,
            'entity_id' => $product->id,
            'category_id' => $request->category_id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        CategoryMap::create($request->all());

        $request->request->add([
            'status_id' => $request->status_id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        StatusMap::create($request->all());

        $request->request->add([
            'product_id' => $product->id,
            'price' => abs($request->price_original),
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
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
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            ProductDiscount::create($request->all());
        }

        $request->request->add([
            'product_id' => $product->id,
            'amount' => abs($request->shipping_price),
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        ProductShipping::create($request->all());

        if (!empty($attribute)) {
            foreach ($attribute as $key => $value) {
                $request->request->add([$key => $value]);
            }

            $request->request->add([
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

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
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        ProductInventory::create($request->all());

        return response()->json(self::productGet($product->id, $request)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/product/{id}",
     *     operationId="productGet",
     *     tags={"Product"},
     *     summary="Retrieves the product given the id",
     *     description="Retrieves the product given the id.",
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
    public function productGet(int $id, Request $request = null)
    {
        $product = Product::where('id', $id)->whereNull('deleted_at')->first();

        if (!empty($product)) {
            // Explicit exclusion of the deleted_at field to still get username who created the product
            $createUser = User::where('id', $product->created_by)->first();
            $product['created_by_user'] = $createUser->only([
                'id',
                'username',
                'email',
                'first_name',
                'middle_name',
                'last_name',
            ]);

            // Explicit exclusion of the deleted_at field to still get username who updated the product
            $updateUser = User::where('id', $product->updated_by)->first();
            $product['updated_by_user'] = $updateUser->only([
                'id',
                'username',
                'email',
                'first_name',
                'middle_name',
                'last_name',
            ]);

            $productEntity = Entity::where('name', $product->getTable())->first();

            $categoryMap = CategoryMap::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($categoryMap)) {
                $product['category'] = Category::where('id', $categoryMap->category_id)->whereNull('deleted_at')->first();
                $categoryRoot[] = CategoryLevel::buildRoot([
                    'category' => Category::where('id', $categoryMap->category_id)->whereNull('deleted_at')->first()->toArray(),
                ]);
                $product['category_root'] = $categoryRoot;
            } else {
                $product['category'] = null;
                $product['category_root'] = null;
            }

            $productPricing = ProductPricing::where('product_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($productPricing)) {
                $product['price_original'] = $productPricing->price;
            } else {
                $product['price_original'] = 0.00;
            }

            $productDiscount = ProductDiscount::where('product_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($productDiscount) && $productDiscount->amount <> 0) {
                if ($productDiscount->type == 'fixed') {
                    $product['price_discounted'] = $product['price_original'] - $productDiscount->amount;
                } else if ($productDiscount->type == 'percentage') {
                    $product['price_discounted'] = $product['price_original'] * (1 - abs($productDiscount->amount));
                }
            } else {
                $product['price_discounted'] = null;
            }

            $productShipping = ProductShipping::where('product_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($productShipping)) {
                $product['shipping_price'] = $productShipping->amount;
            } else {
                $product['shipping_price'] = 0.00;
            }

            $product['sell'] = 0;
            $product['stock'] = 0;

            $statusMap = StatusMap::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($statusMap)) {
                $status = Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first();
                $product['status'] = (!empty($status)) ? $status->name : null;
            } else {
                $product['status'] = null;
            }

            // MySQL-centric code, change this to cross-database compatibility
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
            $imageEntity = Entity::where('name', $image->getTable())->first();
            $imageList = Image::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->where('sort', '<>', 0)->orderBy('sort', 'ASC')->get();
            $product['image'] = $imageList;
            foreach ($imageList as $imageKey => $imageItem) {
                $imageFollowingList = Following::where('entity', $imageEntity->id)->where('entity_id', $imageItem->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
                $product['image'][$imageKey]['followers'] = count($imageFollowingList);
            }

            $productRatingList = Rating::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
            $productRatingTotal = 0;
            $productUserRating = null;
            foreach ($productRatingList as $productRatingItem) {
                $productRatingTotal += $productRatingItem->rate;
                if ($productRatingItem->created_by == $request->access_token_user_id) {
                    $productUserRating = $productRatingItem->rate;
                }
            }
            $productRating = [
                'average' => $productRatingTotal / (count($productRatingList) ?: 1),
                'count' => count($productRatingList),
                'user_rating' => $productUserRating,
            ];
            $product['rating'] = $productRating;

            $productFollowingList = Following::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
            $product['followers'] = count($productFollowingList);

            $product['is_following'] = false;
            foreach ($productFollowingList as $following) {
                if (!empty($following) && $following->created_by == $request->access_token_user_id) {
                    $product['is_following'] = true;
                    break;
                }
            }

            $productViewList = View::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->get();
            $product['views'] = count($productViewList);

            $request->request->add([
                'product_id' => $product->id,
            ]);

            $product['shop'] = app('App\Http\Controllers\ShopController')->shopGet($product->shop_id, $request)->getData();
            unset($product['shop_id']);
        }

        return response()->json($product, 200);
    }

    public function productGetMinimal(int $id, Request $request = null)
    {
        $product = Product::where('id', $id)->whereNull('deleted_at')->first();

        if (!empty($product)) {
            $productEntity = Entity::where('name', $product->getTable())->first();

            $image = new Image();
            $imageEntity = Entity::where('name', $image->getTable())->first();
            $product['image'] = Image::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->where('sort', '<>', 0)->orderBy('sort', 'ASC')->first();

            unset($product['shop_id']);
        }

        return response()->json($product, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/product/{id}",
     *     operationId="productDelete",
     *     tags={"Product"},
     *     summary="Deletes the product given the id",
     *     description="Deletes the product given the id.",
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
    public function productDelete($id, Request $request)
    {
        $product = Product::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid id',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $product->update($request->all());
        $productEntity = Entity::where('name', $product->getTable())->first();

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
     *     operationId="productModify",
     *     tags={"Product"},
     *     summary="Modifies the product given the id with only defined fields",
     *     description="Modifies the product given the id with only defined fields.",
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
     *     @OA\Parameter(
     *         name="shop_id",
     *         in="query",
     *         description="The product shop id",
     *         required=false,
     *         @OA\Schema(type="integer")
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
    public function productModify($id, Request $request)
    {
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

        if (!empty($request->shop_id)) {
            if (empty(Shop::where('id', $request->shop_id)->whereNull('deleted_at')->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid shop id',
                ], 400);
            }

            $request->request->add(['shop_id' => $request->shop_id]);
        }

        $productEntity = Entity::where('name', $product->getTable())->first();

        if (!empty($request->category_id) || $request->category_id === "0") {
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

            $request->request->add([
                'entity' => $productEntity->id,
                'entity_id' => $product->id,
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
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
            } else if (empty(StatusOption::where('entity', $productEntity->id)->where('status_id', $request->status_id)->whereNull('deleted_at')->first())) {
                if (!empty($categoryMap)) {
                    $categoryMap->delete();
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status for product',
                ], 400);
            }

            $request->request->add([
                'entity' => $productEntity->id,
                'entity_id' => $product->id,
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            $statusMap = StatusMap::create($request->all());
            $request->request->remove('created_by');
            $request->request->remove('updated_by');
        }

        if (!empty($request->price_original)) {
            if ($request->price_original < 0) {
                if (!empty($categoryMap)) {
                    $categoryMap->delete();
                }
                if (!empty($statusMap)) {
                    $statusMap->delete();
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid original price',
                ], 400);
            }

            $request->request->add([
                'product_id' => $product->id,
                'price' => abs($request->price_original),
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            $productPricing = ProductPricing::create($request->all());
            $request->request->remove('created_by');
            $request->request->remove('updated_by');
        }

        if (!empty($request->price_discounted) || $request->price_discounted == 0) {
            if ($request->price_discounted == 0) {
                $discountedAmount = 0.00;
            } else {
                $productPricing = ProductPricing::where('product_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
                if ($request->price_discounted < 0 || $productPricing->price <= $request->price_discounted) {
                    if (!empty($categoryMap)) {
                        $categoryMap->delete();
                    }
                    if (!empty($statusMap)) {
                        $statusMap->delete();
                    }
                    if (!empty($productPricing)) {
                        $productPricing->delete();
                    }

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
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            $productDiscount = ProductDiscount::create($request->all());
            $request->request->remove('created_by');
            $request->request->remove('updated_by');
        }

        if (!empty($request->shipping_price) || $request->shipping_price == 0) {
            if ($request->shipping_price < 0.00) {
                if (!empty($categoryMap)) {
                    $categoryMap->delete();
                }
                if (!empty($statusMap)) {
                    $statusMap->delete();
                }
                if (!empty($productPricing)) {
                    $productPricing->delete();
                }
                if (!empty($productDiscount)) {
                    $productDiscount->delete();
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid shipping price',
                ], 400);
            }

            $request->request->add([
                'product_id' => $product->id,
                'amount' => abs($request->shipping_price),
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            $productShipping = ProductShipping::create($request->all());
            $request->request->remove('created_by');
            $request->request->remove('updated_by');
        }

        $product->update($request->all());
        $product = self::productGet($id, $request)->getData();
        return response()->json($product, 201);
    }

    /**
     * @OA\Post(
     *     path="/api/productstockadd/{id}",
     *     operationId="productStockAdd",
     *     tags={"Product"},
     *     summary="Adds stock to product inventory given the id",
     *     description="Adds stock to product inventory given the id.",
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
     *         response="201",
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
    public function productStockAdd(int $id, Request $request)
    {
        $product = Product::where('id', $id)->whereNull('deleted_at')->first();
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

        if (!empty($attribute)) {
            foreach ($attribute as $key => $value) {
                $request->request->add([$key => $value]);
            }
            $productAttribute = $productAttribute->first();
            if (empty($productAttribute)) {
                $request->request->add([
                    'created_by' => $request->access_token_user_id,
                    'updated_by' => $request->access_token_user_id,
                ]);

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
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        ProductInventory::create($request->all());

        return response()->json(self::productGet($product->id, $request)->getData(), 201);
    }

    /**
     * @OA\Post(
     *     path="/api/productstockremove/{id}",
     *     operationId="productStockRemove",
     *     tags={"Product"},
     *     summary="Removes stock to product inventory given the id",
     *     description="Adds negative record for product inventory.",
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
     *         response="201",
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
    public function productStockRemove(int $id, Request $request)
    {
        $product = Product::where('id', $id)->whereNull('deleted_at')->first();
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

        if (!empty($request->attribute_id) && empty(ProductAttribute::where('id', $request->attribute_id)->whereNull('deleted_at')->first())) {
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
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        ProductInventory::create($request->all());

        return response()->json(self::productGet($product->id, $request)->getData(), 201);
    }

    /**
     * @OA\Post(
     *     path="/api/productattribute",
     *     operationId="productAttributeAdd",
     *     tags={"Product"},
     *     summary="Adds product attribute",
     *     description="Adds product attribute.",
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
     *         name="product id",
     *         in="query",
     *         description="The product id",
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
     *         description="Returns the product attribute added",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product attribute add failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productAttributeAdd(Request $request)
    {
        $product = Product::where('id', $request->product_id)->whereNull('deleted_at')->first();
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid id',
            ], 400);
        }

        if (!empty($request->size_id)) {
            $size = Size::where('id', $request->size_id)->whereNull('deleted_at')->first();
            if (empty($size)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid size id',
                ], 400);
            }
        }

        if (!empty($request->color_id)) {
            $color = Color::where('id', $request->color_id)->whereNull('deleted_at')->first();
            if (empty($color)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid color id',
                ], 400);
            }
        }

        if (!empty($request->other)) {
            $attribute['other'] = $request->other;
            $productAttribute = $productAttribute->where('other', $request->other);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product attribute already exists',
        ], 400);
    }

    /**
     * @OA\Delete(
     *     path="/api/productattribute/{attribute_id}",
     *     operationId="productAttributeDelete",
     *     tags={"Product"},
     *     summary="Deletes product attribute given the id with only defined fields",
     *     description="Deletes product attribute given the id with only defined fields.",
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
     *         name="attribute_id",
     *         in="path",
     *         description="The attribute id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the product attribute deleted",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product attribute delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productAttributeDelete(int $attribute_id, Request $request = null)
    {
        $productAttribute = ProductAttribute::where('id', $attribute_id)->whereNull('deleted_at')->first();
        if (empty($productAttribute)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attribute id',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/productattribute/{attribute_id}",
     *     operationId="productAttributeModify",
     *     tags={"Product"},
     *     summary="Modifies product attribute given the id with only defined fields",
     *     description="Modifies product attribute given the id with only defined fields.",
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
     *         name="attribute_id",
     *         in="path",
     *         description="The attribute id",
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
     *         description="Returns the product attribute updated",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product attribute update failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productAttributeModify(int $attribute_id, Request $request)
    {
        $productAttribute = ProductAttribute::where('id', $attribute_id)->whereNull('deleted_at')->first();
        if (empty($productAttribute)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attribute id',
            ], 400);
        }

        if (!empty($request->size_id)) {
            $size = Size::where('id', $request->size_id)->whereNull('deleted_at')->first();
            if (empty($size)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid size id',
                ], 400);
            }
        }

        if (!empty($request->color_id)) {
            $color = Color::where('id', $request->color_id)->whereNull('deleted_at')->first();
            if (empty($color)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid color id',
                ], 400);
            }
        }

        if (!empty($request->other)) {

        }

        return response()->json([
            'success' => false,
            'message' => 'Attribute not associated with any product',
        ], 400);
    }
}

