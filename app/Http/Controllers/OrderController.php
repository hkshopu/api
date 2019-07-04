<?php

namespace App\Http\Controllers;

use App\Shop;
use App\PaymentMethod;
use App\ShopPaymentMethodMap;
use App\Cart;
use App\CartItem;
use App\Order;
use App\StatusMap;
use App\Status;
use App\StatusOption;
use App\Entity;
use App\User;
use App\UserType;
use App\Image;
use App\Language;
use App\View;
use App\Attribute;
use App\ProductAttribute;
use App\ProductInventory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
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
     *     path="/api/order",
     *     operationId="orderList",
     *     tags={"Order"},
     *     summary="Retrieves all order",
     *     description="Retrieves all order of the consumer, filterable by shop id.",
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
     *     @OA\Response(
     *         response="200",
     *         description="Returns all order",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the order get failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function orderList(Request $request = null, int $user_id = null, int $product_id = null) {
        $simplifyResult = true;

        $user = User::where('id', $request->access_token_user_id)->whereNull('deleted_at')->first();
        if (!empty($user)) {
            $userType = UserType::where('id', $user->user_type_id)->whereNull('deleted_at')->first();
        } else {
            // Getting GUEST user type
            $userType = UserType::where('name', 'guest')->whereNull('deleted_at')->first();
        }

        if (isset($user_id)) {
            // Call is coming from /api/user
            $call = 'user';
        } else if (isset($product_id)) {
            // Call is coming from /api/product
            $call = 'product';
            $user = User::where('id', (isset($user_id) ? $user_id : $request->access_token_user_id))->whereNull('deleted_at')->first();
        } else {
            // Call is coming from /api/order
            $call = 'order';
            $user = User::where('id', (isset($user_id) ? $user_id : $request->access_token_user_id))->whereNull('deleted_at')->first();
        }

        /*
            =======================================
            USE CASES

            /api/order LIST

            Sys Admin   ALL orders                      COVERED V
            Retailer    ALL of retailer orders          COVERED IV
            Consumer    ALL of consumer/user orders     COVERED II
            Guest       Forbidden                       COVERED I

            /api/user GET

            Sys Admin   ALL of consumer/user orders     COVERED II
            Retailer    ALL of consumer/user orders     COVERED II
            Consumer    Empty                           COVERED I
            Guest       Empty                           COVERED I

            /api/product GET

            Sys Admin   ALL of product orders           COVERED III
            Retailer    ALL of product orders           COVERED III
            Consumer    Empty                           COVERED I
            Guest       Empty                           COVERED I
        */

        $orderFilter = \DB::table('order')
            ->leftJoin('shop', 'shop.id', '=', 'order.shop_id')
            ->leftJoin('cart', 'cart.id', '=', 'order.cart_id')
            ->leftJoin('cart_item', 'cart_item.order_id', '=', 'order.id')
            ->select('order.*')
            ->whereNull('order.deleted_at')
            ->whereNull('shop.deleted_at')
            ->whereNull('cart.deleted_at')
            ->whereNull('cart_item.deleted_at')
            ->orderBy('order.id', 'ASC')
            ->groupBy('order.id')
        ;

        if ($userType->name == 'guest' || ($userType->name == 'consumer' && $call <> 'order')) {
            // Empty
            $orderFilter->where('order.id', 0);
        } else if ($userType->name == 'consumer' || $call == 'user') {
            // ALL of consumer/user orders
            $orderFilter->where('cart.user_id', $user_id ?? $request->access_token_user_id);
        } else if ($call == 'product') {
            // ALL of product orders
            $orderFilter->where('cart_item.product_id', $product_id);
        } else if ($userType->name == 'retailer') {
            // ALL of retailer orders
            $orderFilter->where('shop.user_id', $request->access_token_user_id);
            $simplifyResult = false;
        } else {
            // ALL orders
            $simplifyResult = false;
        }

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

            $orderFilter->where('order.shop_id', $request->shop_id);
        }

        $orderList = [];
        $orderListRaw = $orderFilter->get();
        foreach ($orderListRaw as $orderItem) {
            $orderInfo = self::orderGet($orderItem->id, $request)->getData();
            if (!empty($orderInfo->shop_order)) {
                $orderList[] = $orderInfo;
            }
        }

        if ($simplifyResult == true) {
            $orderListSimplified = [];
            foreach ($orderList as $order) {
                $orderListSimplified[$order->id]['order_id'] = $order->id;
                $orderListSimplified[$order->id]['order_total_quantity'] = $order->shop_order->total_quantity;
                $orderListSimplified[$order->id]['order_total_price'] = $order->shop_order_total;
                $orderListSimplified[$order->id]['order_list'] = [];
                if (!empty($order->shop_order->product)) {
                    foreach ($order->shop_order->product as $orderItem) {
                        $orderSimplifiedItem['product_id'] = $orderItem->product_id;
                        $orderSimplifiedItem['product_image'] = $orderItem->image_url;
                        $orderSimplifiedItem['product_name'] = $orderItem->name;
                        $orderSimplifiedItem['product_name_en'] = $orderItem->name_en;
                        $orderSimplifiedItem['product_name_tc'] = $orderItem->name_tc;
                        $orderSimplifiedItem['product_name_sc'] = $orderItem->name_sc;
                        $orderSimplifiedItem['product_description'] = $orderItem->description;
                        $orderSimplifiedItem['product_description_en'] = $orderItem->description_en;
                        $orderSimplifiedItem['product_description_tc'] = $orderItem->description_tc;
                        $orderSimplifiedItem['product_description_sc'] = $orderItem->description_sc;
                        $orderSimplifiedItem['shop_name'] = $order->shop->name;
                        $orderSimplifiedItem['shop_name_en'] = $order->shop->name_en;
                        $orderSimplifiedItem['shop_name_tc'] = $order->shop->name_tc;
                        $orderSimplifiedItem['shop_name_sc'] = $order->shop->name_sc;
                        $orderSimplifiedItem['cart_item_id'] = $orderItem->cart_item_id;
                        $orderSimplifiedItem['order_date'] = $order->order_date;
                        $orderSimplifiedItem['quantity'] = $orderItem->quantity;
                        $orderSimplifiedItem['price'] = $orderItem->price;
                        $orderSimplifiedItem['total_price'] = $orderItem->total_price;
                        $orderSimplifiedItem['total_price_discounted'] = $orderItem->total_price_discounted;
                        $orderSimplifiedItem['payment_status'] = $order->payment_status;
                        $orderSimplifiedItem['order_item_status'] = $orderItem->order_item_status;
                        $orderItem = $orderSimplifiedItem;
                        $orderListSimplified[$order->id]['order_list'][] = $orderItem;
                    }
                }
            }

            $orderListGrouped = [];
            $orderGroupCounter = 0;
            foreach ($orderListSimplified as $orderSummary) {
                $orderListGrouped[$orderGroupCounter]['order_id'] = $orderSummary['order_id'];
                $orderListGrouped[$orderGroupCounter]['order_total_quantity'] = $orderSummary['order_total_quantity'];
                $orderListGrouped[$orderGroupCounter]['order_total_price'] = $orderSummary['order_total_price'];
                $orderListGrouped[$orderGroupCounter]['product_list'] = $orderSummary['order_list'];
                $orderGroupCounter++;
            }

            $orderList = $orderListGrouped;
        }

        return response()->json($orderList, 200);
    }

    public function orderListLatest(Request $request) {
        return response()->json(current(self::orderList($request)->getData()), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/order",
     *     operationId="orderAdd",
     *     tags={"Order"},
     *     summary="Adds order after checkout",
     *     description="
Saves the order whether it's successful payment or not (order status = 'Process'). Can be updated if payment is successful via another endpoint.
<br /><br />
As for payment: If successful, payment status = 'Paid'. If not, payment status = 'Wait For Payment'
<br /><br />
<strong>Important Note: </strong>This is under the assumption of <span style='font-weight:bold;color:red'>ONLY</span> logged users are allowed to order items. Guest cart must signup before proceeding to order.
           ",
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         description="The access token for authentication. This is required to identify the cart of the logged user.",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="shop_id",
     *         in="query",
     *         description="The shop id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="payment_method_id",
     *         in="query",
     *         description="The payment method id (Can be found in shop object in cart endpoint)",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="receiver",
     *         in="query",
     *         description="The order receiver",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         description="The receiver address",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_successful",
     *         in="query",
     *         description="If payment is successful (true/false), default is false",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the order added",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the order add failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function orderAdd(Request $request)
    {
        $cart = Cart::where('user_id', $request->access_token_user_id)->whereNull('deleted_at')->first();
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'No cart for user yet',
            ], 400);
        }

        $request->request->add([
            'cart_id' => $cart->id,
        ]);

        $cartItem = CartItem::where('cart_id', $cart->id)->whereNull('deleted_at')->first();
        if (empty($cartItem)) {
            return response()->json([
                'success' => false,
                'message' => 'Empty cart',
            ], 400);
        }

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

        $paymentMethod = PaymentMethod::where('id', $request->payment_method_id)->whereNull('deleted_at')->first();
        if (empty($paymentMethod)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment method id',
            ], 400);
        }

        $shopPaymentMethodMap = ShopPaymentMethodMap::where('shop_id', $shop->id)->where('payment_method_id', $request->payment_method_id)->whereNull('deleted_at')->first();
        if (empty($shopPaymentMethodMap)) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not available for that shop',
            ], 400);
        }

        $request->request->add([
            'shop_payment_method_id' => $shopPaymentMethodMap->id,
            'shipment_receiver' => $request->receiver ?? null,
            'shipment_address' => $request->address ?? null,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $cartItemArray = app('App\Http\Controllers\CartController')->cartGet($cart->id, $request)->getData();
        $shopArray = $cartItemArray->shop;

        $isShopFound = false;
        $shopOrder = [];
        foreach ($shopArray as $shopItem) {
            if ($shop->id == $shopItem->shop_id) {
                $shopOrder = $shopItem;
                $isShopFound = true;
                break;
            }
        }

        if (!$isShopFound) {
            return response()->json([
                'success' => false,
                'message' => 'No items checked out from that shop',
            ], 400);
        }

        // Re-check available stock to avoid negative transaction
        foreach ($shopOrder->product as $productItem) {
            $productStock = ProductInventory::checkStock($productItem->product_id, $productItem->attribute_id);
            if ($productItem->quantity > $productStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order cannot proceed. Stock ran out or insufficient for product id: ' . $productItem->product_id,
                ], 400);
            }
        }

        if (isset($request->is_successful)) {
            $paymentStatus = $request->is_successful ? 'paid' : 'wait for payment';
        } else {
            $paymentStatus = 'wait for payment';
        }

        $order = Order::create($request->only([
            'cart_id',
            'shop_id',
            'shop_payment_method_id',
            'shipment_receiver',
            'shipment_address',
            'created_by',
            'updated_by',
        ]));

        // Mark cart items as sold, belonging to an order
        $cartItemToUpdateArray = [];
        foreach ($shopOrder->product as $productItem) {
            $cartItemToUpdateArray[] = [
                'cart_id' => $cart->id,
                'cart_item_id' => $productItem->cart_item_id,
                'product_id' => $productItem->product_id,
                'attribute_id' => $productItem->attribute_id,
            ];
        }

        $request->request->add([
            'order_id' => $order->id,
            'updated_by' => $request->access_token_user_id,
        ]);

        // Setting PROCESS status for order
        $statusOrder = Status::where('name', 'process')->whereNull('deleted_at')->first();
        $orderEntity = Entity::where('name', $order->getTable())->first();

        $request->request->add([
            'entity' => $orderEntity->id,
            'entity_id' => $order->id,
            'status_id' => $statusOrder->id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $statusMap = StatusMap::create($request->only([
            'entity',
            'entity_id',
            'status_id',
            'created_by',
            'updated_by',
        ]));

        // Setting WAIT FOR PAYMENT / PAID status for payment
        $statusPayment = Status::where('name', $paymentStatus)->whereNull('deleted_at')->first();
        $paymentEntity = Entity::where('name', 'payment')->first();

        $request->request->add([
            'entity' => $paymentEntity->id,
            'entity_id' => $order->id,
            'status_id' => $statusPayment->id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $statusMap = StatusMap::create($request->only([
            'entity',
            'entity_id',
            'status_id',
            'created_by',
            'updated_by',
        ]));

        // Mass update cart items and statuses
        $orderItemEntity = Entity::where('name', 'order_item')->whereNull('deleted_at')->first();
        foreach ($cartItemToUpdateArray as $cartItemToUpdateItem) {
            $cartItemToUpdateCollection = CartItem::where('cart_id', $cart->id)
                                            ->where('product_id', $cartItemToUpdateItem['product_id'])
                                            ->where('attribute_id', $cartItemToUpdateItem['attribute_id'])
                                            ->whereNull('order_id')
                                            ->whereNull('deleted_at')
                                            ;
            $cartItemToUpdateObjectArray = $cartItemToUpdateCollection->get();
            $cartItemWithStatus = $cartItemToUpdateCollection->first();

            $quantityTotal = 0;
            foreach ($cartItemToUpdateObjectArray as $cartItemToUpdateObjectItem) {
                $cartItemToUpdateObjectItem->update($request->only([
                    'order_id',
                    'updated_by',
                ]));

                $quantityTotal += $cartItemToUpdateObjectItem->quantity;

                if ($quantityTotal < 0) {
                    $quantityTotal = 0;
                }
            }

            if ($paymentStatus == 'paid') {
                $productAttribute = ProductAttribute::where('product_id', $cartItemToUpdateItem['product_id'])
                    ->where('attribute_id', $cartItemToUpdateItem['attribute_id'])
                    ->whereNull('deleted_at')
                    ->first();

                $request->request->add([
                    'product_attribute_id' => $productAttribute->id,
                    'stock' => -1 * abs($quantityTotal),
                    'order_id' => $order->id,
                    'created_by' => $request->access_token_user_id,
                    'updated_by' => $request->access_token_user_id,
                ]);

                ProductInventory::create($request->only([
                    'product_attribute_id',
                    'stock',
                    'order_id',
                    'created_by',
                    'updated_by',
                ]));
            }

            $request->request->add([
                'entity' => $orderItemEntity->id,
                'entity_id' => $cartItemWithStatus->id,
                'status_id' => $statusOrder->id,
                'created_by' => $request->access_token_user_id,
                'updated_by' => $request->access_token_user_id,
            ]);

            $statusMap = StatusMap::create($request->only([
                'entity',
                'entity_id',
                'status_id',
                'created_by',
                'updated_by',
            ]));
        }

        return response()->json(self::orderListLatest($request)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/order/{id}",
     *     operationId="orderGet",
     *     tags={"Order"},
     *     summary="Retrieves the order given the id",
     *     description="Retrieves the order given the id.",
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
     *         description="The order id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the order given the id",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function orderGet(int $id, Request $request = null)
    {
        $order = Order::where('id', $id)->whereNull('deleted_at')->first();

        if (!empty($order)) {
            $shipmentFeeOverride = (float) $order->shipment_fee_override;
            $createdAt = $order->created_at;
            $createdBy = $order->created_by;
            unset($order->shipment_fee_override);
            unset($order->created_at);
            unset($order->created_by);
            $order['shop_cart_gross'] = 0.00;
            $order['shipping_fee_original'] = 0.00;
            $order['shipment_fee_override'] = $shipmentFeeOverride;
            $order['shop_order_total'] = 0.00;
            $order['created_at'] = $createdAt;
            $order['created_by'] = $createdBy;

            $request->request->add([
                'filter_inactive' => false,
            ]);

            $order['user'] = null;
            if (!empty($order->created_by)) {
                $user = User::where('id', $order->created_by)->whereNull('deleted_at')->first();
                if (!empty($user)) {
                    $userEntity = Entity::where('name', $user->getTable())->first();
                    $image = Image::where('entity', $userEntity->id)->where('entity_id', $user->id)->whereNull('deleted_at')->where('sort', '<>', 0)->orderBy('sort', 'ASC')->first();
                    $user['image_url'] = !empty($image) ? $image->url : null;
                    $order['user'] = $user;
                }
            }

            $order['shop'] = null;
            if (!empty($order->shop_id)) {
                $shopQuery = \DB::table('shop')
                    ->leftJoin('user', 'user.id', '=', 'shop.user_id')
                    ->select('shop.*')
                    ->where('shop.id', $order->shop_id)
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

                if (!empty($shop)) {
                    $shop = Shop::where('id', $shop->id)->whereNull('deleted_at')->first();

                    // LANGUAGE Translation
                    $shop->name = Language::translate($request, $shop, 'name');
                    $shop->description = Language::translate($request, $shop, 'description');

                    $shopEntity = Entity::where('name', $shop->getTable())->first();
                    $image = Image::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->where('sort', '<>', 0)->orderBy('sort', 'ASC')->first();
                    $shop['image_url'] = !empty($image) ? $image->url : null;
                    $order['shop'] = $shop;
                }
            }

            $order['is_new'] = true;
            $orderViewList = app('App\Http\Controllers\ViewController')->orderViewGet($order->id)->getData();
            foreach ($orderViewList as $orderViewItem) {
                if ($request->access_token_user_id == $orderViewItem->created_by) {
                    $order['is_new'] = false;
                    break;
                }
            }

            $orderEntity = Entity::where('name', $order->getTable())->first();
            $paymentEntity = Entity::where('name', 'payment')->first();
            $orderStatusMap = StatusMap::where('entity', $orderEntity->id)->where('entity_id', $order->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($orderStatusMap)) {
                $orderStatus = Status::where('id', $orderStatusMap->status_id)->whereNull('deleted_at')->first();
                $order['order_status'] = (!empty($orderStatus)) ? $orderStatus->name : null;
            } else {
                $order['order_status'] = null;
            }

            $paymentStatusMap = StatusMap::where('entity', $paymentEntity->id)->where('entity_id', $order->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($paymentStatusMap)) {
                $paymentStatus = Status::where('id', $paymentStatusMap->status_id)->whereNull('deleted_at')->first();
                $order['payment_status'] = (!empty($paymentStatus)) ? $paymentStatus->name : null;
            } else {
                $order['payment_status'] = null;
            }

            $order['order_date'] = $order->created_at->format('Y-m-d H:i:s');

            $cartItemList = CartItem::where('order_id', $order->id)->whereNull('deleted_at')->get();
            $shopOrderList = app('App\Http\Controllers\CartController')->cartItemList($cartItemList, $request, true)->getData();
            $order['shop_order'] = current($shopOrderList);

            if (!empty($order->shop_order)) {
                $order->shop_cart_gross = $order->shop_order->total_amount_discounted;
                $order->shipping_fee_original = $order->shop_order->shipment_fee_computed;
                $order->shop_order_total = $order->shop_cart_gross + ($shipmentFeeOverride ?? $order->shop_order->shipment_fee_computed);
            }
        }

        return response()->json($order, 200);
    }

    public function orderGetTwoPointOw(int $id, Request $request = null)
    {
        $orderQuery = \DB::table('order')
            ->leftJoin('cart_item', 'cart_item.order_id', '=', 'order.id')
            ->leftJoin('cart', 'cart.id', '=', 'cart_item.cart_id')
            ->leftJoin('user', 'user.id', '=', 'cart.user_id')
            ->leftJoin('shop', 'shop.id', '=', 'order.shop_id')
            ->select([
                'order.id',
                'order.cart_id',
                'order.shop_id',
                'order.shop_payment_method_id',
                'order.shipment_receiver',
                'order.shipment_address',
            ])
            ->where('order.id', $id)
            ->whereNull('order.deleted_at')
            ->whereNull('cart_item.deleted_at')
            ->whereNull('cart.deleted_at')
        ;

        $orderQuery->addSelect([
            \DB::raw("0.00 AS shop_cart_gross"),
            \DB::raw("0.00 AS shipping_fee_original"),
        ]);

        $orderQuery->addSelect([
            'order.shipment_fee_override',
        ]);

        $orderQuery->addSelect([
            \DB::raw("0.00 AS shop_order_total"),
        ]);

        $orderQuery->addSelect([
            'order.created_at',
            'order.created_by',
        ]);

        $orderQuery->addSelect([
            'user.id AS user_id',
            'user.username AS user_username',
            'user.email AS user_email',
            'user.first_name AS user_first_name',
            'user.middle_name AS user_middle_name',
            'user.last_name AS user_last_name',
            'user.gender AS user_gender',
            'user.birth_date AS user_birth_date',
            'user.mobile_phone AS user_mobile_phone',
            'user.address AS user_address',
            'user.user_type_id AS user_user_type_id',
            'user.activation_key AS user_activation_key',
            'user.language_id AS user_language_id',
            'user.created_at AS user_created_at',
        ]);

        $orderQuery->addSelect([
            \DB::raw("NULL AS user_image_url"),
        ]);

        $orderQuery->addSelect([
            'shop.id AS shop_id',
            'shop.name AS shop_name',
            'shop.name_en AS shop_name_en',
            'shop.name_tc AS shop_name_tc',
            'shop.name_sc AS shop_name_sc',
            'shop.description AS shop_description',
            'shop.description_en AS shop_description_en',
            'shop.description_tc AS shop_description_tc',
            'shop.description_sc AS shop_description_sc',
            'shop.logo_url AS shop_logo_url',
            'shop.user_id AS shop_user_id',
            'shop.created_at AS shop_created_at',
        ]);

        $orderQuery->addSelect([
            \DB::raw("NULL AS shop_image_url"),
        ]);

        $orderQuery->addSelect([
            \DB::raw("NULL AS is_new"),
            \DB::raw("NULL AS order_status"),
            \DB::raw("NULL AS payment_status"),
        ]);

        $orderQuery->addSelect([
            'order.created_at AS order_date',
        ]);

        $orderInfo = $orderQuery->first();
        $order = [];

        if (!empty($orderInfo)) {
            $request->request->add([
                'filter_inactive' => false,
            ]);

            $order['id'] = $orderInfo->id;
            $order['cart_id'] = $orderInfo->cart_id;
            $order['shop_id'] = $orderInfo->shop_id;
            $order['shop_payment_method_id'] = $orderInfo->shop_payment_method_id;
            $order['shipment_receiver'] = $orderInfo->shipment_receiver;
            $order['shipment_address'] = $orderInfo->shipment_address;
            $order['shop_cart_gross'] = (float) $orderInfo->shop_cart_gross;
            $order['shipping_fee_original'] = (float) $orderInfo->shipping_fee_original;
            $order['shipment_fee_override'] = (float) $orderInfo->shipment_fee_override;
            $order['shop_order_total'] = (float) $orderInfo->shop_order_total;
            $order['created_at'] = $orderInfo->created_at;
            $order['created_by'] = $orderInfo->created_by;

            $order['user'] = [];
            $order['user']['id'] = $orderInfo->user_id;
            $order['user']['username'] = $orderInfo->user_username;
            $order['user']['email'] = $orderInfo->user_email;
            $order['user']['first_name'] = $orderInfo->user_first_name;
            $order['user']['middle_name'] = $orderInfo->user_middle_name;
            $order['user']['last_name'] = $orderInfo->user_last_name;
            $order['user']['gender'] = $orderInfo->user_gender;
            $order['user']['birth_date'] = $orderInfo->user_birth_date;
            $order['user']['mobile_phone'] = $orderInfo->user_mobile_phone;
            $order['user']['address'] = $orderInfo->user_address;
            $order['user']['user_type_id'] = $orderInfo->user_user_type_id;
            $order['user']['activation_key'] = $orderInfo->user_activation_key;
            $order['user']['language_id'] = $orderInfo->user_language_id;
            $order['user']['created_at'] = $orderInfo->user_created_at;

            $user = new User();
            $userEntity = Entity::where('name', $user->getTable())->first();
            $image = Image::where('entity', $userEntity->id)->where('entity_id', $orderInfo->user_id)->whereNull('deleted_at')->where('sort', '<>', 0)->orderBy('sort', 'ASC')->first();
            $order['user']['image_url'] = !empty($image) ? $image->url : null;

            $order['shop'] = [];

            if (!empty($orderInfo->shop_id)) {
                $shopQuery = \DB::table('shop')
                    ->leftJoin('user', 'user.id', '=', 'shop.user_id')
                    ->select('shop.*')
                    ->where('shop.id', $orderInfo->shop_id)
                    ->whereNull('shop.deleted_at');

                if ($request->filter_inactive == true) {
                    $shopQuery
                        ->leftJoin('shop_payment_method_map', 'shop_payment_method_map.shop_id', '=', 'shop.id')
                        ->whereNotNull('shop_payment_method_map.id')
                        ->groupBy('shop.id')
                        ->whereNull('user.deleted_at');
                }

                $shop = $shopQuery->first();

                if (!empty($shop)) {
                    $shop = Shop::where('id', $shop->id)->whereNull('deleted_at')->first();

                    // LANGUAGE Translation
                    $shop->name = Language::translate($request, $shop, 'name');
                    $shop->description = Language::translate($request, $shop, 'description');

                    $shopEntity = Entity::where('name', $shop->getTable())->first();
                    $image = Image::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->where('sort', '<>', 0)->orderBy('sort', 'ASC')->first();
                    $shop['image_url'] = !empty($image) ? $image->url : null;
                    $order['shop'] = $shop->toArray();
                }
            }

            $order['is_new'] = true;
            $orderViewList = app('App\Http\Controllers\ViewController')->orderViewGet($orderInfo->id)->getData();
            foreach ($orderViewList as $orderViewItem) {
                if ($request->access_token_user_id == $orderViewItem->created_by) {
                    $order['is_new'] = false;
                    break;
                }
            }

            $orderObject = new Order();
            $orderEntity = Entity::where('name', $orderObject->getTable())->first();
            $paymentEntity = Entity::where('name', 'payment')->first();
            $orderInfoStatusMap = StatusMap::where('entity', $orderEntity->id)->where('entity_id', $orderInfo->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($orderInfoStatusMap)) {
                $orderInfoStatus = Status::where('id', $orderInfoStatusMap->status_id)->whereNull('deleted_at')->first();
                $order['order_status'] = (!empty($orderInfoStatus)) ? $orderInfoStatus->name : null;
            } else {
                $order['order_status'] = null;
            }

            $paymentStatusMap = StatusMap::where('entity', $paymentEntity->id)->where('entity_id', $orderInfo->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($paymentStatusMap)) {
                $paymentStatus = Status::where('id', $paymentStatusMap->status_id)->whereNull('deleted_at')->first();
                $order['payment_status'] = (!empty($paymentStatus)) ? $paymentStatus->name : null;
            } else {
                $order['payment_status'] = null;
            }

            $order['order_date'] = $orderInfo->order_date;

            $cartItemList = CartItem::where('order_id', $orderInfo->id)->whereNull('deleted_at')->get();
            $shopOrderList = app('App\Http\Controllers\CartController')->cartItemList($cartItemList, $request, true)->getData();
            $order['shop_order'] = current($shopOrderList);

            if (!empty($order->shop_order)) {
                $order->shop_cart_gross = $order->shop_order->total_amount_discounted;
                $order->shipping_fee_original = $order->shop_order->shipment_fee_computed;
                $order->shop_order_total = $order->shop_cart_gross + ($shipmentFeeOverride ?? $order->shop_order->shipment_fee_computed);
            }
        }

        return response()->json($order, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/order/{id}",
     *     operationId="orderDelete",
     *     tags={"Order"},
     *     summary="Deletes order from the web app",
     *     description="Deletes order from the web app.",
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
     *         description="The order id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the order delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the order delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function orderDelete(int $id, Request $request)
    {
        $order = Order::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($order)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order id',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $order->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        $orderEntity = Entity::where('name', $order->getTable())->first();

        $orderStatusMapList = StatusMap::where('entity', $orderEntity->id)->where('entity_id', $order->id)->whereNull('deleted_at')->get();
        foreach ($orderStatusMapList as $statusMap) {
            $statusMap->update($request->all());
        }

        $paymentEntity = Entity::where('name', 'payment')->first();

        $paymentStatusMapList = StatusMap::where('entity', $paymentEntity->id)->where('entity_id', $order->id)->whereNull('deleted_at')->get();
        foreach ($paymentStatusMapList as $statusMap) {
            $statusMap->update($request->all());
        }

        $orderItemEntity = Entity::where('name', 'order_item')->first();

        $orderItemList = CartItem::where('order_id', $order->id)->whereNull('deleted_at')->get();
        foreach ($orderItemList as $orderItemItem) {
            $orderItemStatusMapList = StatusMap::where('entity', $orderItemEntity->id)->where('entity_id', $orderItemItem->id)->whereNull('deleted_at')->get();
            foreach ($orderItemStatusMapList as $statusMap) {
                $statusMap->update($request->all());
            }

            $orderItemItem->update($request->only([
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
     *     path="/api/order/{id}",
     *     operationId="orderModify",
     *     tags={"Order"},
     *     summary="Modifies the order given the id with only defined fields",
     *     description="
Modifies the order given the id with only defined fields.
<br /><br />
<span style='font-weight:bold;color:red'>NOTE:</span> This should also be used for holding off orders by simply sending the corresponding order status id for 'On Hold'.
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
     *         name="id",
     *         in="path",
     *         description="The order id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="order_status_id",
     *         in="query",
     *         description="The order status id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="payment_status_id",
     *         in="query",
     *         description="The payment status id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="shipment_address",
     *         in="query",
     *         description="The shipment address",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="shipment_receiver",
     *         in="query",
     *         description="The shipment receiver",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="shipment_fee",
     *         in="query",
     *         description="The shipment fee",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the order given the id",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function orderModify(int $id, Request $request = null)
    {
        $order = Order::where('id', $id)->whereNull('deleted_at')->first();

        if (empty($order)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid order id',
            ], 400);
        }

        if (isset($request->order_status_id)) {
            $orderEntity = Entity::where('name', $order->getTable())->first();

            $status = Status::where('id', $request->order_status_id)->whereNull('deleted_at')->first();
            $statusOption = StatusOption::where('entity', $orderEntity->id)->where('status_id', $request->order_status_id)->whereNull('deleted_at')->first();
            if (empty($status)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status id',
                ], 400);
            } else if (empty($statusOption)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status for the order',
                ], 400);
            }
        }

        if (isset($request->payment_status_id)) {
            $paymentEntity = Entity::where('name', 'payment')->first();

            $status = Status::where('id', $request->payment_status_id)->whereNull('deleted_at')->first();
            $statusOption = StatusOption::where('entity', $paymentEntity->id)->where('status_id', $request->payment_status_id)->whereNull('deleted_at')->first();
            if (empty($status)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status id',
                ], 400);
            } else if (empty($statusOption)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status for the payment',
                ], 400);
            }
        }

        if (isset($request->shipment_receiver)) {
            $request->request->add(['shipment_receiver' => $request->shipment_receiver]);
        }

        if (isset($request->shipment_address)) {
            $request->request->add(['shipment_address' => $request->shipment_address]);
        }

        if (isset($request->shipment_fee)) {
            if ($request->shipment_fee < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid shipment fee',
                ], 400);
            }

            $request->request->add(['shipment_fee_override' => $request->shipment_fee]);
        }

        $request->request->add([
            'updated_by' => $request->access_token_user_id,
        ]);

        $order->update($request->all());

        $request->request->add([
            'created_by' => $request->access_token_user_id,
        ]);

        if (isset($request->order_status_id)) {
            $request->request->add([
                'entity' => $orderEntity->id,
                'entity_id' => $order->id,
                'status_id' => $request->order_status_id,
            ]);

            $statusMap = StatusMap::create($request->only([
                'entity',
                'entity_id',
                'status_id',
                'created_by',
                'updated_by',
            ]));
        }

        if (isset($request->payment_status_id)) {
            $request->request->add([
                'entity' => $paymentEntity->id,
                'entity_id' => $order->id,
                'status_id' => $request->payment_status_id,
            ]);

            $statusMap = StatusMap::create($request->only([
                'entity',
                'entity_id',
                'status_id',
                'created_by',
                'updated_by',
            ]));
        }

        return response()->json(self::orderGet($order->id, $request)->getData(), 200);
    }
}

