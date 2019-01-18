<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Shop;
use App\Product;
use App\Entity;
use App\Category;
use App\CategoryMap;
use App\Image;
use App\Following;
use App\Status;
use App\StatusMap;
use App\StatusOption;
use App\View;
use App\Like;
use App\Comment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BlogController extends Controller
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
     *     path="/api/blog",
     *     operationId="blogList",
     *     tags={"Blog"},
     *     summary="Retrieves all blog",
     *     description="Retrieves all blog, filterable by blog title (in English), with pagination.",
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
     *         name="shop_id",
     *         in="query",
     *         description="The shop id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="title_en",
     *         in="query",
     *         description="The blog title (in English)",
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
     *         description="Returns all blog",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog list failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogList(Request $request = null)
    {
        $blogFilter = Blog::whereNull('deleted_at');

        if (isset($request->shop_id)) {
            if (empty(Shop::where('id', $request->shop_id)->whereNull('deleted_at')->first())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid shop id',
                ], 400);
            } else {
                $blogFilter->where('shop_id', $request->shop_id);
            }
        }

        if (isset($request->title_en)) {
            $blogFilter->where('title_en', 'LIKE', '%' . $request->title_en . '%');
        }

        $blogList = $blogFilter->get();

        $pageNumber = (empty($request->page_number) || $request->page_number <= 0) ? 1 : (int) $request->page_number;
        $pageSize = (empty($request->page_size) || $request->page_size <= 0) ? 25 : (int) $request->page_size;
        $pageStart = ($pageNumber - 1) * $pageSize;
        $pageEnd = $pageNumber * $pageSize - 1;

        $blogListPaginated = [];
        foreach ($blogList as $blogKey => $blog) {
            if ($blogKey >= $pageStart && $blogKey <= $pageEnd) {
                $blogListPaginated[] = $blog;
            }
        }

        $blogList = $blogListPaginated;

        foreach ($blogList as $blogKey => $blog) {
            $blogList[$blogKey] = self::blogGet($blog->id)->getData();
        }

        return response()->json($blogList, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/blog",
     *     operationId="blogCreate",
     *     tags={"Blog"},
     *     summary="Creates new blog",
     *     description="Creates new blog.",
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
     *         name="shop_id",
     *         in="query",
     *         description="The shop id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="title_en",
     *         in="query",
     *         description="The blog title (in English)",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="title_tc",
     *         in="query",
     *         description="The blog title (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="title_sc",
     *         in="query",
     *         description="The blog title (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="The category id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="content_en",
     *         in="query",
     *         description="The blog content (in English)",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="content_tc",
     *         in="query",
     *         description="The blog content (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="content_sc",
     *         in="query",
     *         description="The blog content (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_top",
     *         in="query",
     *         description="The top of the blog positioning",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date_publish_start",
     *         in="query",
     *         description="The blog publishing start date (in YYYY-MM-DD format)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date_publish_end",
     *         in="query",
     *         description="The blog publishing end date (in YYYY-MM-DD format)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the blog created",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogCreate(Request $request)
    {
        if (empty($request->shop_id) || empty(Shop::where('id', $request->shop_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $blog = new Blog();
        $blogEntity = Entity::where('name', $blog->getTable())->first();

        if (empty($request->category_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category id',
            ], 400);
        }

        $category = Category::where('id', $request->category_id)->whereNull('deleted_at')->first();

        if (empty($category)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category id',
            ], 400);
        } else if ($category->entity <> $blogEntity->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category for the blog',
            ], 400);
        }

        if (!empty($request->is_top) && ($request->is_top <> 1 && $request->is_top <> 0)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid value for is_top',
            ], 400);
        }

        $request->request->add([
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        $blog = Blog::create($request->all());

        // Setting DRAFT status for blog
        $status = Status::where('name', 'draft')->whereNull('deleted_at')->first();

        $request->request->add([
            'entity' => $blogEntity->id,
            'entity_id' => $blog->id,
            'category_id' => $request->category_id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        CategoryMap::create($request->all());

        $request->request->add([
            'status_id' => $status->id,
            'created_by' => $request->access_token_user_id,
            'updated_by' => $request->access_token_user_id,
        ]);

        StatusMap::create($request->all());

        return response()->json(self::blogGet($blog->id)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/blog/{id}",
     *     operationId="blogGet",
     *     tags={"Blog"},
     *     summary="Retrieves the blog given the id",
     *     description="Retrieves the blog given the id.",
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
     *         description="The blog id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the blog given the id",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogGet($id)
    {
        $blog = Blog::where('id', $id)->whereNull('deleted_at')->first();

        if (!empty($blog)) {
            $blogEntity = Entity::where('name', $blog->getTable())->first();

            $categoryMap = CategoryMap::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($categoryMap)) {
                $blog['category'] = Category::where('id', $categoryMap->category_id)->whereNull('deleted_at')->first();
            } else {
                $blog['category'] = null;
            }

            $statusMap = StatusMap::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($statusMap)) {
                $blog['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;
            } else {
                $blog['status'] = null;
            }

            $image = new Image();
            $imageEntity = Entity::where('name', $image->getTable())->first();
            $imageList = Image::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->where('sort', '<>', 0)->orderBy('sort', 'ASC')->get();
            $blog['image'] = $imageList;

            $blogViewList = View::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->get();
            $blog['views'] = count($blogViewList);

            $blogLikeList = Like::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->get();
            $blog['likes'] = count($blogLikeList);

            $blogCommentList = Comment::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->get();
            $blog['comments'] = count($blogCommentList);
            
        }

        return response()->json($blog, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/blog/{id}",
     *     operationId="blogDelete",
     *     tags={"Blog"},
     *     summary="Deletes the blog given the id",
     *     description="Deletes the blog given the id.",
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
     *         description="The blog id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the blog delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogDelete($id, Request $request)
    {
        $blog = Blog::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($blog)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid id',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => $request->access_token_user_id,
        ]);

        $blog->update($request->all());
        $blogEntity = Entity::where('name', $blog->getTable())->first();

        $categoryMap = CategoryMap::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->first();
        if (!empty($categoryMap)) {
            $categoryMap->update($request->all());
        }

        $statusMap = StatusMap::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->first();
        if (!empty($statusMap)) {
            $statusMap->update($request->all());
        }

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ], 200);
    }

    /**
     * @OA\Patch(
     *     path="/api/blog/{id}",
     *     operationId="blogModify",
     *     tags={"Blog"},
     *     summary="Modifies the blog given the id with only defined fields",
     *     description="Modifies the blog given the id with only defined fields.",
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
     *         description="The blog id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="shop_id",
     *         in="query",
     *         description="The shop id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="title_en",
     *         in="query",
     *         description="The blog title (in English)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="title_tc",
     *         in="query",
     *         description="The blog title (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="title_sc",
     *         in="query",
     *         description="The blog title (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="The blog category id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status_id",
     *         in="query",
     *         description="The blog status id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="content_en",
     *         in="query",
     *         description="The blog content (in English)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="content_tc",
     *         in="query",
     *         description="The blog content (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="content_sc",
     *         in="query",
     *         description="The blog content (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_top",
     *         in="query",
     *         description="The top of the blog positioning",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date_publish_start",
     *         in="query",
     *         description="The blog publishing start date (in YYYY-MM-DD format)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date_publish_end",
     *         in="query",
     *         description="The blog publishing end date (in YYYY-MM-DD format)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the blog updated",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the blog update failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function blogModify($id, Request $request)
    {
        $blog = Blog::where('id', $id)->whereNull('deleted_at')->first();
        $blogEntity = Entity::where('name', $blog->getTable())->first();
        if (empty($blog)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid id',
            ], 400);
        }

        if (!empty($request->shop_id) && empty(Shop::where('id', $request->shop_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        if (!empty($request->title_en)) {
            $request->request->add(['title_en' => $request->title_en]);
        }

        if (!empty($request->title_tc)) {
            $request->request->add(['title_tc' => $request->title_tc]);
        }

        if (!empty($request->title_sc)) {
            $request->request->add(['title_sc' => $request->title_sc]);
        }

        if (!empty($request->category_id)) {
            $category = Category::where('id', $request->category_id)->whereNull('deleted_at')->first();
            if (empty($category)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category id',
                ], 400);
            } else if ($category->entity <> $blogEntity->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category for the blog',
                ], 400);
            }
        }

        if (!empty($request->status_id)) {
            $status = Status::where('id', $request->status_id)->whereNull('deleted_at')->first();
            $statusOption = StatusOption::where('entity', $blogEntity->id)->where('status_id', $request->status_id)->whereNull('deleted_at')->first();
            if (empty($status)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status id',
                ], 400);
            } else if (empty($statusOption)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status for the blog',
                ], 400);
            }
        }

        if (!empty($request->content_en)) {
            $request->request->add(['content_en' => $request->content_en]);
        }

        if (!empty($request->content_tc)) {
            $request->request->add(['content_tc' => $request->content_tc]);
        }

        if (!empty($request->content_sc)) {
            $request->request->add(['content_sc' => $request->content_sc]);
        }

        if (!empty($request->is_top) && ($request->is_top <> 1 && $request->is_top <> 0)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid value for is_top',
            ], 400);
        }

        // Update blog table
        $request->request->add([
            'updated_by' => $request->access_token_user_id,
        ]);

        $blog->update($request->all());

        // Update category_map table
        if (!empty($request->category_id)) {
            $request->request->add([
                'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'deleted_by' => $request->access_token_user_id,
            ]);

            $categoryMap = CategoryMap::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            $categoryMap->update($request->only([
                'deleted_at',
                'deleted_by',
            ]));

            $request->request->add([
                'entity' => $blogEntity->id,
                'entity_id' => $blog->id,
                'created_by' => $request->access_token_user_id,
            ]);

            CategoryMap::create($request->only([
                'entity',
                'entity_id',
                'category_id',
                'created_by',
                'updated_by',
            ]));
        }

        // Update status_map table
        if (!empty($request->status_id)) {
            $request->request->add([
                'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'deleted_by' => $request->access_token_user_id,
            ]);

            $statusMap = StatusMap::where('entity', $blogEntity->id)->where('entity_id', $blog->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            $statusMap->update($request->only([
                'deleted_at',
                'deleted_by',
            ]));

            $request->request->add([
                'entity' => $blogEntity->id,
                'entity_id' => $blog->id,
                'created_by' => $request->access_token_user_id,
            ]);

            StatusMap::create($request->only([
                'entity',
                'entity_id',
                'status_id',
                'created_by',
                'updated_by',
            ]));
        }

        $blog = self::blogGet($id)->getData();
        return response()->json($blog, 201);
    }
}