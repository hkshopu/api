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
        ) {
            return $next($request);
        }

        if (empty($request->header('token'))) {
            $request->request->add([
                'access_token_user_id' => 0,
            ]);

            return $next($request);

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Unauthorized',
            // ], 401);
        }

        if ($request->getPathInfo() == '/api/login'
            || $request->getPathInfo() == '/api/register'
            || $request->getPathInfo() == '/api/signup') {
            if ($request->header('token') != 'hkshopu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token',
                ], 400);
            }
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

            if ($dateCurrent > $dateExpiration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expired token',
                ], 400);
            }

            $request->request->add([
                'access_token_user_id' => $accessToken->user_id,
            ]);
        }

        return $next($request);
    }
}
