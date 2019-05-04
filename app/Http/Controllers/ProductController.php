<?php

namespace App\Http\Controllers;

use App\Product;
use App\Attribute;
use App\ProductAttribute;
use App\ProductInventory;
use App\ProductPricing;
use App\ProductDiscount;
use App\ProductShipping;
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
use App\Language;
use App\CartItem;
use App\Order;
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
        $productFilter = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productFilter
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        if (isset($request->shop_id)) {
            $shopQuery = \DB::table('shop')
                ->leftJoin('user', 'user.id', '=', 'shop.user_id')
                ->select('shop.*')
                ->where('shop.id', $request->shop_id)
                ->whereNull('shop.deleted_at');

            if ($request->filter_inactive == true) {
                $shopQuery
                    ->whereNull('user.deleted_at');
            }

            $shop = $shopQuery->first();

            if (empty($shop)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid shop id',
                ], 400);
            }

            $shop = Shop::where('id', $shop->id)->whereNull('deleted_at')->first();

            $productFilter->where('product.shop_id', $request->shop_id);
        }

        if (isset($request->name_en)) {
            $productFilter->where('product.name_en', 'LIKE', '%' . $request->name_en . '%');
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
            $productItemQuery = \DB::table('product')
                ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
                ->leftJoin('user', 'user.id', '=', 'shop.user_id')
                ->select('product.*')
                ->where('product.id', $key)
                ->whereNull('product.deleted_at');

            if ($request->filter_inactive == true) {
                $productItemQuery
                    ->whereNull('shop.deleted_at')
                    ->whereNull('user.deleted_at');
            }

            $productItem = $productItemQuery->first();
            if (!empty($productItem)) {
                $productSortedList[] = $productItem;
            }
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
        $productActive = [];

        foreach ($productList as $product) {
            $productGet = self::productGet($product->id, $request)->getData();
            if (!empty($productGet) && !empty($productGet->id)) {
                $productActive[] = $productGet;
            }
        }

        $productList = $productActive;

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

        $shopQuery = \DB::table('shop')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('shop.*')
            ->where('shop.id', $request->shop_id)
            ->whereNull('shop.deleted_at');

        if ($request->filter_inactive == true) {
            $shopQuery
                ->whereNull('user.deleted_at');
        }

        $shop = $shopQuery->first();

        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $shop = Shop::where('id', $shop->id)->whereNull('deleted_at')->first();

        $attribute = Attribute::whereNull('deleted_at');
        $attributeDraft = [];

        if (!empty($request->size_id)) {
            $size = Size::where('id', $request->size_id)->whereNull('deleted_at')->first();
            if (empty($size)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid size id',
                ], 400);
            }

            $attributeDraft['size_id'] = $size->id;
            $attribute = $attribute->where('size_id', $size->id);
        }

        if (!empty($request->color_id)) {
            $color = Color::where('id', $request->color_id)->whereNull('deleted_at')->first();
            if (empty($color)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid color id',
                ], 400);
            }

            $attributeDraft['color_id'] = $color->id;
            $attribute = $attribute->where('color_id', $color->id);
        }

        if (!empty($request->other)) {
            $attributeDraft['other'] = $request->other;
            $attribute = $attribute->where('other', $request->other);
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

        CategoryMap::create($request->only([
            'entity',
            'entity_id',
            'category_id',
            'created_by',
            'updated_by',
        ]));

        $request->request->add([
            'status_id' => $request->status_id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        StatusMap::create($request->only([
            'entity',
            'entity_id',
            'status_id',
            'created_by',
            'updated_by',
        ]));

        $request->request->add([
            'product_id' => $product->id,
            'price' => abs($request->price_original),
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        ProductPricing::create($request->only([
            'product_id',
            'price',
            'created_by',
            'updated_by',
        ]));

        if (!empty($request->price_discounted)) {
            if ($request->price_discounted == 0) {
                $discountedAmount = 0.00;
            } else {
                $discountedAmount = $request->price_original - $request->price_discounted;
            }

            $request->request->add([
                'product_id' => $product->id,
                'type' => 'fixed',
                'amount' => abs($discountedAmount),
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            ProductDiscount::create($request->only([
                'product_id',
                'type',
                'amount',
                'created_by',
                'updated_by',
            ]));
        }

        $request->request->add([
            'product_id' => $product->id,
            'amount' => abs($request->shipping_price),
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        ProductShipping::create($request->only([
            'product_id',
            'amount',
            'created_by',
            'updated_by',
        ]));

        if (!empty($attributeDraft)) {
            foreach ($attributeDraft as $key => $value) {
                $request->request->add([$key => $value]);
            }

            $request->request->add([
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            if (!empty($attribute->whereNull('deleted_at')->first())) {
                $attribute = $attribute->whereNull('deleted_at')->first();
            } else {
                $attribute = Attribute::create($request->only([
                    'size_id',
                    'color_id',
                    'other',
                    'created_by',
                    'updated_by',
                ]));
            }
        }

        $request->request->add([
            'product_id' => $product->id,
            'attribute_id' => !empty($attribute->id) ? $attribute->id : null,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $productAttribute = ProductAttribute::create($request->only([
            'product_id',
            'attribute_id',
            'created_by',
            'updated_by',
        ]));

        $request->request->add([
            'product_attribute_id' => $productAttribute->id,
            'stock' => abs($request->stock),
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        ProductInventory::create($request->only([
            'product_attribute_id',
            'stock',
            'created_by',
            'updated_by',
        ]));

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
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (!empty($product)) {
            $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();

            // LANGUAGE Translation
            $product->name = Language::translate($request, $product, 'name');
            $product->description = Language::translate($request, $product, 'description');

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

            $product['sell_via_order'] = 0;
            $product['sell_object_order'] = null;

            $productSellPerCartOrder = [];
            $cartItemList = CartItem::where('product_id', $product->id)->whereNotNull('order_id')->whereNull('deleted_at')->get();
            foreach ($cartItemList as $cartItemItem) {
                // Getting PAID status for payment
                $order = Order::where('id', $cartItemItem->order_id)->whereNull('deleted_at')->first();
                $statusPayment = Status::where('name', 'paid')->whereNull('deleted_at')->first();
                $paymentEntity = Entity::where('name', 'payment')->first();
                $statusMapPayment = StatusMap::where('entity', $paymentEntity->id)->where('entity_id', $order->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
                if ($statusMapPayment->status_id <> $statusPayment->id) {
                    continue;
                }

                $productAttribute = ProductAttribute::where('product_id', $cartItemItem->product_id)->where('attribute_id', $cartItemItem->attribute_id)->whereNull('deleted_at')->first();

                if (!isset($productSellPerCartOrder['Order:' . $cartItemItem->order_id . '_Cart:' . $cartItemItem->cart_id . '_ProductAttribute:' . $productAttribute->id])) {
                    $productSellPerCartOrder['Order:' . $cartItemItem->order_id . '_Cart:' . $cartItemItem->cart_id . '_ProductAttribute:' . $productAttribute->id] = [];
                    $productSellPerCartOrder['Order:' . $cartItemItem->order_id . '_Cart:' . $cartItemItem->cart_id . '_ProductAttribute:' . $productAttribute->id . '_computed'] = 0;
                }

                $productSellPerCartOrder['Order:' . $cartItemItem->order_id . '_Cart:' . $cartItemItem->cart_id . '_ProductAttribute:' . $productAttribute->id][] = $cartItemItem->quantity;
                $productSellPerCartOrder['Order:' . $cartItemItem->order_id . '_Cart:' . $cartItemItem->cart_id . '_ProductAttribute:' . $productAttribute->id . '_computed'] += $cartItemItem->quantity;

                if ($productSellPerCartOrder['Order:' . $cartItemItem->order_id . '_Cart:' . $cartItemItem->cart_id . '_ProductAttribute:' . $productAttribute->id . '_computed'] < 0) {
                    $productSellPerCartOrder['Order:' . $cartItemItem->order_id . '_Cart:' . $cartItemItem->cart_id . '_ProductAttribute:' . $productAttribute->id . '_computed'] = 0;
                }
            }

            foreach ($productSellPerCartOrder as $key => $value) {
                if (strpos($key, '_computed')) {
                    $product['sell_via_order'] += $value;
                }
            }

            $product['sell_object_order'] = $productSellPerCartOrder;

            $product['sell_via_inventory'] = 0;
            $product['sell_object_inventory'] = null;

            $product['stock'] = 0;

            $statusMap = StatusMap::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($statusMap)) {
                $status = Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first();
                $product['status'] = (!empty($status)) ? $status->name : null;
            } else {
                $product['status'] = null;
            }

            $productInventory = [];
            $productSellPerInventory = [];
            $productInventoryQuery = \DB::table('product_inventory')
                ->leftJoin('product_attribute', 'product_attribute.id', '=', 'product_inventory.product_attribute_id')
                ->leftJoin('product', 'product.id', '=', 'product_attribute.product_id')
                ->leftJoin('attribute', 'attribute.id', '=', 'product_attribute.attribute_id')
                ->select('product_inventory.*', 'product_attribute.product_id', 'product_attribute.attribute_id')
                ->where('product.id', $product->id)
                ->whereNull('product_inventory.deleted_at')
                ->whereNull('product_attribute.deleted_at')
                ->whereNull('product.deleted_at')
                ->whereNull('attribute.deleted_at')
            ;

            $productInventoryListArray = $productInventoryQuery->get()->toArray();

            foreach ($productInventoryListArray as $productInventoryItem) {
                if (!isset($productInventory[$productInventoryItem->product_attribute_id])) {
                    $productInventory[$productInventoryItem->product_attribute_id] = array(
                        'product_attribute_id' => $productInventoryItem->product_attribute_id,
                        'product_id' => $productInventoryItem->product_id,
                        'attribute_id' => $productInventoryItem->attribute_id,
                        'stock' => $productInventoryItem->stock,
                    );
                } else {
                    $productInventory[$productInventoryItem->product_attribute_id]['stock'] += $productInventoryItem->stock;
                }

                if ($productInventoryItem->order_id != null) {
                    $product['sell_via_inventory'] += abs($productInventoryItem->stock);
                    $productSellPerInventory[] = $productInventoryItem;
                }
            }

            $product['sell_object_inventory'] = $productSellPerInventory;

            $product['sell'] = $product['sell_via_inventory'];

            // Comment to enable debugging
            unset($product['sell_via_order']);
            unset($product['sell_object_order']);
            unset($product['sell_via_inventory']);
            unset($product['sell_object_inventory']);

            $productAttributeList = [];
            $productStock = 0;
            foreach ($productInventory as $productInventoryItem) {
                $productAttribute = ProductAttribute::where('id', $productInventoryItem['product_attribute_id'])->whereNull('deleted_at')->first();
                $productAttributeStock = (int) $productInventoryItem['stock'];
                $productStock += $productAttributeStock;

                if (empty($productAttribute)) {
                    continue;
                }

                $productAttribute->stock = $productAttributeStock;
                $productAttribute->size = null;
                $productAttribute->color = null;
                $productAttribute->other = null;

                $attribute = Attribute::where('id', $productAttribute->attribute_id)->whereNull('deleted_at')->first();
                if (empty($attribute)) {
                    $productAttribute = null;
                    continue;
                }

                // Replace product attribute id with attribute id
                $productAttribute->id = $attribute->id;
                unset($productAttribute->product_id);
                unset($productAttribute->attribute_id);

                $productAttribute->other = $attribute->other;

                if (!empty($attribute->size_id)) {
                    $productAttribute->size = Size::where('id', $attribute->size_id)->whereNull('deleted_at')->first();
                }

                if (!empty($attribute->color_id)) {
                    $productAttribute->color = Color::where('id', $attribute->color_id)->whereNull('deleted_at')->first();
                }

                $productAttributeList[] = $productAttribute;
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

        return response()->json($product, 200);$productAttribute;
    }

    public function productGetMinimal(int $id, Request $request = null)
    {
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (!empty($product)) {
            $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();

            // LANGUAGE Translation
            $product->name = Language::translate($request, $product, 'name');
            $product->description = Language::translate($request, $product, 'description');

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
    public function productDelete(int $id, Request $request)
    {
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $product->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        $productEntity = Entity::where('name', $product->getTable())->first();

        $categoryMap = CategoryMap::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($categoryMap)) {
            $categoryMap->update($request->only([
                'deleted_at',
                'deleted_by',
            ]));
        }

        $statusMap = StatusMap::where('entity', $productEntity->id)->where('entity_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($statusMap)) {
            $statusMap->update($request->only([
                'deleted_at',
                'deleted_by',
            ]));
        }

        $productAttribute = ProductAttribute::where('product_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($productAttribute)) {
            $productAttribute->update($request->only([
                'deleted_at',
                'deleted_by',
            ]));

            $productInventory = ProductInventory::where('product_attribute_id', $productAttribute->id)->whereNull('deleted_at')->first();
            if (!empty($productInventory)) {
                $productInventory->update($request->only([
                    'deleted_at',
                    'deleted_by',
                ]));
            }
        }

        $productPricing = ProductPricing::where('product_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($productPricing)) {
            $productPricing->update($request->only([
                'deleted_at',
                'deleted_by',
            ]));
        }

        $productDiscount = ProductDiscount::where('product_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($productDiscount)) {
            $productDiscount->update($request->only([
                'deleted_at',
                'deleted_by',
            ]));
        }

        $productShipping = ProductShipping::where('product_id', $product->id)->whereNull('deleted_at')->first();
        if (!empty($productShipping)) {
            $productShipping->update($request->only([
                'deleted_at',
                'deleted_by',
            ]));
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
    public function productModify(int $id, Request $request)
    {
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();

        if (isset($request->sku)) {
            $request->request->add(['sku' => $request->sku]);
        }

        if (isset($request->name_en)) {
            $request->request->add(['name_en' => $request->name_en]);
        }

        if (isset($request->name_tc)) {
            $request->request->add(['name_tc' => $request->name_tc]);
        }

        if (isset($request->name_sc)) {
            $request->request->add(['name_sc' => $request->name_sc]);
        }

        if (isset($request->description_en)) {
            $request->request->add(['description_en' => $request->description_en]);
        }

        if (isset($request->description_tc)) {
            $request->request->add(['description_tc' => $request->description_tc]);
        }

        if (isset($request->description_sc)) {
            $request->request->add(['description_sc' => $request->description_sc]);
        }

        if (isset($request->shop_id)) {
            $shopQuery = \DB::table('shop')
                ->leftJoin('user', 'user.id', '=', 'shop.user_id')
                ->select('shop.*')
                ->where('shop.id', $request->shop_id)
                ->whereNull('shop.deleted_at');

            if ($request->filter_inactive == true) {
                $shopQuery
                    ->whereNull('user.deleted_at');
            }

            $shop = $shopQuery->first();

            if (empty($shop)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid shop id',
                ], 400);
            }

            $shop = Shop::where('id', $shop->id)->whereNull('deleted_at')->first();

            $request->request->add(['shop_id' => $request->shop_id]);
        }

        $productEntity = Entity::where('name', $product->getTable())->first();

        if (isset($request->category_id) || $request->category_id === "0") {
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

            $var['category_map'] = [
                'entity' => $productEntity->id,
                'entity_id' => $product->id,
                'category_id' => $request->category_id,
            ];
        }

        if (isset($request->status_id)) {
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

            $var['status_map'] = [
                'entity' => $productEntity->id,
                'entity_id' => $product->id,
                'status_id' => $request->status_id,
            ];
        }

        if (isset($request->price_original)) {
            if ($request->price_original < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid original price',
                ], 400);
            }

            $var['product_pricing'] = [
                'product_id' => $product->id,
                'price' => abs($request->price_original),
            ];
        }

        if (isset($request->price_discounted)) {
            if ($request->price_discounted == 0) {
                $discountedAmount = 0.00;
            } else {
                $productPricing = ProductPricing::where('product_id', $product->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
                if ($request->price_discounted < 0 || $productPricing->price <= $request->price_discounted) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid discounted price',
                    ], 400);
                }

                $discountedAmount = $productPricing->price - $request->price_discounted;
            }

            $var['product_discount'] = [
                'product_id' => $product->id,
                'type' => 'fixed',
                'amount' => abs($discountedAmount),
            ];
        }

        if (isset($request->shipping_price) || $request->shipping_price == 0) {
            if ($request->shipping_price < 0.00) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid shipping price',
                ], 400);
            }

            $var['product_shipping'] = [
                'product_id' => $product->id,
                'amount' => abs($request->shipping_price),
            ];
        }

        $request->request->add([
            'updated_by' => $request->access_token_user_id,
        ]);

        $product->update($request->all());

        $request->request->add([
            'created_by' => $request->access_token_user_id,
        ]);

        if (isset($request->category_id)) {
            $request->request->add([
                'entity' => $var['category_map']['entity'],
                'entity_id' => $var['category_map']['entity_id'],
                'category_id' => $var['category_map']['category_id'],
            ]);

            $categoryMap = CategoryMap::create($request->only([
                'entity',
                'entity_id',
                'category_id',
                'created_by',
                'updated_by',
            ]));
        }

        if (isset($request->status_id)) {
            $request->request->add([
                'entity' => $var['status_map']['entity'],
                'entity_id' => $var['status_map']['entity_id'],
                'status_id' => $var['status_map']['status_id'],
            ]);

            $statusMap = StatusMap::create($request->only([
                'entity',
                'entity_id',
                'status_id',
                'created_by',
                'updated_by',
            ]));
        }

        if (isset($request->price_original)) {
            $request->request->add([
                'product_id' => $var['product_pricing']['product_id'],
                'price' => $var['product_pricing']['price'],
            ]);

            $productPricing = ProductPricing::create($request->only([
                'product_id',
                'price',
                'created_by',
                'updated_by',
            ]));
        }

        if (isset($request->price_discounted)) {
            $request->request->add([
                'product_id' => $var['product_discount']['product_id'],
                'type' => $var['product_discount']['type'],
                'amount' => $var['product_discount']['amount'],
            ]);

            $productDiscount = ProductDiscount::create($request->only([
                'product_id',
                'type',
                'amount',
                'created_by',
                'updated_by',
            ]));
        }

        if (isset($request->shipping_price)) {
            $request->request->add([
                'product_id' => $var['product_shipping']['product_id'],
                'amount' => $var['product_shipping']['amount'],
            ]);

            $productShipping = ProductShipping::create($request->only([
                'product_id',
                'amount',
                'created_by',
                'updated_by',
            ]));
        }

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
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();

        if (empty($request->stock) || $request->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid stock value',
            ], 400);
        }

        $productAttribute = new Attribute();
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

                $productAttribute = Attribute::create($request->all());
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
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();

        if (empty($request->stock) || $request->stock > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid stock value',
            ], 400);
        }

        if (!empty($request->attribute_id) && empty(Attribute::where('id', $request->attribute_id)->whereNull('deleted_at')->first())) {
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
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $request->product_id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();

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
    public function productAttributeDelete(int $attribute_id, Request $request)
    {
        $productAttribute = Attribute::where('id', $attribute_id)->whereNull('deleted_at')->first();
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
        $productAttribute = Attribute::where('id', $attribute_id)->whereNull('deleted_at')->first();
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

    /**
     * @OA\Put(
     *     path="/api/productstock/{product_id}",
     *     operationId="productStockPut",
     *     tags={"Product"},
     *     summary="Modifies product attribute and stock",
     *     description="Modifies product attribute and stock given the product id, attributes, stock.",
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
     *         name="product_id",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="attribute_id",
     *         in="query",
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
     *     @OA\Parameter(
     *         name="stock",
     *         in="query",
     *         description="The product stock (Optional)",
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
     *         description="Returns the product stock update failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productStockPut(int $product_id, Request $request) {
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $product_id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $attribute = Attribute::where('id', $request->attribute_id)->whereNull('deleted_at')->first();

        if (empty($attribute)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attribute id',
            ], 400);
        }

        $productAttribute = ProductAttribute::where('product_id', $product_id)->where('attribute_id', $request->attribute_id)->whereNull('deleted_at')->first();

        if (empty($productAttribute)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attribute for the product',
            ], 400);
        }

        $attributeSearch = new Attribute();
        $attributeDraft = [];

        if (isset($request->size_id)) {
            $size = Size::where('id', $request->size_id)->whereNull('deleted_at')->first();
            if (empty($size)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid size id',
                ], 400);
            } else {
                $attributeDraft['size_id'] = $size->id;
                $attributeSearch = $attributeSearch->where('size_id', $size->id);
            }
        }

        if (isset($request->color_id)) {
            $color = Color::where('id', $request->color_id)->whereNull('deleted_at')->first();
            if (empty($color)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid color id',
                ], 400);
            } else {
                $attributeDraft['color_id'] = $color->id;
                $attributeSearch = $attributeSearch->where('color_id', $color->id);
            }
        }

        if (isset($request->other)) {
            $attributeDraft['other'] = $request->other;
            $attributeSearch = $attributeSearch->where('other', $request->other);
        }

        if (isset($request->stock) && $request->stock < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid stock value',
            ], 400);
        }

        $stock = $request->stock;

        if (empty($attributeDraft)) {
            return response()->json([
                'success' => false,
                'message' => 'Attribute required (Product should have at least size, color, remark to have a stock)',
            ], 400);
        }

        foreach ($attributeDraft as $key => $value) {
            $request->request->add([$key => $value]);
        }

        $attributeSearch = $attributeSearch->first();

        // Changes made leads to new product attribute
        if (empty($attributeSearch)) {
            $this->deleteProductAttribute($productAttribute, $request);
            $this->clearStock($productAttribute->id, $request);

            $request->request->add([
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            $attribute = Attribute::create($request->only([
                'size_id',
                'color_id',
                'other',
                'created_by',
                'updated_by',
            ]));

            $productAttributeNew = $this->createProductAttribute($product->id, $attribute->id, $request);
            $this->addStock($productAttributeNew->id, $stock, $request);

        // Changes made leads to existing product attribute
        } else if ($attributeSearch->id <> $attribute->id) {
            $this->deleteProductAttribute($productAttribute, $request);
            $this->clearStock($productAttribute->id, $request);
            $productAttributeNew = ProductAttribute::where('product_id', $product->id)->where('attribute_id', $attributeSearch->id)->whereNull('deleted_at')->first();
            if (empty($productAttributeNew)) {
                $productAttributeNew = $this->createProductAttribute($product->id, $attributeSearch->id, $request);
                $this->addStock($productAttributeNew->id, $stock, $request);
            } else {
                $this->replaceStock($productAttributeNew->id, $stock, $request);
            }

        // Changes made leads to same product attribute
        } else {
            $this->replaceStock($productAttribute->id, $stock, $request);
        }

        return response()->json(self::productGet($product->id, $request)->getData(), 201);
    }

    /**
     * @OA\Post(
     *     path="/api/productstock/{product_id}",
     *     operationId="productStockPost",
     *     tags={"Product"},
     *     summary="Adds product attribute and stock",
     *     description="Adds product attribute and stock given the product id, attributes, stock.",
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
     *         name="product_id",
     *         in="path",
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
     *     @OA\Parameter(
     *         name="stock",
     *         in="query",
     *         description="The product stock",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the product with the added stocks",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product stock add failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productStockPost(int $product_id, Request $request) {
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $product_id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();

        $attribute = new Attribute();
        $attributeDraft = [];

        if (isset($request->size_id)) {
            $size = Size::where('id', $request->size_id)->whereNull('deleted_at')->first();
            if (empty($size)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid size id',
                ], 400);
            } else {
                $attributeDraft['size_id'] = $size->id;
                $attribute = $attribute->where('size_id', $size->id);
            }
        }

        if (isset($request->color_id)) {
            $color = Color::where('id', $request->color_id)->whereNull('deleted_at')->first();
            if (empty($color)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid color id',
                ], 400);
            } else {
                $attributeDraft['color_id'] = $color->id;
                $attribute = $attribute->where('color_id', $color->id);
            }
        }

        if (isset($request->other)) {
            $attributeDraft['other'] = $request->other;
            $attribute = $attribute->where('other', $request->other);
        }

        if (!isset($request->stock) || $request->stock < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid stock value',
            ], 400);
        }

        $stock = $request->stock;

        if (empty($attributeDraft)) {
            return response()->json([
                'success' => false,
                'message' => 'Attribute required (Product should have at least size, color, remark to have a stock)',
            ], 400);
        }

        foreach ($attributeDraft as $key => $value) {
            $request->request->add([$key => $value]);
        }

        $attribute = $attribute->first();

        if (empty($attribute)) {
            $request->request->add([
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            $attribute = Attribute::create($request->only([
                'size_id',
                'color_id',
                'other',
                'created_by',
                'updated_by',
            ]));
        }

        $productAttribute = ProductAttribute::where('product_id', $product_id)->where('attribute_id', $attribute->id)->whereNull('deleted_at')->first();
        if (empty($productAttribute)) {
            $productAttribute = $this->createProductAttribute($product_id, $attribute->id, $request);
        }

        $this->addStock($productAttribute->id, $stock, $request);

        return response()->json(self::productGet($product->id, $request)->getData(), 201);
    }

     /**
     * @OA\Delete(
     *     path="/api/productstock/{product_id}",
     *     operationId="productStockDelete",
     *     tags={"Product"},
     *     summary="Deletes product attribute",
     *     description="Deletes product attribute and stock given the product id, attribute id.",
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
     *         name="product_id",
     *         in="path",
     *         description="The product id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="attribute_id",
     *         in="query",
     *         description="The attribute id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the product",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the product stock delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function productStockDelete(int $product_id, Request $request) {
        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $product_id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->whereNull('shop.deleted_at')
                ->whereNull('user.deleted_at');
        }

        $product = $productQuery->first();

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product id',
            ], 400);
        }

        $attribute = Attribute::where('id', $request->attribute_id)->whereNull('deleted_at')->first();

        if (empty($attribute)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attribute id',
            ], 400);
        }

        $productAttribute = ProductAttribute::where('product_id', $product_id)->where('attribute_id', $request->attribute_id)->whereNull('deleted_at')->first();

        if (empty($productAttribute)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attribute for the product',
            ], 400);
        }

        $this->deleteProductAttribute($productAttribute, $request);
        $this->clearStock($productAttribute->id, $request);

        return response()->json(self::productGet($product->id, $request)->getData(), 201);
    }

    private function createProductAttribute(int $product_id, int $attribute_id, Request $request) {
        $request->request->add([
            'product_id' => $product_id,
            'attribute_id' => $attribute_id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $productAttribute = ProductAttribute::create($request->only([
            'product_id',
            'attribute_id',
            'created_by',
            'updated_by',
        ]));

        return $productAttribute;
    }

    private function deleteProductAttribute(ProductAttribute $productAttribute, Request $request) {
        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $productAttribute->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return true;
    }

    private function addStock(int $product_attribute_id, int $stock, Request $request) {
        $request->request->add([
            'product_attribute_id' => $product_attribute_id,
            'stock' => abs($stock),
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        ProductInventory::create($request->only([
            'product_attribute_id',
            'stock',
            'created_by',
            'updated_by',
        ]));

        return true;
    }

    private function subtractStock(int $product_attribute_id, int $stock, Request $request) {
        $request->request->add([
            'product_attribute_id' => $product_attribute_id,
            'stock' => -1 * abs($stock),
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        ProductInventory::create($request->only([
            'product_attribute_id',
            'stock',
            'created_by',
            'updated_by',
        ]));

        return true;
    }

    private function clearStock(int $product_attribute_id, Request $request) {
        $stock = $this->getStock($product_attribute_id);

        $this->subtractStock($product_attribute_id, $stock, $request);

        return true;
    }

    private function replaceStock(int $product_attribute_id, int $stock, Request $request) {
        $this->clearStock($product_attribute_id, $request);
        $this->addStock($product_attribute_id, $stock, $request);

        return true;
    }

    public function getStock(int $product_attribute_id) {
        $stock = 0;

        $productInventoryList = ProductInventory::where('product_attribute_id', $product_attribute_id)->whereNull('deleted_at')->get();
        foreach ($productInventoryList as $productInventoryItem) {
            $stock += $productInventoryItem->stock;
        }

        return $stock;
    }
}

