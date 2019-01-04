<?php

namespace App\Http\Controllers;

use App\News;
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

class NewsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/news",
     *     operationId="newsList",
     *     tags={"News"},
     *     summary="Retrieves all news",
     *     description="Retrieves all news, filterable by news title (in English), with pagination.",
     *     @OA\Parameter(
     *         name="title_en",
     *         in="query",
     *         description="The news title (in English)",
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
     *         description="Returns all news",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news list failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsList(Request $request = null)
    {
        $news = new News();
        $newsEntity = Entity::where('name', $news->getTable())->first();

        if (!empty($request->title_en)) {
            $newsList = News::where('title_en', 'LIKE', '%' . $request->title_en . '%')->whereNull('deleted_at')->get();
        } else {
            $newsList = News::whereNull('deleted_at')->get();
        }

        $pageNumber = (empty($request->page_number) || $request->page_number <= 0) ? 1 : (int) $request->page_number;
        $pageSize = (empty($request->page_size) || $request->page_size <= 0) ? 25 : (int) $request->page_size;
        $pageStart = ($pageNumber - 1) * $pageSize;
        $pageEnd = $pageNumber * $pageSize - 1;

        $newsListPaginated = [];
        foreach ($newsList as $newsKey => $news) {
            if ($newsKey >= $pageStart && $newsKey <= $pageEnd) {
                $newsListPaginated[] = $news;
            }
        }

        $newsList = $newsListPaginated;

        foreach ($newsList as $newsKey => $news) {
            $newsList[$newsKey] = self::newsGet($news->id)->getData();
        }

        return response()->json($newsList, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/news",
     *     operationId="newsCreate",
     *     tags={"News"},
     *     summary="Creates new news",
     *     description="Creates new news.",
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
     *         description="The news title (in English)",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="title_tc",
     *         in="query",
     *         description="The news title (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="title_sc",
     *         in="query",
     *         description="The news title (in Simplified Chinese)",
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
     *         description="The news content (in English)",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="content_tc",
     *         in="query",
     *         description="The news content (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="content_sc",
     *         in="query",
     *         description="The news content (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_top",
     *         in="query",
     *         description="The top of the news positioning",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date_publish_start",
     *         in="query",
     *         description="The news publishing start date (in YYYY-MM-DD format)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date_publish_end",
     *         in="query",
     *         description="The news publishing end date (in YYYY-MM-DD format)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the news created",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news create failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsCreate(Request $request)
    {
        if (empty($request->shop_id) || empty(Shop::where('id', $request->shop_id)->whereNull('deleted_at')->first())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shop id',
            ], 400);
        }

        $news = new News();
        $newsEntity = Entity::where('name', $news->getTable())->first();

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
        } else if ($category->entity <> $newsEntity->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category for the news',
            ], 400);
        }

        if (!empty($request->is_top) && ($request->is_top <> 1 && $request->is_top <> 0)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid value for is_top',
            ], 400);
        }

        $request->request->add([
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $news = News::create($request->all());

        // Setting DRAFT status for news
        $status = Status::where('name', 'draft')->whereNull('deleted_at')->first();

        $request->request->add([
            'entity' => $newsEntity->id,
            'entity_id' => $news->id,
            'category_id' => $request->category_id,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        CategoryMap::create($request->all());

        $request->request->add([
            'status_id' => $status->id,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        StatusMap::create($request->all());

        return response()->json(self::newsGet($news->id)->getData(), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/news/{id}",
     *     operationId="newsGet",
     *     tags={"News"},
     *     summary="Retrieves the news given the id",
     *     description="Retrieves the news given the id.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The news id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the news given the id",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsGet($id)
    {
        $news = News::where('id', $id)->whereNull('deleted_at')->first();

        if (!empty($news)) {
            $newsEntity = Entity::where('name', $news->getTable())->first();

            $categoryMap = CategoryMap::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($categoryMap)) {
                $news['category'] = Category::where('id', $categoryMap->category_id)->whereNull('deleted_at')->first();
            } else {
                $news['category'] = null;
            }

            $statusMap = StatusMap::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            if (!empty($statusMap)) {
                $news['status'] = (Status::where('id', $statusMap->status_id)->whereNull('deleted_at')->first())->name;
            } else {
                $news['status'] = null;
            }

            $image = new Image();
            $imageEntity = Entity::where('name', $image->getTable())->first();
            $imageList = Image::where('entity', $newsEntity->id)->where('entity_id', $news->id)->where('sort', '<>', 0)->orderBy('sort', 'ASC')->get();
            $news['image'] = $imageList;

            $newsViewList = View::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->get();
            $news['views'] = count($newsViewList);

            $newsLikeList = Like::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->get();
            $news['likes'] = count($newsLikeList);

            $newsCommentList = Comment::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->get();
            $news['comments'] = count($newsCommentList);
            
        }

        return response()->json($news, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/news/{id}",
     *     operationId="newsDelete",
     *     tags={"News"},
     *     summary="Deletes the news given the id",
     *     description="Deletes the news given the id.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The news id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the news delete status",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news delete failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsDelete($id, Request $request)
    {
        $news = News::where('id', $id)->whereNull('deleted_at')->first();
        if (empty($news)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid id',
            ], 400);
        }

        $request->request->add([
            'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'deleted_by' => 1,
        ]);

        $news->update($request->all());
        $newsEntity = Entity::where('name', $news->getTable())->first();

        $categoryMap = CategoryMap::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->first();
        if (!empty($categoryMap)) {
            $categoryMap->update($request->all());
        }

        $statusMap = StatusMap::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->first();
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
     *     path="/api/news/{id}",
     *     operationId="newsModify",
     *     tags={"News"},
     *     summary="Modifies the news given the id with only defined fields",
     *     description="Modifies the news given the id with only defined fields.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The news id",
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
     *         description="The news title (in English)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="title_tc",
     *         in="query",
     *         description="The news title (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="title_sc",
     *         in="query",
     *         description="The news title (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="The news category id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status_id",
     *         in="query",
     *         description="The news status id",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="content_en",
     *         in="query",
     *         description="The news content (in English)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="content_tc",
     *         in="query",
     *         description="The news content (in Traditional Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="content_sc",
     *         in="query",
     *         description="The news content (in Simplified Chinese)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="is_top",
     *         in="query",
     *         description="The top of the news positioning",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date_publish_start",
     *         in="query",
     *         description="The news publishing start date (in YYYY-MM-DD format)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date_publish_end",
     *         in="query",
     *         description="The news publishing end date (in YYYY-MM-DD format)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the news updated",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Returns the news update failure reason",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function newsModify($id, Request $request)
    {
        $news = News::where('id', $id)->whereNull('deleted_at')->first();
        $newsEntity = Entity::where('name', $news->getTable())->first();
        if (empty($news)) {
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
            } else if ($category->entity <> $newsEntity->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category for the news',
                ], 400);
            }
        }

        if (!empty($request->status_id)) {
            $status = Status::where('id', $request->status_id)->whereNull('deleted_at')->first();
            $statusOption = StatusOption::where('entity', $newsEntity->id)->where('status_id', $request->status_id)->whereNull('deleted_at')->first();
            if (empty($status)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status id',
                ], 400);
            } else if (empty($statusOption)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status for the news',
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

        // Update news table
        $request->request->add([
            'updated_by' => 1,
        ]);

        $news->update($request->all());

        // Update category_map table
        if (!empty($request->category_id)) {
            $request->request->add([
                'deleted_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'deleted_by' => 1,
            ]);

            $categoryMap = CategoryMap::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            $categoryMap->update($request->only([
                'deleted_at',
                'deleted_by',
            ]));

            $request->request->add([
                'entity' => $newsEntity->id,
                'entity_id' => $news->id,
                'created_by' => 1,
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
                'deleted_by' => 1,
            ]);

            $statusMap = StatusMap::where('entity', $newsEntity->id)->where('entity_id', $news->id)->whereNull('deleted_at')->orderBy('id', 'DESC')->first();
            $statusMap->update($request->only([
                'deleted_at',
                'deleted_by',
            ]));

            $request->request->add([
                'entity' => $newsEntity->id,
                'entity_id' => $news->id,
                'created_by' => 1,
            ]);

            StatusMap::create($request->only([
                'entity',
                'entity_id',
                'status_id',
                'created_by',
                'updated_by',
            ]));
        }

        $news = self::newsGet($id)->getData();
        return response()->json($news, 201);
    }
}