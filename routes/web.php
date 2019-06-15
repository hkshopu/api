<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {

    // Blog
    $router->get('blog',  ['uses' => 'BlogController@blogList']);
    $router->post('blog', ['uses' => 'BlogController@blogCreate']);
    $router->get('blog/{id}', ['uses' => 'BlogController@blogGet']);
    $router->delete('blog/{id}', ['uses' => 'BlogController@blogDelete']);
    $router->patch('blog/{id}', ['uses' => 'BlogController@blogModify']);

    // Cart
    $router->post('cart', ['uses' => 'CartController@cartAdd']);
    $router->post('carttest', ['uses' => 'CartController@cartAddTest']);
    $router->get('cart/{cart_id}',  ['uses' => 'CartController@cartGet']);
    $router->patch('cart',  ['uses' => 'CartController@cartModify']);
    $router->delete('cart', ['uses' => 'CartController@cartDelete']);
    $router->post('assigncart', ['uses' => 'CartController@cartAssign']);

    // Category
    $router->get('productcategory',  ['uses' => 'CategoryController@productCategoryList']);
    $router->post('productcategory',  ['uses' => 'CategoryController@productCategoryAdd']);
    $router->get('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryGet']);
    $router->patch('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryModify']);
    $router->delete('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryDelete']);
    $router->get('productcategoryparent/{id}',  ['uses' => 'CategoryController@productCategoryParentGet']);
    $router->get('shopcategory',  ['uses' => 'CategoryController@shopCategoryList']);
    $router->post('shopcategory',  ['uses' => 'CategoryController@shopCategoryAdd']);
    $router->get('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryGet']);
    $router->patch('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryModify']);
    $router->delete('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryDelete']);
    $router->get('blogcategory',  ['uses' => 'CategoryController@blogCategoryList']);
    $router->post('blogcategory',  ['uses' => 'CategoryController@blogCategoryAdd']);
    $router->get('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryGet']);
    $router->patch('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryModify']);
    $router->delete('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryDelete']);

    // Color
    $router->get('color',  ['uses' => 'ColorController@colorList']);

    // Comment
    $router->post('shopcomment', ['uses' => 'CommentController@shopCommentAdd']);
    $router->get('shopcomment/{shop_id}',  ['uses' => 'CommentController@shopCommentGet']);
    $router->delete('shopcomment/{id}', ['uses' => 'CommentController@shopCommentDelete']);
    $router->patch('shopcommentenable/{id}', ['uses' => 'CommentController@shopCommentEnable']);
    $router->patch('shopcommentdisable/{id}', ['uses' => 'CommentController@shopCommentDisable']);
    $router->post('blogcomment', ['uses' => 'CommentController@blogCommentAdd']);
    $router->get('blogcomment/{blog_id}',  ['uses' => 'CommentController@blogCommentGet']);
    $router->delete('blogcomment/{id}', ['uses' => 'CommentController@blogCommentDelete']);
    $router->patch('blogcommentenable/{id}', ['uses' => 'CommentController@blogCommentEnable']);
    $router->patch('blogcommentdisable/{id}', ['uses' => 'CommentController@blogCommentDisable']);

    // Following
    $router->post('productfollowing', ['uses' => 'FollowingController@productFollowingAdd']);
    $router->get('productfollowing/{product_id}',  ['uses' => 'FollowingController@productFollowingGet']);
    $router->delete('productfollowing/{product_id}', ['uses' => 'FollowingController@productFollowingDelete']);
    $router->post('imagefollowing', ['uses' => 'FollowingController@imageFollowingAdd']);
    $router->get('imagefollowing/{image_id}',  ['uses' => 'FollowingController@imageFollowingGet']);
    $router->delete('imagefollowing/{image_id}', ['uses' => 'FollowingController@imageFollowingDelete']);
    $router->post('shopfollowing', ['uses' => 'FollowingController@shopFollowingAdd']);
    $router->get('shopfollowing/{shop_id}',  ['uses' => 'FollowingController@shopFollowingGet']);
    $router->delete('shopfollowing/{shop_id}', ['uses' => 'FollowingController@shopFollowingDelete']);

    // Image
    $router->post('productimage/{id}', ['uses' => 'ImageController@productImageAdd']);
    $router->delete('productimage/{id}', ['uses' => 'ImageController@productImageDelete']);
    $router->post('shopimage/{id}', ['uses' => 'ImageController@shopImageAdd']);
    $router->delete('shopimage/{id}', ['uses' => 'ImageController@shopImageDelete']);
    $router->post('blogimage/{id}', ['uses' => 'ImageController@blogImageAdd']);
    $router->delete('blogimage/{id}', ['uses' => 'ImageController@blogImageDelete']);
    $router->post('userimage/{id}', ['uses' => 'ImageController@userImageAdd']);
    $router->delete('userimage/{id}', ['uses' => 'ImageController@userImageDelete']);
    $router->post('uploadimage', ['uses' => 'ImageController@uploadImage']);

    // Language
    $router->get('language',  ['uses' => 'LanguageController@languageList']);

    // Like
    $router->post('bloglike', ['uses' => 'LikeController@blogLikeAdd']);
    $router->get('bloglike/{blog_id}',  ['uses' => 'LikeController@blogLikeGet']);
    $router->delete('bloglike/{blog_id}', ['uses' => 'LikeController@blogLikeDelete']);

    // Order
    $router->get('order', ['uses' => 'OrderController@orderList']);
    $router->post('order', ['uses' => 'OrderController@orderAdd']);
    $router->get('order/{id}', ['uses' => 'OrderController@orderGet']);
    $router->delete('order/{id}', ['uses' => 'OrderController@orderDelete']);
    $router->patch('order/{id}',  ['uses' => 'OrderController@orderModify']);

    // Product
    $router->get('product',  ['uses' => 'ProductController@productList']);
    $router->post('product', ['uses' => 'ProductController@productCreate']);
    $router->get('product/{id}', ['uses' => 'ProductController@productGet']);
    $router->delete('product/{id}', ['uses' => 'ProductController@productDelete']);
    $router->patch('product/{id}', ['uses' => 'ProductController@productModify']);
    $router->put('productstock/{product_id}', ['uses' => 'ProductController@productStockPut']);
    $router->post('productstock/{product_id}', ['uses' => 'ProductController@productStockPost']);
    $router->delete('productstock/{product_id}', ['uses' => 'ProductController@productStockDelete']);

    // Rating
    $router->post('shoprating', ['uses' => 'RatingController@shopRatingAdd']);
    $router->get('shoprating/{shop_id}',  ['uses' => 'RatingController@shopRatingGet']);
    $router->delete('shoprating/{id}', ['uses' => 'RatingController@shopRatingDelete']);
    $router->post('productrating', ['uses' => 'RatingController@productRatingAdd']);
    $router->get('productrating/{product_id}',  ['uses' => 'RatingController@productRatingGet']);
    $router->delete('productrating/{id}', ['uses' => 'RatingController@productRatingDelete']);

    // Shop
    $router->get('shop',  ['uses' => 'ShopController@shopList']);
    $router->post('shop', ['uses' => 'ShopController@shopCreate']);
    $router->get('shop/{id}', ['uses' => 'ShopController@shopGet']);
    $router->delete('shop/{id}', ['uses' => 'ShopController@shopDelete']);
    $router->patch('shop/{id}', ['uses' => 'ShopController@shopModify']);
    $router->get('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodList']);
    $router->post('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodCreate']);
    $router->delete('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodDelete']);
    $router->patch('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodModify']);
    $router->get('shopshipment', ['uses' => 'ShopController@shopShipmentList']);
    $router->patch('shopshipment', ['uses' => 'ShopController@shopShipmentModify']);

    // Size
    $router->get('size',  ['uses' => 'SizeController@sizeList']);

    // Status
    $router->get('categorystatus',  ['uses' => 'StatusController@categoryStatusList']);
    $router->get('productstatus',  ['uses' => 'StatusController@productStatusList']);
    $router->get('shopstatus',  ['uses' => 'StatusController@shopStatusList']);
    $router->get('commentstatus',  ['uses' => 'StatusController@commentStatusList']);
    $router->get('blogstatus',  ['uses' => 'StatusController@blogStatusList']);
    $router->get('userstatus',  ['uses' => 'StatusController@userStatusList']);
    $router->get('orderstatus',  ['uses' => 'StatusController@orderStatusList']);
    $router->get('paymentstatus',  ['uses' => 'StatusController@paymentStatusList']);
    $router->get('orderitemstatus',  ['uses' => 'StatusController@orderItemStatusList']);

    // User
    $router->get('user',  ['uses' => 'UserController@userList']);
    $router->post('user', ['uses' => 'UserController@userCreate']);
    $router->get('user/{id}', ['uses' => 'UserController@userGet']);
    $router->delete('user/{id}', ['uses' => 'UserController@userDelete']);
    $router->patch('user/{id}', ['uses' => 'UserController@userModify']);
    $router->post('register', ['uses' => 'UserController@userRegister']);
    $router->post('signup', ['uses' => 'UserController@userSignup']);
    $router->post('login',  ['uses' => 'UserController@userLogin']);
    $router->get('logout',  ['uses' => 'UserController@userLogout']);
    $router->patch('updatepassword/{user_id}',  ['uses' => 'UserController@passwordUpdate']);
    $router->patch('changelanguage',  ['uses' => 'UserController@languageChange']);

    // User Type
    $router->get('usertype',  ['uses' => 'UserTypeController@userTypeList']);

    // View
    $router->post('productview', ['uses' => 'ViewController@productViewAdd']);
    $router->get('productview/{product_id}',  ['uses' => 'ViewController@productViewGet']);
    $router->post('blogview', ['uses' => 'ViewController@blogViewAdd']);
    $router->get('blogview/{blog_id}',  ['uses' => 'ViewController@blogViewGet']);
    $router->post('orderview', ['uses' => 'ViewController@orderViewAdd']);
    $router->get('orderview/{order_id}',  ['uses' => 'ViewController@orderViewGet']);

});
