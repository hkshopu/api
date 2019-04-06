<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\AccessToken;
use Carbon\Carbon;

class Authenticate
{
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
        if (empty($request->header('token'))) {

            // Bypass token authentication for guest account browsing
            $id = (count(explode('/', $request->getPathInfo())) == 4) ? explode('/', $request->getPathInfo())[3] : '';

            if (
                "{$request->getMethod()} {$request->getPathInfo()}" == 'GET /api/productcategory'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'GET /api/productcategoryparent'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'GET /api/shopcategory'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'GET /api/categorystatus'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'GET /api/productstatus'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'GET /api/shopstatus'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'GET /api/commentstatus'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'GET /api/blogstatus'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'GET /api/userstatus'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'GET /api/usertype'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == "GET /api/cart/{$id}"
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'POST /api/cart'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'DELETE /api/cart'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'PATCH /api/cart'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'POST /api/carttest'
                    || "{$request->getMethod()} {$request->getPathInfo()}" == 'POST /api/assigncart'
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

                return $next($request);
            }
        } else {
            
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

            $request->request->add([
                'access_token_user_id' => $accessToken->user_id,
            ]);
        }

        return $next($request);
    }
}

