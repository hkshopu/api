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
    $router->get('categorystatus',  ['uses' => 'StatusController@categoryStatusList']);
    $router->get('productstatus',  ['uses' => 'StatusController@productStatusList']);
    // Following
    $router->post('imagefollowing', ['uses' => 'FollowingController@imageFollowingAdd']);
    $router->get('imagefollowing/{image_id}',  ['uses' => 'FollowingController@imageFollowingGet']);
    $router->delete('imagefollowing/{id}', ['uses' => 'FollowingController@imageFollowingDelete']);
    $router->post('productfollowing', ['uses' => 'FollowingController@productFollowingAdd']);
    $router->get('productfollowing/{product_id}',  ['uses' => 'FollowingController@productFollowingGet']);
    $router->delete('productfollowing/{id}', ['uses' => 'FollowingController@productFollowingDelete']);
    // Image
    $router->post('productimage/{id}', ['uses' => 'ImageController@productImageAdd']);
    $router->post('uploadimage', ['uses' => 'ImageController@uploadImage']);
    // Category
    $router->get('productcategory',  ['uses' => 'ProductCategoryController@list']);
    $router->post('productcategory',  ['uses' => 'ProductCategoryController@create']);
    $router->get('productcategory/{id}',  ['uses' => 'ProductCategoryController@fetch']);
    $router->patch('productcategory/{id}',  ['uses' => 'ProductCategoryController@update']);
    $router->delete('productcategory/{id}',  ['uses' => 'ProductCategoryController@delete']);
    $router->get('productcategoryparent/{id}',  ['uses' => 'ProductCategoryParentController@fetch']);
    $router->get('shopcategory',  ['uses' => 'ShopCategoryController@list']);
    $router->post('shopcategory',  ['uses' => 'ShopCategoryController@create']);
    $router->get('shopcategory/{id}',  ['uses' => 'ShopCategoryController@fetch']);
    $router->patch('shopcategory/{id}',  ['uses' => 'ShopCategoryController@update']);
    $router->delete('shopcategory/{id}',  ['uses' => 'ShopCategoryController@delete']);
    // Product
    $router->get('product',  ['uses' => 'ProductController@list']);
    $router->post('product', ['uses' => 'ProductController@create']);
    $router->get('product/{id}', ['uses' => 'ProductController@fetch']);
    $router->delete('product/{id}', ['uses' => 'ProductController@delete']);
    $router->patch('product/{id}', ['uses' => 'ProductController@update']);
    $router->post('productstockadd/{id}', ['uses' => 'ProductInventoryController@add']);
    $router->post('productstockremove/{id}', ['uses' => 'ProductInventoryController@remove']);
    // View
    $router->post('productview', ['uses' => 'ProductViewController@create']);
    $router->get('productview/{product_id}',  ['uses' => 'ProductViewController@fetch']);
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
