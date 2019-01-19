<?php

namespace App\Http\Controllers;

use App\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
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
     *     path="/api/size",
     *     operationId="sizeList",
     *     tags={"Size"},
     *     summary="Retrieves all size",
     *     description="This provides all product size for frontend use.",
     *     @OA\Response(
     *         response="200",
     *         description="Returns available size",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function sizeList()
    {
        $sizeList = Size::whereNull('deleted_at')->get();

        $data = $sizeList;

        return response()->json($data, 200);
    }
}

