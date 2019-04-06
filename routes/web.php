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

    // Status
    /* UTX */ $router->get('categorystatus',  ['uses' => 'StatusController@categoryStatusList']);
    /* UTX */ $router->get('productstatus',  ['uses' => 'StatusController@productStatusList']);
    /* UTX */ $router->get('shopstatus',  ['uses' => 'StatusController@shopStatusList']);
    /* UTX */ $router->get('commentstatus',  ['uses' => 'StatusController@commentStatusList']);
    /* UTX */ $router->get('blogstatus',  ['uses' => 'StatusController@blogStatusList']);
    /* UTX */ $router->get('userstatus',  ['uses' => 'StatusController@userStatusList']);

    // Following
    $router->post('productfollowing', ['uses' => 'FollowingController@productFollowingAdd']);
    /* UTX */ $router->get('productfollowing/{product_id}',  ['uses' => 'FollowingController@productFollowingGet']);
    $router->delete('productfollowing/{product_id}', ['uses' => 'FollowingController@productFollowingDelete']);
    $router->post('imagefollowing', ['uses' => 'FollowingController@imageFollowingAdd']);
    /* UTX */ $router->get('imagefollowing/{image_id}',  ['uses' => 'FollowingController@imageFollowingGet']);
    $router->delete('imagefollowing/{image_id}', ['uses' => 'FollowingController@imageFollowingDelete']);
    $router->post('shopfollowing', ['uses' => 'FollowingController@shopFollowingAdd']);
    /* UTX */ $router->get('shopfollowing/{shop_id}',  ['uses' => 'FollowingController@shopFollowingGet']);
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

    // Category
    /* UTX */ $router->get('productcategory',  ['uses' => 'CategoryController@productCategoryList']);
    $router->post('productcategory',  ['uses' => 'CategoryController@productCategoryAdd']);
    $router->get('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryGet']);
    $router->patch('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryModify']);
    $router->delete('productcategory/{id}',  ['uses' => 'CategoryController@productCategoryDelete']);
    $router->get('productcategoryparent/{id}',  ['uses' => 'CategoryController@productCategoryParentGet']);
    /* UTX */ $router->get('shopcategory',  ['uses' => 'CategoryController@shopCategoryList']);
    $router->post('shopcategory',  ['uses' => 'CategoryController@shopCategoryAdd']);
    $router->get('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryGet']);
    $router->patch('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryModify']);
    $router->delete('shopcategory/{id}',  ['uses' => 'CategoryController@shopCategoryDelete']);
    $router->get('blogcategory',  ['uses' => 'CategoryController@blogCategoryList']);
    /* UTX */ $router->post('blogcategory',  ['uses' => 'CategoryController@blogCategoryAdd']);
    $router->get('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryGet']);
    $router->patch('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryModify']);
    $router->delete('blogcategory/{id}',  ['uses' => 'CategoryController@blogCategoryDelete']);

    // Product
    /* UTX */ $router->get('product',  ['uses' => 'ProductController@productList']);
    $router->post('product', ['uses' => 'ProductController@productCreate']);
    $router->get('product/{id}', ['uses' => 'ProductController@productGet']);
    $router->delete('product/{id}', ['uses' => 'ProductController@productDelete']);
    $router->patch('product/{id}', ['uses' => 'ProductController@productModify']);
    $router->post('productstockadd/{id}', ['uses' => 'ProductController@productStockAdd']);
    $router->post('productstockremove/{id}', ['uses' => 'ProductController@productStockRemove']);
    $router->post('productattribute', ['uses' => 'ProductController@productAttributeAdd']);
    $router->delete('productattribute/{attribute_id}', ['uses' => 'ProductController@productAttributeDelete']);
    $router->patch('productattribute/{attribute_id}', ['uses' => 'ProductController@productAttributeModify']);

    // View
    $router->post('productview', ['uses' => 'ViewController@productViewAdd']);
    /* UTX */ $router->get('productview/{product_id}',  ['uses' => 'ViewController@productViewGet']);
    $router->post('blogview', ['uses' => 'ViewController@blogViewAdd']);
    /* UTX */ $router->get('blogview/{blog_id}',  ['uses' => 'ViewController@blogViewGet']);

    // Shop
    /* UTX */ $router->get('shop',  ['uses' => 'ShopController@shopList']);
    $router->post('shop', ['uses' => 'ShopController@shopCreate']);
    $router->get('shop/{id}', ['uses' => 'ShopController@shopGet']);
    $router->delete('shop/{id}', ['uses' => 'ShopController@shopDelete']);
    $router->patch('shop/{id}', ['uses' => 'ShopController@shopModify']);
    /* UTX */ $router->get('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodList']);
    $router->post('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodCreate']);
    $router->delete('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodDelete']);
    $router->patch('shoppaymentmethod', ['uses' => 'ShopController@shopPaymentMethodModify']);
    /* UTX */ $router->get('shopshipment', ['uses' => 'ShopController@shopShipmentList']);
    $router->patch('shopshipment', ['uses' => 'ShopController@shopShipmentModify']);

    // Rating
    $router->post('shoprating', ['uses' => 'RatingController@shopRatingAdd']);
    $router->get('shoprating/{shop_id}',  ['uses' => 'RatingController@shopRatingGet']);
    $router->delete('shoprating/{id}', ['uses' => 'RatingController@shopRatingDelete']);
    $router->post('productrating', ['uses' => 'RatingController@productRatingAdd']);
    $router->get('productrating/{product_id}',  ['uses' => 'RatingController@productRatingGet']);
    $router->delete('productrating/{id}', ['uses' => 'RatingController@productRatingDelete']);

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

    // Blog
    /* UTX */ $router->get('blog',  ['uses' => 'BlogController@blogList']);
    $router->post('blog', ['uses' => 'BlogController@blogCreate']);
    $router->get('blog/{id}', ['uses' => 'BlogController@blogGet']);
    $router->delete('blog/{id}', ['uses' => 'BlogController@blogDelete']);
    $router->patch('blog/{id}', ['uses' => 'BlogController@blogModify']);

    // Like
    $router->post('bloglike', ['uses' => 'LikeController@blogLikeAdd']);
    $router->get('bloglike/{blog_id}',  ['uses' => 'LikeController@blogLikeGet']);
    $router->delete('bloglike/{blog_id}', ['uses' => 'LikeController@blogLikeDelete']);

    // User
    /* UTX */ $router->get('user',  ['uses' => 'UserController@userList']);
    $router->post('user', ['uses' => 'UserController@userCreate']);
    $router->get('user/{id}', ['uses' => 'UserController@userGet']);
    $router->delete('user/{id}', ['uses' => 'UserController@userDelete']);
    $router->patch('user/{id}', ['uses' => 'UserController@userModify']);
    $router->post('register', ['uses' => 'UserController@userRegister']);
    $router->post('signup', ['uses' => 'UserController@userSignup']);
    $router->post('login',  ['uses' => 'UserController@userLogin']);
    /* UTX */ $router->get('logout',  ['uses' => 'UserController@userLogout']);
    $router->patch('updatepassword/{user_id}',  ['uses' => 'UserController@passwordUpdate']);

    // User Type
    /* UTX */ $router->get('usertype',  ['uses' => 'UserTypeController@userTypeList']);

    // Size
    /* UTX */ $router->get('size',  ['uses' => 'SizeController@sizeList']);

    // Color
    /* UTX */ $router->get('color',  ['uses' => 'ColorController@colorList']);

    // Cart
    $router->post('cart', ['uses' => 'CartController@cartAdd']);
    $router->post('carttest', ['uses' => 'CartController@cartAddTest']);
    $router->get('cart/{cart_id}',  ['uses' => 'CartController@cartGet']);
    $router->patch('cart',  ['uses' => 'CartController@cartModify']);
    $router->delete('cart', ['uses' => 'CartController@cartItemDelete']);
    $router->post('assigncart', ['uses' => 'CartController@cartAssign']);

    // Order
    $router->get('order', ['uses' => 'OrderController@orderList']);
    $router->post('order', ['uses' => 'OrderController@orderAdd']);
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    ///
    // $router->get('categorymap',  ['uses' => 'CategoryMapController@showAllCategoryMap']);
    // $router->get('categorymap/{id}', ['uses' => 'CategoryMapController@showOneCategoryMap']);
    // $router->post('categorymap', ['uses' => 'CategoryMapController@create']);
    // $router->delete('categorymap/{id}', ['uses' => 'CategoryMapController@delete']);
    // $router->put('categorymap/{id}', ['uses' => 'CategoryMapController@update']);
    
    // $router->get('color',  ['uses' => 'ColorController@showAllColor']);
    // $router->get('color/{id}', ['uses' => 'ColorController@showOneColor']);
    // $router->post('color', ['uses' => 'ColorController@create']);
    // $router->delete('color/{id}', ['uses' => 'ColorController@delete']);
    // $router->put('color/{id}', ['uses' => 'ColorController@update']);
    
    // $router->get('entity',  ['uses' => 'EntityController@showAllEntity']);
    // $router->get('entity/{id}', ['uses' => 'EntityController@showOneEntity']);
    // $router->post('entity', ['uses' => 'EntityController@create']);
    // $router->delete('entity/{id}', ['uses' => 'EntityController@delete']);
    // $router->put('entity/{id}', ['uses' => 'EntityController@update']);
    
    // $router->get('image',  ['uses' => 'ImageController@showAllImage']);
    // $router->get('image/{id}', ['uses' => 'ImageController@showOneImage']);
    // $router->post('image', ['uses' => 'ImageController@create']);
    // $router->delete('image/{id}', ['uses' => 'ImageController@delete']);
    // $router->put('image/{id}', ['uses' => 'ImageController@update']);
    
    // $router->get('productdiscount',  ['uses' => 'ProductDiscountController@showAllProductDiscount']);
    // $router->get('productdiscount/{id}', ['uses' => 'ProductDiscountController@showOneProductDiscount']);
    // $router->post('productdiscount', ['uses' => 'ProductDiscountController@create']);
    // $router->delete('productdiscount/{id}', ['uses' => 'ProductDiscountController@delete']);
    // $router->put('productdiscount/{id}', ['uses' => 'ProductDiscountController@update']);
    
    // $router->get('productinventory',  ['uses' => 'ProductInventoryController@showAllProductInventory']);
    // $router->get('productinventory/{id}', ['uses' => 'ProductInventoryController@showOneProductInventory']);
    // $router->post('productinventory', ['uses' => 'ProductInventoryController@create']);
    // $router->delete('productinventory/{id}', ['uses' => 'ProductInventoryController@delete']);
    // $router->put('productinventory/{id}', ['uses' => 'ProductInventoryController@update']);
    
    // $router->get('productpricing',  ['uses' => 'ProductPricingController@showAllProductPricing']);
    // $router->get('productpricing/{id}', ['uses' => 'ProductPricingController@showOneProductPricing']);
    // $router->post('productpricing', ['uses' => 'ProductPricingController@create']);
    // $router->delete('productpricing/{id}', ['uses' => 'ProductPricingController@delete']);
    // $router->put('productpricing/{id}', ['uses' => 'ProductPricingController@update']);
    
    // $router->get('size',  ['uses' => 'SizeController@showAllSize']);
    // $router->get('size/{id}', ['uses' => 'SizeController@showOneSize']);
    // $router->post('size', ['uses' => 'SizeController@create']);
    // $router->delete('size/{id}', ['uses' => 'SizeController@delete']);
    // $router->put('size/{id}', ['uses' => 'SizeController@update']);

});
