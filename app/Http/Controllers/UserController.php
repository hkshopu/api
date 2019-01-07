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

        if (!empty($request->username)) {
            $userList = User::where('username', 'LIKE', '%' . $request->username . '%')->whereNull('deleted_at')->get();
        } else if (!empty($request->email)) {
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
     *     path="/api/signup",
     *     operationId="userSignup",
     *     tags={"User"},
     *     summary="Registers user from the app",
     *     description="Registers user from the app.",
     *     @OA\Parameter(
     *         name="token",
     *         in="header",
     *         description="The access token for authentication (API token instead of session token, provided by system admin)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="The user signup information",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="username",
     *                     type="string",
     *                     example="",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     example="",
     *                 ),
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     example="",
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     example="",
     *                 ),
     *                 @OA\Property(
     *                     property="gender",
     *                     type="string",
     *                     example="",
     *                 ),
     *                 @OA\Property(
     *                     property="birth_date",
     *                     type="string",
     *                     example="",
     *                 ),
     *                 @OA\Property(
     *                     property="mobile_phone",
     *                     type="string",
     *                     example="",
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     example="",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the user signed up",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the user signup failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function userSignup(Request $request = null)
    {
        if (empty($request->username)) {
            return response()->json([
                'success' => false,
                'message' => 'Username required',
            ], 400);
        } else if (preg_match('/[^a-zA-Z0-9_]/i', $request->username)) {
            return response()->json([
                'success' => false,
                'message' => 'Username should only contain alphanumeric characters and underscores',
            ], 400);
        } else if (!empty(User::where('username', $request->username)->first())) {
            // Explicit exclusion of the deleted_at field to avoid username duplication whether deleted or not
            return response()->json([
                'success' => false,
                'message' => 'Username already in use',
            ], 400);
        }

        if (empty($request->email)) {
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

        if (empty($request->password)) {
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

        if (empty($request->first_name)) {
            $request->request->add([
                'first_name' => null,
            ]);
        }

        if (empty($request->last_name)) {
            $request->request->add([
                'last_name' => null,
            ]);
        }

        if (empty($request->gender)) {
            $request->request->add([
                'gender' => null,
            ]);
        }

        if (empty($request->birth_date)) {
            $request->request->add([
                'birth_date' => null,
            ]);
        }

        if (empty($request->mobile_phone)) {
            $request->request->add([
                'mobile_phone' => null,
            ]);
        }

        if (empty($request->address)) {
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
                $user['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;
            } else {
                $user['status'] = null;
            }

            $user['shop'] = Shop::where('user_id', $user->id)->whereNull('deleted_at')->first();

            $image = new Image();
            $imageEntity = Entity::where('name', $image->getTable())->first();
            $imageList = Image::where('entity', $userEntity->id)->where('entity_id', $user->id)->where('sort', '<>', 0)->orderBy('sort', 'ASC')->get();
            $user['image'] = $imageList;
        }

        return response()->json($user, 200);
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
     *     @OA\RequestBody(
     *         description="The user login credentials",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="login",
     *                     description="Email/Phone/Username",
     *                     type="string",
     *                     example="",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     example="",
     *                 ),
     *             ),
     *         ),
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

        return response()->json($accessToken, 200);
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
}
