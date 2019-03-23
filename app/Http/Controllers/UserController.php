<?php

namespace App\Http\Controllers;

use App\User;
use App\UserType;
use App\AccessToken;
use App\Entity;
use App\Status;
use App\StatusMap;
use App\Shop;
use App\Image;
use App\Category;
use App\CategoryMap;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
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

    public function accessTokenGet(int $id) {
        $accessTokenDetails = [];
        $accessToken = AccessToken::where('id', $id)->whereNull('deleted_at')->first();
        $user = User::where('id', $accessToken->user_id)->whereNull('deleted_at')->first();

        $accessTokenDetails['id'] = $accessToken->id;
        $accessTokenDetails['access_token_id'] = $accessToken->id;
        $accessTokenDetails['token'] = $accessToken->token;
        $accessTokenDetails['expires_at'] = $accessToken->expires_at;
        $accessTokenDetails['created_at'] = $accessToken->created_at->format('Y-m-d H:i:s');
        $accessTokenDetails['user_id'] = $accessToken->user_id;

        $image = new Image();
        $imageEntity = Entity::where('name', $image->getTable())->first();
        $userEntity = Entity::where('name', $user->getTable())->first();
        $image = Image::where('entity', $userEntity->id)->where('entity_id', $user->id)->whereNull('deleted_at')->where('sort', '<>', 0)->orderBy('sort', 'ASC')->first();

        $accessTokenDetails['user'] = [];
        $accessTokenDetails['user']['id'] = $user->id;
        $accessTokenDetails['user']['nickname'] = $user->username;
        $accessTokenDetails['user']['image_url'] = $image->url ?? null;
        $accessTokenDetails['user']['join_date'] = $user->created_at->format('Y-m-d H:i:s');

        return $accessTokenDetails;
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     operationId="userList",
     *     tags={"User"},
     *     summary="Retrieves all user",
     *     description="Retrieves all user, filterable by username, email, with pagination.",
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
     *         name="username",
     *         in="query",
     *         description="The username",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The user email",
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
     *         description="Returns all user",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the user list failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function userList(Request $request = null)
    {
        $user = new User();

        if (isset($request->username)) {
            $userList = User::where('username', 'LIKE', '%' . $request->username . '%')->whereNull('deleted_at')->get();
        } else if (isset($request->email)) {
            $userList = User::where('email', 'LIKE', '%' . $request->email . '%')->whereNull('deleted_at')->get();
        } else {
            $userList = User::whereNull('deleted_at')->get();
        }

        $pageNumber = (empty($request->page_number) || $request->page_number <= 0) ? 1 : (int) $request->page_number;
        $pageSize = (empty($request->page_size) || $request->page_size <= 0) ? 25 : (int) $request->page_size;
        $pageStart = ($pageNumber - 1) * $pageSize;
        $pageEnd = $pageNumber * $pageSize - 1;

        $userListPaginated = [];
        foreach ($userList as $userKey => $user) {
            if ($userKey >= $pageStart && $userKey <= $pageEnd) {
                $userListPaginated[] = $user;
            }
        }

        $userList = $userListPaginated;

        foreach ($userList as $userKey => $user) {
            $userList[$userKey] = self::userGet($user->id)->getData();
        }

        return response()->json($userList, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/user",
     *     operationId="userCreate",
     *     tags={"User"},
     *     summary="Creates user from the web app",
     *     description="Creates user from the web app.",
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
     *         name="first_name",
     *         in="query",
     *         description="The first name",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="The last name",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="The username",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="The password",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="user_type_id",
     *         in="query",
     *         description="The user type id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="shop_name_en",
     *         in="query",
     *         description="The shop name en",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="shop_name_tc",
     *         in="query",
     *         description="The shop name tc",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="shop_name_sc",
     *         in="query",
     *         description="The shop name sc",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="shop_category_id",
     *         in="query",
     *         description="The shop category id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the user created",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the user create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function userCreate(Request $request = null)
    {
        if (!isset($request->username)) {
            return response()->json([
                'success' => false,
                'message' => 'Username required',
            ], 400);
        } else if (preg_match('/[^a-zA-Z0-9\.\-_]/i', $request->username)) {
            return response()->json([
                'success' => false,
                'message' => 'Username should only contain alphanumeric characters, dots/periods, hyphens, and underscores',
            ], 400);
        } else if (!empty(User::where('username', $request->username)->first())) {
            // Explicit exclusion of the deleted_at field to avoid username duplication whether deleted or not
            return response()->json([
                'success' => false,
                'message' => 'Username already in use',
            ], 400);
        }

        if (!isset($request->first_name)) {
            return response()->json([
                'success' => false,
                'message' => 'First name required',
            ], 400);
        } else if (preg_match('/[^a-zA-Z\s]/i', $request->first_name)) {
            return response()->json([
                'success' => false,
                'message' => 'First name should only contain alphabetical characters and spaces',
            ], 400);
        }

        if (!isset($request->last_name)) {
            return response()->json([
                'success' => false,
                'message' => 'Last name required',
            ], 400);
        } else if (preg_match('/[^a-zA-Z\s]/i', $request->last_name)) {
            return response()->json([
                'success' => false,
                'message' => 'Last name should only contain alphabetical characters and spaces',
            ], 400);
        }

        if (!isset($request->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Email required',
            ], 400);
        } else if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email',
            ], 400);
        } else if (!empty(User::where('email', $request->email)->first())) {
            // Explicit exclusion of the deleted_at field to avoid email duplication whether deleted or not
            return response()->json([
                'success' => false,
                'message' => 'Email already in use',
            ], 400);
        }

        if (!isset($request->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password required',
            ], 400);
        } else if (strlen($request->password) < 4) {
            return response()->json([
                'success' => false,
                'message' => 'Password should be at least 4 characters',
            ], 400);
        }

        // Set password hash
        $salt = '$2a$12$' . bin2hex(openssl_random_pseudo_bytes(16));
        $password = crypt($request->password, $salt);
        $request->request->add([
            'salt' => $salt,
            'password' => $password,
        ]);

        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $userType = UserType::where('id', $request->user_type_id)->whereNull('deleted_at')->first();

        if (empty($userType)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user type id',
            ], 400);
        }

        $request->request->add([
            'user_type_id' => $userType->id,
        ]);

        $user = User::create($request->only([
            'username',
            'email',
            'salt',
            'password',
            'first_name',
            'last_name',
            'user_type_id',
        ]));

        $request->request->add([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $user->update($request->only([
            'created_by',
            'updated_by',
        ]));

        $userEntity = Entity::where('name', $user->getTable())->first();

        // Setting ACTIVE status for user
        $status = Status::where('name', 'active')->whereNull('deleted_at')->first();

        $request->request->add([
            'entity' => $userEntity->id,
            'entity_id' => $user->id,
            'status_id' => $status->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $statusMap = StatusMap::create($request->only([
            'entity',
            'entity_id',
            'status_id',
            'created_by',
            'updated_by',
        ]));

        // Getting RETAILER user type for user
        $retailerUserType = UserType::where('name', 'Retailer')->whereNull('deleted_at')->first();

        if ($request->user_type_id == $retailerUserType->id) {
            if (!isset($request->shop_name_en)) {
                $user->delete();
                $statusMap->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Shop name (in English) required',
                ], 400);
            }

            $request->request->add([
                'name_en' => $request->shop_name_en,
            ]);

            if (!isset($request->shop_name_tc)) {
                $request->request->add([
                    'name_tc' => null,
                ]);
            }

            if (!isset($request->shop_name_sc)) {
                $request->request->add([
                    'name_sc' => null,
                ]);
            }

            $shop = new Shop();
            $shopEntity = Entity::where('name', $shop->getTable())->first();

            $category = Category::where('id', $request->shop_category_id)->whereNull('deleted_at')->first();

            if (empty($category)) {
                $user->delete();
                $statusMap->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category id',
                ], 400);
            } else if ($category->entity <> $shopEntity->id) {
                $user->delete();
                $statusMap->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category for the shop',
                ], 400);
            }

            $request->request->add([
                'description_en' => '',
                'user_id' => $user->id,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            $shop = Shop::create($request->only([
                'name_en',
                'name_tc',
                'name_sc',
                'description_en',
                'user_id',
                'created_by',
                'updated_by',
            ]));

            $shopEntity = Entity::where('name', $shop->getTable())->first();

            // Setting ACTIVE status for shop
            $status = Status::where('name', 'active')->whereNull('deleted_at')->first();

            $request->request->add([
                'entity' => $shopEntity->id,
                'entity_id' => $shop->id,
                'status_id' => $status->id,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            $statusMap = StatusMap::create($request->only([
                'entity',
                'entity_id',
                'status_id',
                'created_by',
                'updated_by',
            ]));

            $shopEntity = Entity::where('name', $shop->getTable())->first();

            $request->request->add([
                'entity' => $shopEntity->id,
                'entity_id' => $shop->id,
                'category_id' => $request->shop_category_id,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            $categoryMap = CategoryMap::create($request->only([
                'entity',
                'entity_id',
                'category_id',
                'created_by',
                'updated_by',
            ]));
        }

        return response()->json(self::userGet($user->id)->getData(), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     operationId="userGet",
     *     tags={"User"},
     *     summary="Retrieves the user given the id",
     *     description="Retrieves the user given the id.",
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
     *         description="The user id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the user given the id",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function userGet($id)
    {
        $user = User::where('id', $id)->whereNull('deleted_at')->first();

        if (!empty($user)) {
            $userEntity = Entity::where('name', $user->getTable())->first();

            $user['user_type'] = UserType::where('id', $user->user_type_id)->whereNull('deleted_at')->first();
            unset($user['user_type_id']);

            $statusMap = StatusMap::where('entity', $userEntity->id)->where('entity_id', $user->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($statusMap)) {
                $status = Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first();
                $user['status'] = (!empty($status)) ? $status->name : null;
            } else {
                $user['status'] = null;
            }

            $user['shop'] = Shop::where('user_id', $user->id)->whereNull('deleted_at')->first();

            $image = new Image();
            $imageEntity = Entity::where('name', $image->getTable())->first();
            $user['image'] = Image::where('entity', $userEntity->id)->where('entity_id', $user->id)->whereNull('deleted_at')->where('sort', '<>', 0)->orderBy('sort', 'ASC')->first();
        }

        return response()->json($user, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     operationId="userRegister",
     *     tags={"User"},
     *     summary="Registers retailer account from the web app",
     *     description="Registers retailer account from the web app.",
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         description="The access token for authentication (API token instead of session token, provided by system admin)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="The username",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="The first_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="The last_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="The password",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="shop_name_en",
     *         in="query",
     *         description="The shop_name_en",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="shop_name_tc",
     *         in="query",
     *         description="The shop_name_tc",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="shop_name_sc",
     *         in="query",
     *         description="The shop_name_sc",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="shop_category_id",
     *         in="query",
     *         description="The shop_category_id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the retailer registered",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the retailer registration failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function userRegister(Request $request = null)
    {
        if (!isset($request->username)) {
            return response()->json([
                'success' => false,
                'message' => 'Username required',
            ], 400);
        } else if (preg_match('/[^a-zA-Z0-9\.\-_]/i', $request->username)) {
            return response()->json([
                'success' => false,
                'message' => 'Username should only contain alphanumeric characters, dots/periods, hyphens, and underscores',
            ], 400);
        } else if (!empty(User::where('username', $request->username)->first())) {
            // Explicit exclusion of the deleted_at field to avoid username duplication whether deleted or not
            return response()->json([
                'success' => false,
                'message' => 'Username already in use',
            ], 400);
        }

        if (!isset($request->first_name)) {
            return response()->json([
                'success' => false,
                'message' => 'First name required',
            ], 400);
        } else if (preg_match('/[^a-zA-Z\s]/i', $request->first_name)) {
            return response()->json([
                'success' => false,
                'message' => 'First name should only contain alphabetical characters and spaces',
            ], 400);
        }

        if (!isset($request->last_name)) {
            return response()->json([
                'success' => false,
                'message' => 'Last name required',
            ], 400);
        } else if (preg_match('/[^a-zA-Z\s]/i', $request->last_name)) {
            return response()->json([
                'success' => false,
                'message' => 'Last name should only contain alphabetical characters and spaces',
            ], 400);
        }

        if (!isset($request->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Email required',
            ], 400);
        } else if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email',
            ], 400);
        } else if (!empty(User::where('email', $request->email)->first())) {
            // Explicit exclusion of the deleted_at field to avoid email duplication whether deleted or not
            return response()->json([
                'success' => false,
                'message' => 'Email already in use',
            ], 400);
        }

        if (!isset($request->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password required',
            ], 400);
        } else if (strlen($request->password) < 4) {
            return response()->json([
                'success' => false,
                'message' => 'Password should be at least 4 characters',
            ], 400);
        }

        // Set password hash
        $salt = '$2a$12$' . bin2hex(openssl_random_pseudo_bytes(16));
        $password = crypt($request->password, $salt);
        $request->request->add([
            'salt' => $salt,
            'password' => $password,
        ]);

        // Setting RETAILER user_type_id for user
        $userType = UserType::where('name', 'Retailer')->whereNull('deleted_at')->first();
        $request->request->add([
            'user_type_id' => $userType->id,
        ]);

        if (!isset($request->shop_name_en)) {
            return response()->json([
                'success' => false,
                'message' => 'Shop name (in English) required',
            ], 400);
        }

        $request->request->add([
            'name_en' => $request->shop_name_en,
        ]);

        if (!isset($request->shop_name_tc)) {
            $request->request->add([
                'name_tc' => null,
            ]);
        }

        if (!isset($request->shop_name_sc)) {
            $request->request->add([
                'name_sc' => null,
            ]);
        }

        $shop = new Shop();
        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $category = Category::where('id', $request->shop_category_id)->whereNull('deleted_at')->first();

        if (empty($category)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category id',
            ], 400);
        } else if ($category->entity <> $shopEntity->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category for the shop',
            ], 400);
        }

        $user = User::create($request->only([
            'username',
            'email',
            'salt',
            'password',
            'first_name',
            'last_name',
            'user_type_id',
        ]));

        $request->request->add([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $user->update($request->only([
            'created_by',
            'updated_by',
        ]));

        $userEntity = Entity::where('name', $user->getTable())->first();

        // Setting ACTIVE status for user
        $status = Status::where('name', 'active')->whereNull('deleted_at')->first();

        $request->request->add([
            'entity' => $userEntity->id,
            'entity_id' => $user->id,
            'status_id' => $status->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $statusMap = StatusMap::create($request->only([
            'entity',
            'entity_id',
            'status_id',
            'created_by',
            'updated_by',
        ]));

        $request->request->add([
            'description_en' => '',
            'user_id' => $user->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $shop = Shop::create($request->only([
            'name_en',
            'name_tc',
            'name_sc',
            'description_en',
            'user_id',
            'created_by',
            'updated_by',
        ]));

        $shopEntity = Entity::where('name', $shop->getTable())->first();

        // Setting ACTIVE status for shop
        $status = Status::where('name', 'active')->whereNull('deleted_at')->first();

        $request->request->add([
            'entity' => $shopEntity->id,
            'entity_id' => $shop->id,
            'status_id' => $status->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $statusMap = StatusMap::create($request->only([
            'entity',
            'entity_id',
            'status_id',
            'created_by',
            'updated_by',
        ]));

        $shopEntity = Entity::where('name', $shop->getTable())->first();

        $request->request->add([
            'entity' => $shopEntity->id,
            'entity_id' => $shop->id,
            'category_id' => $request->shop_category_id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $categoryMap = CategoryMap::create($request->only([
            'entity',
            'entity_id',
            'category_id',
            'created_by',
            'updated_by',
        ]));

        return response()->json(self::userGet($user->id)->getData(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/signup",
     *     operationId="userSignup",
     *     tags={"User"},
     *     summary="Signs up consumer account from the mobile app",
     *     description="Signs up consumer account from the mobile app.",
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         description="The access token for authentication (API token instead of session token, provided by system admin)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="The username",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="The password",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="The first name",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="The last name",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="gender",
     *         in="query",
     *         description="The gender",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="birth_date",
     *         in="query",
     *         description="The birth date",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="mobile_phone",
     *         in="query",
     *         description="The mobile phone",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         description="The address",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the consumer signed up",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the consumer signup failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function userSignup(Request $request = null)
    {
        if (!isset($request->username)) {
            return response()->json([
                'success' => false,
                'message' => 'Username required',
            ], 400);
        } else if (preg_match('/[^a-zA-Z0-9\.\-_]/i', $request->username)) {
            return response()->json([
                'success' => false,
                'message' => 'Username should only contain alphanumeric characters, dots/periods, hyphens, and underscores',
            ], 400);
        } else if (!empty(User::where('username', $request->username)->first())) {
            // Explicit exclusion of the deleted_at field to avoid username duplication whether deleted or not
            return response()->json([
                'success' => false,
                'message' => 'Username already in use',
            ], 400);
        }

        if (!isset($request->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Email required',
            ], 400);
        } else if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email',
            ], 400);
        } else if (!empty(User::where('email', $request->email)->first())) {
            // Explicit exclusion of the deleted_at field to avoid email duplication whether deleted or not
            return response()->json([
                'success' => false,
                'message' => 'Email already in use',
            ], 400);
        }

        if (!isset($request->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password required',
            ], 400);
        } else if (strlen($request->password) < 4) {
            return response()->json([
                'success' => false,
                'message' => 'Password should be at least 4 characters',
            ], 400);
        }

        // Set password hash
        $salt = '$2a$12$' . bin2hex(openssl_random_pseudo_bytes(16));
        $password = crypt($request->password, $salt);
        $request->request->add([
            'salt' => $salt,
            'password' => $password,
        ]);

        if (!isset($request->first_name)) {
            $request->request->add([
                'first_name' => null,
            ]);
        }

        if (!isset($request->last_name)) {
            $request->request->add([
                'last_name' => null,
            ]);
        }

        if (!isset($request->gender)) {
            $request->request->add([
                'gender' => null,
            ]);
        } else if (preg_match('/[^mf]/i', strtolower(substr($request->gender, 0, 1)))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid gender',
            ], 400);
        } else {
            $request->request->add([
                'gender' => strtolower(substr($request->gender, 0, 1)),
            ]);
        }

        if (!isset($request->birth_date)) {
            $request->request->add([
                'birth_date' => null,
            ]);
        }

        if (!isset($request->mobile_phone)) {
            $request->request->add([
                'mobile_phone' => null,
            ]);
        }

        if (!isset($request->address)) {
            $request->request->add([
                'address' => null,
            ]);
        }

        // Setting CONSUMER user_type_id for user
        $userType = UserType::where('name', 'Consumer')->whereNull('deleted_at')->first();
        $request->request->add([
            'user_type_id' => $userType->id,
        ]);

        $request->request->add([
            'activation_key' => bin2hex(openssl_random_pseudo_bytes(16)),
        ]);

        $user = User::create($request->all());
        $request->request->add([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $user->update($request->only([
            'created_by',
            'updated_by',
        ]));

        $userEntity = Entity::where('name', $user->getTable())->first();

        // Setting ACTIVE status for user
        $status = Status::where('name', 'active')->whereNull('deleted_at')->first();

        $request->request->add([
            'entity' => $userEntity->id,
            'entity_id' => $user->id,
            'status_id' => $status->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $statusMap = StatusMap::create($request->only([
            'entity',
            'entity_id',
            'status_id',
            'created_by',
            'updated_by',
        ]));

        return response()->json(self::userGet($user->id)->getData(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     operationId="userLogin",
     *     tags={"User"},
     *     summary="User login",
     *     description="Sends user credentials for login.",
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         description="The access token for authentication (API token instead of session token, provided by system admin)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="login",
     *         in="query",
     *         description="The login credentials (Email/Phone/Username)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="The password",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns access token",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the user login failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function userLogin(Request $request = null) {
        $user = User::where('username', $request->login)
                    ->orWhere('email', $request->login)
                    ->orWhere('mobile_phone', $request->login)
                    ->whereNull('deleted_at')
                    ->first();

        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user',
            ], 400);
        }

        // $salt = '$2a$12$' . bin2hex(openssl_random_pseudo_bytes(22));
        // $registrationEmail = $this->setEmail($this->newAccount['registration_login']);
        // $insertRegistration->execute([
        //     ':login' => $registrationEmail,
        //     ':user_password' => crypt($this->newAccount['registration_password'], $salt),
        //     ':salt' => $salt,
        // ]);

        $password = crypt($request->password, $user->salt);

        if ($password != $user->password) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 400);
        }

        $request->request->add([
            'user_id' => $user->id,
            'token' => bin2hex(openssl_random_pseudo_bytes(32)),
            // 'expires_at' => Carbon::now()->addYears(1)->format('Y-m-d H:i:s'),
            'expires_at' => Carbon::now()->addDays(1)->format('Y-m-d H:i:s'),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $accessToken = AccessToken::create($request->only([
            'user_id',
            'token',
            'expires_at',
            'created_by',
            'updated_by',
        ]));

        return response()->json(self::accessTokenGet($accessToken->id), 200);
    }

    /**
     * @OA\Get(
     *     path="/api/logout",
     *     operationId="userLogout",
     *     tags={"User"},
     *     summary="User logout",
     *     description="Sends access token for logout.",
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
     *         description="Returns the user logout status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the user logout failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function userLogout(Request $request = null) {
        $accessToken = AccessToken::where('token', $request->header('token'))
                    ->whereNull('deleted_at')
                    ->first();

        if (empty($accessToken)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $accessToken->update($request->only([
            'deleted_at',
            'deleted_by',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ], 200);
    }

    

    /**
     * @OA\Patch(
     *     path="/api/updatepassword/{user_id}",
     *     operationId="passwordUpdate",
     *     tags={"User"},
     *     summary="Update Password",
     *     description="Change password via admin.",
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
     *         name="user_id",
     *         in="path",
     *         description="The user id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="The new password",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the password update status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the password update failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function passwordUpdate(int $user_id, Request $request = null) {
        $user = User::where('id', $user_id)->whereNull('deleted_at')->first();
        if (empty($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user id',
            ], 400);
        }

        if (!isset($request->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password required',
            ], 400);
        } else if (strlen($request->password) < 4) {
            return response()->json([
                'success' => false,
                'message' => 'Password should be at least 4 characters',
            ], 400);
        }

        // Set password hash
        $salt = '$2a$12$' . bin2hex(openssl_random_pseudo_bytes(16));
        $password = crypt($request->password, $salt);
        $request->request->add([
            'salt' => $salt,
            'password' => $password,
        ]);

        $request->request->add([
            'updated_by' => $request->access_token_user_id,
        ]);

        $user->update($request->only([
            'salt',
            'password',
            'updated_by',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Password updated',
        ], 200);
    }
}

