<?php

namespace App\Http\Controllers;

use App\Cart;
use App\CartItem;
use App\Shop;
use App\Product;
use App\ProductInventory;
use Illuminate\Http\Request;
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
        // For Sprint 1.1 demo only
        $cart = Cart::whereNull('user_id')->whereNull('deleted_at')->first();
        if (empty($cart)) {
            $cart = Cart::create();
        }
        $cart_id = $cart->id;

        if (!isset($request->access_token_user_id)) {
            $cart = Cart::where('id', $cart_id)->whereNull('user_id')->whereNull('deleted_at')->first();
        } else {
            $cart = Cart::where('user_id', $request->access_token_user_id)->whereNull('deleted_at')->first();
        }

        $cartItemList = [];
        if (!empty($cart)) {
            $cartItemList = CartItem::where('cart_id', $cart->id)->whereNull('deleted_at')->get();
        }

        $data = [
            // For Sprint 1.1 demo only
            // 'cart_id' => $cart->id ?? null,
            'shop' => [],
        ];

        $productGroupList = [];
        foreach ($cartItemList as $cartItem) {
            $product = Product::where('id', $cartItem->product_id)->whereNull('deleted_at')->first();
            if (!empty($product)) {
                $productGroupList[$product->shop_id][] = [
                    'cart_item_id' => $cartItem->id,
                ];
            }
        }

        $shopCtr = 0;
        foreach ($productGroupList as $shopId => $productGroup) {
            $shop = app('App\Http\Controllers\ShopController')->shopGet($shopId, $request)->getData();
            if (!empty($shop)) {
                $data['shop'][$shopCtr] = [
                    'shop_id' => $shop->id,
                    'logo_url' => $shop->logo_url,
                    'name_en' => $shop->name_en,
                    'name_tc' => $shop->name_tc,
                    'name_sc' => $shop->name_sc,
                    'cart_date' => '',
                    'shop_date' => $shop->created_at,
                    'total_quantity' => 0,
                    'total_amount' => 0.00,
                ];

                $productCtr = 0;
                foreach ($productGroup as $cartItemId) {
                    $cartItem = CartItem::where('id', $cartItemId)->whereNull('deleted_at')->first();
                    if (!empty($cartItem)) {
                        $product = app('App\Http\Controllers\ProductController')->productGet($cartItem->product_id, $request)->getData();
                        if (!empty($product)) {
                            $data['shop'][$shopCtr]['product'][$productCtr] = [
                                'cart_item_id' => $cartItem->id,
                                'product_id' => $cartItem->product_id,
                                'attribute_id' => $cartItem->attribute_id,
                                'image_url' => $product->image[0]->url ?? null,
                                'name_en' => $product->name_en,
                                'name_tc' => $product->name_tc,
                                'name_sc' => $product->name_sc,
                                'shop_name_en' => $shop->name_en,
                                'shop_name_tc' => $shop->name_tc,
                                'shop_name_sc' => $shop->name_sc,
                                'description_en' => $product->description_en,
                                'description_tc' => $product->description_tc,
                                'description_sc' => $product->description_sc,
                                'cart_date' => $cartItem->created_at->format('Y-m-d H:i:s'),
                                'product_date' => $product->created_at,
                                'price' => $product->price_discounted ?? $product->price_original,
                                'quantity' => $cartItem->quantity,
                                'total_price' => $cartItem->quantity * ($product->price_discounted ?? $product->price_original),
                            ];
                            $data['shop'][$shopCtr]['cart_date'] = $cartItem->created_at->format('Y-m-d H:i:s');
                            $data['shop'][$shopCtr]['total_quantity'] += $data['shop'][$shopCtr]['product'][$productCtr]['quantity'];
                            $data['shop'][$shopCtr]['total_amount'] += $data['shop'][$shopCtr]['product'][$productCtr]['total_price'];
                            $productCtr++;
                        }
                    }
                }
            }
            $shopCtr++;
        }

        return response()->json($data, 200);
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
     *         response="200",
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
        // For Sprint 1.1 demo only
        $cart = Cart::whereNull('user_id')->whereNull('deleted_at')->first();
        if (empty($cart)) {
            $cart = Cart::create();
        }
        $request->request->add([
            'cart_id' => $cart->id,
        ]);

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

        if ($request->quantity < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid quantity',
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
     *         response="200",
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
        // For Sprint 1.1 demo only
        $cart = Cart::whereNull('user_id')->whereNull('deleted_at')->first();
        if (empty($cart)) {
            $cart = Cart::create();
        }
        $request->request->add([
            'cart_id' => $cart->id,
        ]);

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

        if (isset($request->quantity)) {
            if ($request->quantity < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid quantity',
                ], 400);
            }
        } else {
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
        // For Sprint 1.1 demo only
        $cart = Cart::whereNull('user_id')->whereNull('deleted_at')->first();
        if (empty($cart)) {
            $cart = Cart::create();
        }
        $request->request->add([
            'cart_id' => $cart->id,
        ]);

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

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $cartItem = CartItem::where('id', $request->cart_item_id)->where('cart_id', $cart->id)->whereNull('deleted_at')->first();
        $cartItem->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

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