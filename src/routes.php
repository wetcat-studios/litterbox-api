<?php

// Public auth routes
Route::group(['prefix' => 'auth', 'middleware' => ['cors', 'litterbox-guest']], function () 
{
  Route::post('login', ['uses' => 'Wetcat\Litterbox\Controllers\AuthController@login']);
  Route::post('register', ['uses' => 'Wetcat\Litterbox\Controllers\AuthController@register']);
  Route::post('forgot', ['uses' => 'Wetcat\Litterbox\Controllers\AuthController@forgot']);
  Route::post('reset', ['uses' => 'Wetcat\Litterbox\Controllers\AuthController@reset']);
  Route::post('request', ['uses' => 'Wetcat\Litterbox\Controllers\AuthController@request']);
});


// Protected auth routes
Route::group(['prefix' => 'user', 'middleware' => ['cors', 'litterbox-auth']], function () 
{
  Route::put('password/{uuid}', ['uses' => 'Wetcat\Litterbox\Controllers\AuthController@password']);
  Route::post('name/{uuid}', ['uses' => 'Wetcat\Litterbox\Controllers\AuthController@name']);
});

// Unprotected API
Route::group(['middleware' => ['cors']], function () {
  // Articles
  Route::resource('articles', 'Wetcat\Litterbox\Controllers\ArticleController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
  Route::resource('articles.batches', 'Wetcat\Litterbox\Controllers\ArticleBatchController', ['only' => ['index', 'store', 'update', 'destroy']]);
  Route::resource('articles.intrastats', 'Wetcat\Litterbox\Controllers\ArticleIntrastatController', ['only' => ['index', 'store', 'update', 'destroy']]);
  Route::resource('articles.brands', 'Wetcat\Litterbox\Controllers\ArticleBrandController', ['only' => ['index', 'store', 'update', 'destroy']]);
  Route::resource('articles.categories', 'Wetcat\Litterbox\Controllers\ArticleCategoryController', ['only' => ['index', 'store', 'update', 'destroy']]);
  Route::resource('articles.manufacturers', 'Wetcat\Litterbox\Controllers\ArticleManufacturerController', ['only' => ['index', 'store', 'update', 'destroy']]);
  Route::resource('articles.segments', 'Wetcat\Litterbox\Controllers\ArticleSegmentController', ['only' => ['index', 'store', 'update', 'destroy']]);
  
  // Batches
  Route::resource('batches', 'Wetcat\Litterbox\Controllers\BatchController', ['only' => ['index', 'show', 'update', 'destroy']]);
    
  // Brands 
  Route::resource('brands', 'Wetcat\Litterbox\Controllers\BrandController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
  
  // Chains
  Route::resource('chains', 'Wetcat\Litterbox\Controllers\ChainController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  
  // ChainSegments
  Route::resource('chainsegments', 'Wetcat\Litterbox\Controllers\ChainSegmentController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  
  // Categories
  Route::resource('categories', 'Wetcat\Litterbox\Controllers\CategoryController', ['only' => ['index', 'store']]);
  
  // Manufacturers
  Route::resource('manufacturers', 'Wetcat\Litterbox\Controllers\ManufacturerController', ['only' => ['index', 'show', 'update', 'store', 'destroy']]);
  
  // Segments
  Route::resource('segments', 'Wetcat\Litterbox\Controllers\SegmentController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
  
  // Intrastat
  Route::resource('intrastats', 'Wetcat\Litterbox\Controllers\IntrastatController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('intrastats.articles', 'Wetcat\Litterbox\Controllers\IntrastatArticleController', ['only' => ['index', 'update', 'destroy']]);
  
  /*
  
  Route::resource('campaigns', 'Wetcat\Litterbox\Controllers\CampaignController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  
  
  Route::resource('chainsegments', 'Wetcat\Litterbox\Controllers\ChainSegmentController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('ingredients', 'Wetcat\Litterbox\Controllers\IngredientController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);

  Route::resource('pictures', 'Wetcat\Litterbox\Controllers\PictureController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('segments', 'Wetcat\Litterbox\Controllers\SegmentController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('groups', 'Wetcat\Litterbox\Controllers\GroupController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('customersegments', 'Wetcat\Litterbox\Controllers\CustomerSegmentController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  
  Route::resource('user.customer', 'Wetcat\Litterbox\Controllers\UserCustomerController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  */
});


  
// API
Route::group(['middleware' => ['cors', 'litterbox-auth']], function ()
{
  /*
  Route::resource('addresses', 'Wetcat\Litterbox\Controllers\AddressController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  
  Route::resource('cities', 'Wetcat\Litterbox\Controllers\CityController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('countries', 'Wetcat\Litterbox\Controllers\CountryController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('counties', 'Wetcat\Litterbox\Controllers\CountyController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('currencies', 'Wetcat\Litterbox\Controllers\CurrencyController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('customers', 'Wetcat\Litterbox\Controllers\CustomerController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('emails', 'Wetcat\Litterbox\Controllers\EmailController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('groups.pricelists', 'Wetcat\Litterbox\Controllers\PricelistController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('intrastat', 'Wetcat\Litterbox\Controllers\IntrastatController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('orders', 'Wetcat\Litterbox\Controllers\OrderController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('phones', 'Wetcat\Litterbox\Controllers\PhoneController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('rates', 'Wetcat\Litterbox\Controllers\RateController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('restock', 'Wetcat\Litterbox\Controllers\RestockController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('restocksuggestions', 'Wetcat\Litterbox\Controllers\RestockSuggestionController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('shippingmethods', 'Wetcat\Litterbox\Controllers\ShippingmethodController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('users', 'Wetcat\Litterbox\Controllers\UserController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('app', 'Wetcat\Litterbox\Controllers\AppController', ['only' => ['index', 'store']]);

  Route::get('customers/number/{name}', function ($name) {
    return response()->json([
      'status'    => 200,
      'data'      => GoodtradeAdmin\CustomerHelper::createCustomerNumber($name),
      'heading'   => 'Customer number',
      'messages'  => null
    ], 200);
  });

  // Special route for validating customers
  Route::put('customers/{customer}/verify', ['uses' => 'Wetcat\Litterbox\Controllers\CustomerController@verify']);
  
  // Special route for signing orders (uuid) for users (uuid)
  Route::post('orders/{order}/sign/{user}', ['uses' => 'Wetcat\Litterbox\Controllers\OrderController@sign']);
  */
});