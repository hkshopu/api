<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
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
     *     path="/api/language",
     *     operationId="languageList",
     *     tags={"Language"},
     *     summary="Retrieves all language",
     *     description="This provides all language for frontend use.",
     *     @OA\Response(
     *         response="200",
     *         description="Returns available language",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function languageList()
    {
        $languageList = Language::whereNull('deleted_at')->get();

        $data = $languageList;

        return response()->json($data, 200);
    }
}

