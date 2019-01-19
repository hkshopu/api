<?php

namespace App\Http\Controllers;

use App\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
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
     *     path="/api/color",
     *     operationId="colorList",
     *     tags={"Color"},
     *     summary="Retrieves all color",
     *     description="This provides all product color for frontend use.",
     *     @OA\Response(
     *         response="200",
     *         description="Returns available color",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function colorList()
    {
        $colorList = Color::whereNull('deleted_at')->get();

        $data = $colorList;

        return response()->json($data, 200);
    }
}

