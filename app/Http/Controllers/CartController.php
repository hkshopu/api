<?php

namespace App\Http\Controllers;

use App\Cart;
use App\CartItem;
use App\Shop;
use App\Product;
use App\Attribute;
use App\ProductAttribute;
use App\ProductInventory;
use App\Size;
use App\Color;
use App\PaymentMethod;
use App\ShopPaymentMethodMap;
use App\ProductShipping;
use App\Shipment;
use App\ShopShipmentMap;
use App\Entity;
use App\Status;
use App\StatusMap;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class CartController extends Controller
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
     *     path="/api/cart/{cart_id}",
     *     operationId="cartGet",
     *     tags={"Cart"},
     *     summary="Retrieves all cart item",
     *     description="
Retrieves all cart item of the consumer/guest.
<br /><br />
If token is provided, the system will recognize the cart as Consumer cart, no need for a <strong>cart_id</strong>.
<br /><br />
If no token is provided, it will need the <strong>cart_id</strong> to retrieve the Guest cart. Otherwise will throw an empty result.
          ",
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         description="The access token for authentication (For <strong>Consumer</strong> Account)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="cart_id",
     *         in="path",
     *         description="The cart id (For <strong>Guest</strong> Account)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns all cart item",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the cart item get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function cartGet(string $cart_id = null, Request $request = null)
    {
        if (isset($cart_id)) {
            $cart = Cart::where('id', $cart_id)->whereNull('deleted_at')->first();
        }

        if (empty($cart)) {
            if (isset($request->access_token_user_id)) {
                $cart = Cart::where('user_id', $request->access_token_user_id)->whereNull('deleted_at')->first();
            }
        }

        $data = [
            'cart_id' => $cart->id ?? null,
            'shop' => [],
        ];

        if (empty($cart)) {
            return response()->json($data, 200);
        }

        $cartItemList = CartItem::where('cart_id', $cart->id)->whereNull('order_id')->whereNull('deleted_at')->get();

        $cartItemListIds = [];

        foreach ($cartItemList as $cartItemItem) {
            $productInfo = app('App\Http\Controllers\ProductController')->productGet($cartItemItem->product_id, $request, false)->getData();
            if (!empty($productInfo->id)) {
                $cartItemListIds[] = $cartItemItem->id;
            }
        }

        // Re-validate active product list
        $cartItemListActive = CartItem::where('cart_id', $cart->id)->whereNull('order_id')->whereNull('deleted_at');

        $cartItemListActive->whereIn('id', $cartItemListIds);
        $cartItemList = $cartItemListActive->get();

        if (!empty($cartItemList)) {
            $data['shop'] = $this->cartItemList($cartItemList, $request)->getData();
        }

        return response()->json($data, 200);
    }

    public function cartItemList(Collection $cartItemList = null, Request $request = null, bool $generateFromOrder = false) {
        $request->request->add([
            'filter_inactive' => !$generateFromOrder,
        ]);

        $data = [
            'shop' => [],
        ];

        $newList = [];

        foreach ($cartItemList as $cartItem) {
            $cartItemIndex = "{$cartItem->cart_id}:{$cartItem->product_id}:{$cartItem->attribute_id}";
            if (isset($newList[$cartItemIndex])) {
                $newList[$cartItemIndex]['quantity'] += $cartItem->quantity;

                // Avoid negative quantity
                if ($newList[$cartItemIndex]['quantity'] < 0) {
                    $newList[$cartItemIndex]['quantity'] = 0;
                }
            } else {
                $newList[$cartItemIndex]['cart_item_id'] = $cartItem->id;
                $newList[$cartItemIndex]['quantity'] = $cartItem->quantity;
            }
        }

        $newerList = [];

        foreach ($newList as $itemKey => $newItem) {
            $item = [];
            $itemKeyArray = explode(':', $itemKey);
            $item['id'] = $newItem['cart_item_id'];
            $item['cart_id'] = $itemKeyArray[0];
            $item['product_id'] = $itemKeyArray[1];
            $item['attribute_id'] = $itemKeyArray[2];
            $item['quantity'] = $newItem['quantity'];
            $newerList[] = $item;
        }

        $cartItemList = $newerList;

        $productGroupList = [];

        foreach ($cartItemList as $cartItem) {
            $productQuery = \DB::table('product')
                ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
                ->leftJoin('user', 'user.id', '=', 'shop.user_id')
                ->select('product.*')
                ->where('product.id', $cartItem['product_id'])
                ->whereNull('product.deleted_at');

            if ($request->filter_inactive == true) {
                $productQuery
                    ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                    ->whereNotNull('shop_payment_method_map.id')
                    ->whereNull('shop_payment_method_map.deleted_at')
                    ->groupBy('product.id')
                    ->whereNull('shop.deleted_at')
                    ->whereNull('user.deleted_at');
            }

            $product = $productQuery->first();

            if (!empty($product)) {
                $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();
                $productGroupList[$product->shop_id][] = [
                    'cart_item_id' => $cartItem['id'],
                    'quantity' => $cartItem['quantity'],
                ];
            }
        }

//SAVE.THIS        echo "=== Raw Version: \r\n\r\n";
        $shopCtr = 0;

        foreach ($productGroupList as $shopId => $productGroup) {
            $shop = app('App\Http\Controllers\ShopController')->shopGet($shopId, $request)->getData();

            if (!empty($shop) && !empty($shop->id)) {
                $paymentMethodList = ShopPaymentMethodMap::where('shop_id', $shop->id)->whereNull('deleted_at')->orderBy('payment_method_id', 'ASC')->get();
                foreach ($paymentMethodList as $key => $paymentMethodItem) {
                    $tempItem = [];
                    $paymentMethod = PaymentMethod::where('id', $paymentMethodItem->payment_method_id)->whereNull('deleted_at')->first();
                    $tempItem['id'] = $paymentMethod->id;
                    $tempItem['name'] = $paymentMethod->name;
                    $tempItem['code'] = $paymentMethod->code;
                    $tempItem['account_info'] = $paymentMethodItem->account_info;
                    $tempItem['remarks'] = $paymentMethodItem->remarks;

                    $paymentMethodList[$key] = $tempItem;
                }

                $shipmentId = null;
                $shipmentType = null;
                $shipmentLabel = null;
                $shipmentQuota = null;

                $shopShipmentMap = ShopShipmentMap::where('shop_id', $shop->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
                if (!empty($shopShipmentMap)) {
                    $shipment = Shipment::where('id', $shopShipmentMap->shipment_id)->whereNull('deleted_at')->first();
                    if (!empty($shipment)) {
                        $shipmentId = $shipment->id;
                        $shipmentType = $shipment->name;
                        $shipmentLabel = $shipment->label;
                        $shipmentQuota = $shopShipmentMap->amount;
                    }
                }

                $data['shop'][$shopCtr] = [
                    'shop_id' => $shop->id,
                    'logo_url' => $shop->logo_url,
                    'name' => $shop->name,
                    'name_en' => $shop->name_en,
                    'name_tc' => $shop->name_tc,
                    'name_sc' => $shop->name_sc,
                    'payment_method' => $paymentMethodList,
                    'cart_date' => '',
                    'shop_date' => $shop->created_at,
                    'total_quantity' => 0,
                    'total_amount' => 0.00,
                    'total_amount_discounted' => 0.00,
                    'shipment_id' => $shipmentId,
                    'shipment_type' => $shipmentType,
                    'shipment_label' => $shipmentLabel,
                    'shipment_quota' => $shipmentQuota,
                    'shipment_fee_computed' => 0.00,
                    'shop_cart_total' => 0.00,
                ];

                $data['shop'][$shopCtr]['product'] = [];
                foreach ($productGroup as $productKey => $cartItemItem) {
                    $cartItem = CartItem::where('id', $cartItemItem['cart_item_id'])->whereNull('deleted_at')->first();
                    if (!empty($cartItem)) {
                        $product = app('App\Http\Controllers\ProductController')->productGetClone($cartItem->product_id, $request)->getData();
                        if (!empty($product)) {
                            $productShipping = ProductShipping::where('product_id', $cartItem->product_id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
                            if (!empty($productShipping)) {
                                $shippingPrice = $productShipping->amount;
                            } else {
                                $shippingPrice = 0.00;
                            }

                            $attribute = Attribute::where('id', $cartItem->attribute_id)->whereNull('deleted_at')->first();
                            $attribute['color'] = Color::where('id', $attribute->color_id)->whereNull('deleted_at')->first();
                            $attribute['size'] = Size::where('id', $attribute->size_id)->whereNull('deleted_at')->first();

//SAVE.THIS                            echo "Cart: {$cartItem->cart_id}, Product: {$cartItem->product_id}, Attribute: {$cartItem->attribute_id}, Quantity: {$cartItem->quantity} \r\n";

                            $cartItemQuantity = $cartItemItem['quantity'];

                            $orderItemStatus = null;
                            $orderItemEntity = Entity::where('name', 'order_item')->first();
                            $statusMap = StatusMap::where('entity', $orderItemEntity->id)->where('entity_id', $cartItem->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
                            if (!empty($statusMap)) {
                                $status = Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first();
                                if (!empty($status)) {
                                    $orderItemStatus = $status->name;
                                }
                            }

                            $data['shop'][$shopCtr]['product'][$productKey] = [
                                'cart_item_id' => $cartItem->id,
                                'product_id' => $cartItem->product_id,
                                'attribute_id' => $cartItem->attribute_id,
                                'attribute' => $attribute,
                                'image_url' => $product->image[0]->url ?? null,
                                'name' => $product->name,
                                'name_en' => $product->name_en,
                                'name_tc' => $product->name_tc,
                                'name_sc' => $product->name_sc,
                                'shop_name' => $shop->name,
                                'shop_name_en' => $shop->name_en,
                                'shop_name_tc' => $shop->name_tc,
                                'shop_name_sc' => $shop->name_sc,
                                'description' => $product->description,
                                'description_en' => $product->description_en,
                                'description_tc' => $product->description_tc,
                                'description_sc' => $product->description_sc,
                                'cart_date' => $cartItem->created_at->format('Y-m-d H:i:s'),
                                'product_date' => $product->created_at,
                                'price' => $product->price_original,
                                'price_discounted' => $product->price_discounted,
                                'quantity' => $cartItemQuantity,
                                'total_price' => $cartItemQuantity * $product->price_original,
                                'total_price_discounted' => !empty($product->price_discounted) ? $cartItemQuantity * $product->price_discounted : null,
                                'shipping_price' => $shippingPrice,
                                'shipping_price_total' => $cartItemQuantity * $shippingPrice,
                                'order_item_status' => $orderItemStatus,
                            ];

                            $data['shop'][$shopCtr]['cart_date'] = $cartItem->created_at->format('Y-m-d H:i:s');
                            $data['shop'][$shopCtr]['total_quantity'] += $cartItemQuantity;
                            $data['shop'][$shopCtr]['total_amount'] += ($cartItemQuantity * $product->price_original);
                            $data['shop'][$shopCtr]['total_amount_discounted'] += $cartItemQuantity * (!empty($product->price_discounted) ? $product->price_discounted : $product->price_original);
                            $data['shop'][$shopCtr]['shipment_fee_computed'] += ($cartItemQuantity * $shippingPrice);
                        }
                    }
                }

                switch ($shipmentType) {
                    case 'normal':
                        
                    break;
                    case 'all':
                        $data['shop'][$shopCtr]['shipment_fee_computed'] = 0.00;
                    break;
                    case 'over':
                        if ($data['shop'][$shopCtr]['total_amount_discounted'] > $data['shop'][$shopCtr]['shipment_quota']) {
                            $data['shop'][$shopCtr]['shipment_fee_computed'] = 0.00;
                        }
                    break;
                }

                $data['shop'][$shopCtr]['shop_cart_total'] = $data['shop'][$shopCtr]['total_amount_discounted'] + $data['shop'][$shopCtr]['shipment_fee_computed'];
            }
            $shopCtr++;
        }
//SAVE.THIS        echo "\r\n";
//SAVE.THIS        echo "=== Simplified Version: \r\n\r\n";
        // Remove items with zero quantity in cart
        $newShopData = [];
        foreach ($data['shop'] as $shop) {
            $newProductData = [];
            foreach ($shop['product'] as $product) {
//SAVE.THIS                echo "Cart Item ID: {$product['cart_item_id']}, Product: {$product['product_id']}, Attribute: {$product['attribute_id']}, Quantity: {$product['quantity']} \r\n";
                if ($product['quantity'] > 0) {
                    if ($product['total_price_discounted'] <= 0) {
                        $product['total_price_discounted'] = null;
                    }
                    $newProductData[] = $product;
                }
            }

            if (!empty($newProductData)) {
                $shop['product'] = $newProductData;
                $newShopData[] = $shop;
            }
        }

        return response()->json($newShopData, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/cart",
     *     operationId="cartAdd",
     *     tags={"Cart"},
     *     summary="Adds a cart item",
     *     description="
Adds a cart item for the consumer/guest.
<br /><br />
If token is provided, the system will recognize the cart as Consumer cart, no need for a <strong>cart_id</strong>.
<br /><br />
If no token is provided, and no <strong>cart_id</strong>, it will generate a new one for the assumed new Guest cart.
<br /><br />
If no token is provided, but has <strong>cart_id</strong>, it will populate the existing Guest cart.
           ",
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
     *         name="cart_id",
     *         in="query",
     *         description="The cart id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="The product id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="attribute_id",
     *         in="query",
     *         description="The attribute id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="quantity",
     *         in="query",
     *         description="The quantity",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the cart with the added item",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the cart item add failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function cartAdd(Request $request = null)
    {
        if (!isset($request->access_token_user_id)) {
            if (isset($request->cart_id)) {
                $cart = Cart::where('id', $request->cart_id)->whereNull('user_id')->whereNull('deleted_at')->first();
                if (empty($cart)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid cart id',
                    ], 400);
                }
            } else {
                $cart = Cart::create();
                $request->request->add([
                    'cart_id' => $cart->id,
                ]);
            }
        } else {
            $request->request->add([
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            $cart = Cart::where('user_id', $request->access_token_user_id)->whereNull('deleted_at')->first();
            if (empty($cart)) {
                $request->request->add([
                    'user_id' => $request->access_token_user_id,
                ]);
                $cart = Cart::create($request->only([
                    'user_id',
                    'created_by',
                    'updated_by',
                ]));
                $request->request->add([
                    'cart_id' => $cart->id,
                ]);
            } else {
                $request->request->add([
                    'cart_id' => $cart->id,
                ]);
            }
        }

        $productQuery = \DB::table('product')
            ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
            ->leftJoin('user', 'user.id', '=', 'shop.user_id')
            ->select('product.*')
            ->where('product.id', $request->product_id)
            ->whereNull('product.deleted_at');

        if ($request->filter_inactive == true) {
            $productQuery
                ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                ->whereNotNull('shop_payment_method_map.id')
                ->whereNull('shop_payment_method_map.deleted_at')
                ->groupBy('product.id')
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

        $productAttribute = ProductAttribute::where('product_id', $product->id)->where('attribute_id', $attribute->id)->whereNull('deleted_at')->first();

        if (empty($productAttribute)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid attribute for the product',
            ], 400);
        }

        if ($request->quantity == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid quantity',
            ], 400);
        }

        $quantityCart = 0;

        $cartItemList = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('attribute_id', $attribute->id)
            ->whereNull('order_id')
            ->whereNull('deleted_at')
            ->get()
        ;

        foreach ($cartItemList as $cartItemItem) {
            $quantityCart += $cartItemItem->quantity;

            if ($quantityCart < 0) {
                $quantityCart = 0;
            }
        }

        $quantityCart += $request->quantity;

        $productStock = ProductInventory::checkStock($request->product_id, $request->attribute_id);

        if ($productStock === null) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product stock',
            ], 400);
        } else if ($productStock < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Product out of stock',
            ], 400);
        } else if ($productStock < $quantityCart) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock',
            ], 400);
        }

        CartItem::create($request->only([
            'cart_id',
            'product_id',
            'attribute_id',
            'quantity',
            'created_by',
            'updated_by',
        ]));

        return response()->json(self::cartGet($cart->id, $request)->getData(), 201);
    }

    /**
     * @OA\Post(
     *     path="/api/carttest",
     *     operationId="cartAddTest",
     *     tags={"Cart"},
     *     summary="Tests cart item parameters",
     *     description="Tests cart item parameters.",
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
     *         name="cart_id",
     *         in="query",
     *         description="The cart id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="The product id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="attribute_id",
     *         in="query",
     *         description="The attribute id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="quantity",
     *         in="query",
     *         description="The quantity",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the post values",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the error sending post values",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function cartAddTest(Request $request = null)
    {
        return response()->json($request->all(), 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/cart",
     *     operationId="cartModify",
     *     tags={"Cart"},
     *     summary="Modifies a cart item",
     *     description="
Modifies a cart item for the consumer/guest.
<br /><br />
If token is provided, the system will recognize the cart as Consumer cart, no need for a <strong>cart_id</strong>.
<br /><br />
If no token is provided, it will need the <strong>cart_id</strong> to update the Guest cart. Otherwise will throw an error.
           ",
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
     *         name="cart_id",
     *         in="query",
     *         description="The cart id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="cart_item_id",
     *         in="query",
     *         description="The cart item id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="The product id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="attribute_id",
     *         in="query",
     *         description="The attribute id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="quantity",
     *         in="query",
     *         description="The quantity",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the cart with the updated item",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the cart item update failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function cartModify(Request $request = null)
    {
        if (!isset($request->access_token_user_id)) {
            $cart = Cart::where('id', $request->cart_id)->whereNull('user_id')->whereNull('deleted_at')->first();
            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid cart id',
                ], 400);
            }
        } else {
            $cart = Cart::where('user_id', $request->access_token_user_id)->whereNull('deleted_at')->first();
        }

        if (!isset($request->cart_item_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item id required',
            ], 400);
        } else if (empty(CartItem::where('id', $request->cart_item_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cart item id',
            ], 400);
        } else if (empty(CartItem::where('id', $request->cart_item_id)->where('cart_id', $cart->id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Item belongs to other cart',
            ], 400);
        }

        if (isset($request->product_id)) {
            $productStock = ProductInventory::checkStock((int) $request->product_id, (int) $request->attribute_id);
            if ($productStock === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid product stock',
                ], 400);
            } else if ($productStock < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product out of stock',
                ], 400);
            }
        } else {
            $request->request->remove('product_id');
            $request->request->remove('attribute_id');
        }

        if (!isset($request->quantity)) {
            $request->request->remove('quantity');
        }

        $request->request->remove('cart_id');
        $request->request->add([
            'updated_by' => $request->access_token_user_id,
        ]);

        $cartItem = CartItem::where('id', $request->cart_item_id)->where('cart_id', $cart->id)->whereNull('deleted_at')->first();
        $cartItem->update($request->all());

        return response()->json(self::cartGet($cart->id, $request)->getData(), 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/cart",
     *     operationId="cartDelete",
     *     tags={"Cart"},
     *     summary="Removes a cart item",
     *     description="
Removes a cart item for the consumer/guest.
<br /><br />
If token is provided, the system will recognize the cart as Consumer cart, no need for a <strong>cart_id</strong>.
<br /><br />
If no token is provided, it will need the <strong>cart_id</strong> to update the Guest cart. Otherwise will throw an error.
<br /><br />
<span style='font-weight:bold;color:red'>IMPORATANT NOTE:</span> Items can be removed via quantity paired with cart item id, or the entire cart item id, or the entire items from a shop id.
           ",
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
     *         name="cart_id",
     *         in="query",
     *         description="The cart id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="shop_id",
     *         in="query",
     *         description="The shop id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="cart_item_id",
     *         in="query",
     *         description="The cart item id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="quantity",
     *         in="query",
     *         description="The quantity",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the cart from the deleted item",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the cart item delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function cartDelete(Request $request = null)
    {
        exec("echo \"" . "[" . Carbon::now()->format('Y-m-d H:i:s') . "] Endpoint: {cart_delete}, Params: " . json_encode($request->all()) . "\" >> " . env('DEBUGGING_LOG_FILE'));

        if (!isset($request->access_token_user_id)) {
            $cart = Cart::where('id', $request->cart_id)->whereNull('user_id')->whereNull('deleted_at')->first();
            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid cart id',
                ], 400);
            }
        } else {
            $cart = Cart::where('user_id', $request->access_token_user_id)->whereNull('deleted_at')->first();
        }

        $deleteUsing = 'none';
        $conditionalCombination = 0;

        if (isset($request->shop_id)) {
            $shopQuery = \DB::table('shop')
                ->leftJoin('user', 'user.id', '=', 'shop.user_id')
                ->select('shop.*')
                ->where('shop.id', $request->shop_id)
                ->whereNull('shop.deleted_at');

            if ($request->filter_inactive == true) {
                $shopQuery
                    ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                    ->whereNotNull('shop_payment_method_map.id')
                    ->whereNull('shop_payment_method_map.deleted_at')
                    ->groupBy('shop.id')
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

            $conditionalCombination += 4;
        }

        if (isset($request->cart_item_id)) {
            $cartItemQuery = CartItem::where('id', $request->cart_item_id)->whereNull('deleted_at');

            if (empty($cartItemQuery->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid cart item id',
                ], 400);
            } else if (empty($cartItemQuery->where('cart_id', $cart->id)->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item belongs to other cart',
                ], 400);
            }

            $cartItem = $cartItemQuery->first();

            $conditionalCombination += 2;
        }

        if (isset($request->quantity)) {
            if ($request->quantity > 0) {
                $conditionalCombination += 1;
            }
        }

        if (in_array($conditionalCombination, [4, 5])) {
            $deleteUsing = 'shop_id';
        } else if (in_array($conditionalCombination, [2, 6])) {
            $deleteUsing = 'cart_item_id';
        } else if (in_array($conditionalCombination, [3, 7])) {
            $deleteUsing = 'quantity';
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        switch ($deleteUsing) {
            case 'shop_id':
                $cartItemList = CartItem::where('cart_id', $cart->id)
                                        ->whereNull('order_id')
                                        ->whereNull('deleted_at')
                                        ->get();

                foreach ($cartItemList as $cartItem) {
                    $productQuery = \DB::table('product')
                        ->leftJoin('shop', 'shop.id', '=', 'product.shop_id')
                        ->leftJoin('user', 'user.id', '=', 'shop.user_id')
                        ->select('product.*')
                        ->where('product.id', $cartItem->product_id)
                        ->whereNull('product.deleted_at');

                    if ($request->filter_inactive == true) {
                        $productQuery
                            ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                            ->whereNotNull('shop_payment_method_map.id')
                            ->whereNull('shop_payment_method_map.deleted_at')
                            ->groupBy('product.id')
                            ->whereNull('shop.deleted_at')
                            ->whereNull('user.deleted_at');
                    }

                    $product = $productQuery->first();

                    if (!empty($product)) {
                        $product = Product::where('id', $product->id)->whereNull('deleted_at')->first();
                        if ($request->shop_id == $product->shop_id) {
                            $cartItem->update($request->only([
                                'deleted_at',
                                'deleted_by',
                            ]));
                        }
                    }
                }
            break;
            case 'cart_item_id':
                $cartItem = CartItem::where('id', $request->cart_item_id)->where('cart_id', $cart->id)->whereNull('deleted_at')->first();

                $cartItemToDeleteArray = CartItem::where('cart_id', $cartItem->cart_id)
                                                ->where('product_id', $cartItem->product_id)
                                                ->where('attribute_id', $cartItem->attribute_id)
                                                ->whereNull('order_id')
                                                ->whereNull('deleted_at')
                                                ->get()
                ;

                foreach ($cartItemToDeleteArray as $cartItemToDelete) {
                    $cartItemToDelete->update($request->only([
                        'deleted_at',
                        'deleted_by',
                    ]));
                }
            break;
            case 'quantity':
                $request->request->add([
                    'cart_id' => $cart->id,
                    'product_id' => $cartItem->product_id,
                    'attribute_id' => $cartItem->attribute_id,
                    'quantity' => -1 * abs($request->quantity),
                    'created_by' => $request->access_token_user_id,
                    'updated_by' => $request->access_token_user_id,
                ]);

                CartItem::create($request->only([
                    'cart_id',
                    'product_id',
                    'attribute_id',
                    'quantity',
                    'created_by',
                    'updated_by',
                ]));
            break;
        }

        return response()->json(self::cartGet($cart->id, $request)->getData(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/assigncart",
     *     operationId="cartAssign",
     *     tags={"Cart"},
     *     summary="Assigns a cart to a user",
     *     description="Assigns the guest cart to a newly created or existing consumer account.",
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
     *         name="cart_id",
     *         in="query",
     *         description="The cart id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns all cart item from the assigned cart",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the cart item assign failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function cartAssign(Request $request = null)
    {
        if (!isset($request->access_token_user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Token required',
            ], 400);
        } else if (!isset($request->cart_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart id required',
            ], 400);
        } else if (empty(Cart::where('id', $request->cart_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid cart id',
            ], 400);
        } else if (empty(Cart::where('id', $request->cart_id)->whereNull('user_id')->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Cart belongs to other consumer',
            ], 400);
        }

        $cartExisting = Cart::where('user_id', $request->access_token_user_id)->whereNull('deleted_at')->first();
        $cartNew = Cart::where('id', $request->cart_id)->whereNull('user_id')->whereNull('deleted_at')->first();

        if (empty($cartExisting)) {
            $request->request->add([
                'user_id' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            $cartNew->update($request->only([
                'user_id',
                'updated_by',
            ]));

            $cartActual = $cartNew;
        } else {
            $request->request->add([
                'user_id' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
                'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'deleted_by' => $request->access_token_user_id,
            ]);

            $cartNew->update($request->only([
                'user_id',
                'updated_by',
                'deleted_at',
                'deleted_by',
            ]));

            $cartActual = $cartExisting;
        }

        $cartItemList = CartItem::where('cart_id', $cartNew->id)->whereNull('deleted_at')->get();
        foreach ($cartItemList as $cartItem) {
            $cartItem->update($request->only([
                'updated_by',
            ]));

            if (!empty($cartExisting)) {
                $request->request->add([
                    'cart_id' => $cartExisting->id,
                ]);

                $cartItem->update($request->only([
                    'cart_id',
                ]));
            }
        }

        return response()->json(self::cartGet($cartActual->id, $request)->getData(), 201);
    }
}

