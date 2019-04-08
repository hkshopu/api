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
use App\Entity;
use App\User;
use App\UserType;
use App\Image;
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
     *     description="Retrieves all order of the consumer.",
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         description="The access token for authentication",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
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
    public function orderList(Request $request = null) {
        $user = User::where('id', $request->access_token_user_id)->whereNull('deleted_at')->first();
        $userType = UserType::where('id', $user->user_type_id)->whereNull('deleted_at')->first();

        $isConsumer = false;
        switch ($userType->name) {
            case 'system administrator':
            case 'system operator':
                $orderList = Order::whereNull('deleted_at')->orderBy('created_at', 'ASC')->get();
            break;
            case 'retailer':
                $shop = Shop::where('user_id', $user->id)->whereNull('deleted_at')->first();
                $orderList = Order::where('shop_id', $shop->id)->whereNull('deleted_at')->orderBy('created_at', 'ASC')->get();
            break;
            case 'consumer':
                $isConsumer = true;
                $orderList = Order::where('created_by', $request->access_token_user_id)->whereNull('deleted_at')->orderBy('created_at', 'DESC')->get();
            break;
            default:
                $orderList = Order::where('id', 0)->whereNull('deleted_at')->get();
            break;
        }

        foreach ($orderList as $orderKey => $order) {
            $orderList[$orderKey] = self::orderGet($order->id, $request)->getData();
        }

        if ($isConsumer == true) {
            $orderListSimplified = [];
            foreach ($orderList as $order) {
                foreach ($order->shop_order->product as $orderItem) {
                    $orderSimplifiedItem['product_id'] = $orderItem->product_id;
                    $orderSimplifiedItem['product_image'] = $orderItem->image_url;
                    $orderSimplifiedItem['product_name_en'] = $orderItem->name_en;
                    $orderSimplifiedItem['product_name_tc'] = $orderItem->name_tc;
                    $orderSimplifiedItem['product_name_sc'] = $orderItem->name_sc;
                    $orderSimplifiedItem['shop_name_en'] = $order->shop->name_en;
                    $orderSimplifiedItem['shop_name_tc'] = $order->shop->name_tc;
                    $orderSimplifiedItem['shop_name_sc'] = $order->shop->name_sc;
                    $orderSimplifiedItem['cart_item_id'] = $orderItem->cart_item_id;
                    $orderSimplifiedItem['order_date'] = $order->order_date;
                    $orderSimplifiedItem['total_price'] = $orderItem->total_price;
                    $orderSimplifiedItem['total_price_discounted'] = $orderItem->total_price_discounted;
                    $orderSimplifiedItem['payment_status'] = $order->payment_status;
                    $orderSimplifiedItem['order_item_status'] = $orderItem->order_item_status;
                    $orderItem = $orderSimplifiedItem;
                    $orderListSimplified[$order->id][] = $orderItem;
                }
            }

            $orderListGrouped = [];
            $orderGroupCounter = 0;
            foreach ($orderListSimplified as $orderId => $orderProductArray) {
                $orderListGrouped[$orderGroupCounter]['order_id'] = $orderId;
                $orderListGrouped[$orderGroupCounter]['product_list'] = $orderProductArray;
                $orderGroupCounter++;
            }

            $orderList = $orderListGrouped;
        }

        return response()->json($orderList, 200);
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

        $shop = Shop::where('id', $request->shop_id)->whereNull('deleted_at')->first();
        if (empty($shop)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $user = User::where('id', $shop->user_id)->whereNull('deleted_at')->first();
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Shop inactive',
            ], 400);
        }

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

        $paymentMethod = PaymentMethod::where('id', $request->payment_method_id)->whereNull('deleted_at')->first();
        if (empty($paymentMethod)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment method id',
            ], 400);
        }

        $shopPaymentMethodMap = ShopPaymentMethodMap::where('shop_id', $request->shop_id)->where('payment_method_id', $request->payment_method_id)->whereNull('deleted_at')->first();
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
                                            ->whereNull('deleted_at');
            $cartItemToUpdateObjectArray = $cartItemToUpdateCollection->get();

            foreach ($cartItemToUpdateObjectArray as $cartItemToUpdateObject) {
                $cartItemToUpdateObject->update($request->only([
                    'order_id',
                    'updated_by',
                ]));
            }

            $cartItemWithStatus = $cartItemToUpdateCollection->first();
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

        return response()->json(self::orderList($request)->getData(), 201);
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
            $shipmentFeeOverride = $order->shipment_fee_override;
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
                $shop = Shop::where('id', $order->shop_id)->whereNull('deleted_at')->first();
                if (!empty($shop)) {
                    $shopEntity = Entity::where('name', $shop->getTable())->first();
                    $image = Image::where('entity', $shopEntity->id)->where('entity_id', $shop->id)->whereNull('deleted_at')->where('sort', '<>', 0)->orderBy('sort', 'ASC')->first();
                    $shop['image_url'] = !empty($image) ? $image->url : null;
                    $order['shop'] = $shop;
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
            $shopOrderList = app('App\Http\Controllers\CartController')->cartItemList($cartItemList, $request)->getData();
            $order['shop_order'] = current($shopOrderList);

            $order->shop_cart_gross = $order->shop_order->total_amount_discounted;
            $order->shipping_fee_original = $order->shop_order->shipment_fee_computed;
            $order->shop_order_total = $order->shop_cart_gross + ($shipmentFeeOverride ?? $order->shop_order->shipment_fee_computed);
        }

        return response()->json($order, 200);
    }
}

