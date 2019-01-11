<?php

namespace App\Http\Controllers;

use App\UserType;
use App\Product;
use App\Shop;
use App\Blog;
use App\Entity;
use App\Status;
use App\StatusMap;
use App\StatusOption;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserTypeController extends Controller
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
     *     path="/api/usertype",
     *     operationId="userTypeList",
     *     tags={"User Type"},
     *     summary="Retrieves all user type",
     *     description="Retrieves all user types.",
     *     @OA\Response(
     *         response="200",
     *         description="Returns all user type",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function userTypeList(Request $request)
    {
        $data = UserType::whereNull('deleted_at')->get();

        return response()->json($data, 200);
    }
}

