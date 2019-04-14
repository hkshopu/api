<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\AccessToken;
use App\User;
use App\UserType;
use App\Language;
use Carbon\Carbon;

class Authenticate
{
    const DEFAULT_LANGUAGE = 'en'; // English

    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $request->request->add([
            'filter_inactive' => true,
        ]);

        $language = Language::where('code', self::DEFAULT_LANGUAGE)->whereNull('deleted_at')->first();
        $request->request->add([
            'language' => $language->code,
        ]);

        if (empty($request->header('token'))) {

            // Bypass token authentication for guest account browsing
            $id = (count(explode('/', $request->getPathInfo())) == 4) ? explode('/', $request->getPathInfo())[3] : '';

            if (
                       "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/categorystatus"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/productstatus"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/shopstatus"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/commentstatus"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/blogstatus"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/userstatus"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/productfollowing/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/imagefollowing/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/shopfollowing/{$id}"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/productcategory"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/productcategory/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/productcategoryparent/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/shopcategory"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/shopcategory/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/blogcategory"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/blogcategory/{$id}"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/product"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/product/{$id}"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/productview/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/blogview/{$id}"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/shop"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/shop/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/shoppaymentmethod"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/shopshipment"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/shoprating/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/productrating/{$id}"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/shopcomment/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/blogcomment/{$id}"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/blog"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/blog/{$id}"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/bloglike/{$id}"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/user"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/user/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/logout"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/usertype"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/size"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/color"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/cart/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "POST /api/cart"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "DELETE /api/cart"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "PATCH /api/cart"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "POST /api/carttest"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "POST /api/assigncart"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/language"

                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/orderview/{$id}"
            ) {
                return $next($request);
            }

            // Enable token authentication
            if (env('API_AUTHENTICATION')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            // Disable token authentication
            } else {
                $request->request->add([
                    'access_token_user_id' => null,
                ]);

                // Loophole in filter_inactive in webadmin (Must be fixed)

                return $next($request);
            }
        }

        // Route for guest access initialization
        if ($request->getPathInfo() == '/api/login'
            || $request->getPathInfo() == '/api/register'
            || $request->getPathInfo() == '/api/signup') {
            if ($request->header('token') != 'hkshopu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token',
                ], 400);
            }
        // Route for logged in users authentication
        } else {
            // Route isn't for the login endpoint
            $accessToken = AccessToken::where('token', $request->header('token'))->whereNull('deleted_at')->first();

            if (empty($accessToken)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token',
                ], 400);
            }

            $user = User::where('id', $accessToken->user_id)->whereNull('deleted_at')->first();

            if (empty($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User for that token is already deleted',
                ], 400);
            }

            $dateCurrent = Carbon::now();
            $dateExpiration = new Carbon($accessToken->expires_at);

            if (env('TOKEN_EXPIRATION')) {
                if ($dateCurrent > $dateExpiration) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Expired token',
                    ], 400);
                }
            }

            $language = Language::where('id', $user->language_id)->whereNull('deleted_at')->first();

            $request->request->add([
                'access_token_user_id' => $accessToken->user_id,
                'language' => $language->code,
            ]);

            $userType = UserType::where('id', $user->user_type_id)->whereNull('deleted_at')->first();

            if ($userType->name == 'system administrator'
                    || $userType->name == 'system operator'
                    || $userType->name == 'retailer') {
                $request->request->add([
                    'filter_inactive' => false,
                ]);
            }
        }

        return $next($request);
    }
}

