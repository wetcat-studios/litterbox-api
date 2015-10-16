<?php

$app->group(['middleware' => ['auth']], function ($app) 
{
	// Addresses
	$app->get('addresses', 'AddressController@index');
	$app->get('addresses/{id}', 'AddressController@show');
	$app->post('addresses', 'AddressController@store');
	$app->put('addresses/{id}', 'AddressController@update');
	$app->delete('addresses/{id}', 'AddressController@destroy');
	// Articles
	$app->get('articles', 'ArticleController@index');
	$app->get('articles/{id}', 'ArticleController@show');
	$app->post('articles', 'ArticleController@index');
	$app->put('articles/{id}', 'ArticleController@index');
	$app->delete('articles/{id}', 'ArticleController@index');
	// Batches
	$app->get('batches', 'BatchController@index');
	$app->get('batches/{id}', 'BatchController@show');
	$app->post('batches', 'BatchController@index');
	$app->put('batches/{id}', 'BatchController@index');
	$app->delete('batches/{id}', 'BatchController@index');
	// Brands
	$app->get('brands', 'BrandController@index');
	$app->get('brands/{id}', 'BrandController@show');
	$app->post('brands', 'BrandController@index');
	$app->put('brands/{id}', 'BrandController@index');
	$app->delete('brands/{id}', 'BrandController@index');
	// Campaigns
	$app->get('campaigns', 'CampaignController@index');
	$app->get('campaigns/{id}', 'CampaignController@show');
	$app->post('campaigns', 'CampaignController@index');
	$app->put('campaigns/{id}', 'CampaignController@index');
	$app->delete('campaigns/{id}', 'CampaignController@index');
	// Categories
	$app->get('categories', 'CategoryController@index');
	$app->get('categories/{id}', 'CategoryController@show');
	$app->post('categories', 'CategoryController@store');
	$app->put('categories/{id}', 'CategoryController@update');
	$app->delete('categories/{id}', 'CategoryController@destroy');
	// Chains
	$app->get('chains', 'ChainController@index');
	$app->get('chains/{id}', 'ChainController@show');
	$app->post('chains', 'ChainController@store');
	$app->put('chains/{id}', 'ChainController@update');
	$app->delete('chains/{id}', 'ChainController@destroy');
	// Chainsegments
	$app->get('chainsegments', 'ChainSegmentController@index');
	$app->get('chainsegments/{id}', 'ChainSegmentController@show');
	$app->post('chainsegments', 'ChainSegmentController@store');
	$app->put('chainsegments/{id}', 'ChainSegmentController@update');
	$app->delete('chainsegments/{id}', 'ChainSegmentController@destroy');
	// Cities
	$app->get('cities', 'CityController@index');
	$app->get('cities/{id}', 'CityController@show');
	$app->post('cities', 'CityController@store');
	$app->put('cities/{id}', 'CityController@update');
	$app->delete('cities/{id}', 'CityController@destroy');
	// Countries
	$app->get('countries', 'CountryController@index');
	$app->get('countries/{id}', 'CountryController@show');
	$app->post('countries', 'CountryController@store');
	$app->put('countries/{id}', 'CountryController@update');
	$app->delete('countries/{id}', 'CountryController@destroy');
	// Counties
	$app->get('counties', 'CountyController@index');
	$app->get('counties/{id}', 'CountyController@show');
	$app->post('counties', 'CountyController@store');
	$app->put('counties/{id}', 'CountyController@update');
	$app->delete('counties/{id}', 'CountyController@destroy');
	// Currencies
	$app->get('currencies', 'CurrencyController@index');
	$app->get('currencies/{id}', 'CurrencyController@show');
	$app->post('currencies', 'CurrencyController@store');
	$app->put('currencies/{id}', 'CurrencyController@update');
	$app->delete('currencies/{id}', 'CurrencyController@destroy');
	// Customers
	$app->get('customers', 'CustomerController@index');
	$app->get('customers/{id}', 'CustomerController@show');
	$app->post('customers', 'CustomerController@store');
	$app->put('customers/{id}', 'CustomerController@update');
	$app->delete('customers/{id}', 'CustomerController@destroy');
	// Customersegment
	$app->get('customersegments', 'CustomerSegmentController@index');
	$app->get('customersegments/{id}', 'CustomerSegmentController@show');
	$app->post('customersegments', 'CustomerSegmentController@store');
	$app->put('customersegments/{id}', 'CustomerSegmentController@update');
	$app->delete('customersegments/{id}', 'CustomerSegmentController@destroy');
	// Emails
	$app->get('emails', 'EmailController@index');
	$app->get('emails/{id}', 'EmailController@show');
	$app->post('emails', 'EmailController@store');
	$app->put('emails/{id}', 'EmailController@update');
	$app->delete('emails/{id}', 'EmailController@destroy');
	// Ingredients
	$app->get('ingredients', 'IngredientController@index');
	$app->get('ingredients/{id}', 'IngredientController@show');
	$app->post('ingredients', 'IngredientController@store');
	$app->put('ingredients/{id}', 'IngredientController@update');
	$app->delete('ingredients/{id}', 'IngredientController@destroy');	
	// Manufacturers
	$app->get('manufacturers', 'ManufacturerController@index');
	$app->get('manufacturers/{id}', 'ManufacturerController@show');
	$app->post('manufacturers', 'ManufacturerController@store');
	$app->put('manufacturers/{id}', 'ManufacturerController@update');
	$app->delete('manufacturers/{id}', 'ManufacturerController@destroy');
	// Orders
	$app->get('orders', 'OrderController@index');
	$app->get('orders/{id}', 'OrderController@show');
	$app->post('orders', 'OrderController@store');
	$app->put('orders/{id}', 'OrderController@update');
	$app->delete('orders/{id}', 'OrderController@destroy');
	// Phones
	$app->get('phones', 'PhoneController@index');
	$app->get('phones/{id}', 'PhoneController@show');
	$app->post('phones', 'PhoneController@store');
	$app->put('phones/{id}', 'PhoneController@update');
	$app->delete('phones/{id}', 'PhoneController@destroy');
	// Picture
	$app->get('pictures', 'PictureController@index');
	$app->get('pictures/{id}', 'PictureController@show');
	$app->post('pictures', 'PictureController@store');
	$app->put('pictures/{id}', 'PictureController@update');
	$app->delete('pictures/{id}', 'PictureController@destroy');
	// Rates
	$app->get('rates', 'RateController@index');
	$app->get('rates/{id}', 'RateController@show');
	$app->post('rates', 'RateController@store');
	$app->put('rates/{id}', 'RateController@update');
	$app->delete('rates/{id}', 'RateController@destroy');
	// Restocks
	$app->get('restock', 'RestockController@index');
	$app->get('restock/{id}', 'RestockController@show');
	$app->post('restock', 'RestockController@store');
	$app->put('restock/{id}', 'RestockController@update');
	$app->delete('restock/{id}', 'RestockController@destroy');
	// Segments
	$app->get('segments', 'SegmentController@index');
	$app->get('segments/{id}', 'SegmentController@show');
	$app->post('segments', 'SegmentController@store');
	$app->put('segments/{id}', 'SegmentController@update');
	$app->delete('segments/{id}', 'SegmentController@destroy');
	// Shippingmethods
	$app->get('shippingmethods', 'ShippingmethodController@index');
	$app->get('shippingmethods/{id}', 'ShippingmethodController@show');
	$app->post('shippingmethods', 'ShippingmethodController@store');
	$app->put('shippingmethods/{id}', 'ShippingmethodController@update');
	$app->delete('shippingmethods/{id}', 'ShippingmethodController@destroy');
	// TODO: Thumbnails ?
	// Shippingmethods
	$app->get('users', 'UserController@index');
	$app->get('users/{id}', 'UserController@show');
	$app->post('users', 'UserController@store');
	$app->put('users/{id}', 'UserController@update');
	$app->delete('users/{id}', 'UserController@destroy');
});

// Other, special, routes for logged in users
$app->group(['middleware' => ['auth']], function ($app) 
{
	/**
	 * Update the user password.
	 * TODO: Move to a controller action!
	 */
	$app->get('user/password/{uuid}', function ($uuid) 
	{
		$validator = Validator::make(Request::all(), [
      'newpassword' => 'required|confirmed|min:6',
      'oldpassword'  => 'required',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Password change',
        'messages'  => $messages
      ], 400);
    }

    // Old password must match logged in users current password
    $currentPassword = Auth::user()->password;
    if (!Hash::check(Request::input('oldpassword'), $currentPassword)) {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Password change',
        'messages'  => ['Current password doesn\'t match.']
      ], 400);
    }

    // Set new email
    $user = User::where('uuid', Auth::user()->uuid)->first();

    // If user doesn't exist... (shouldn't be possible here, but still...).
    if (!$user) {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Password change',
        'messages'  => ['The user was not found.']
      ], 400);
    }

    $user->password = Hash::make(Request::input('newpassword1'));
    $user->save();

    // Issue new email!

    return response()->json([
      'status'    => 200,
      'data'      => null,
      'heading'   => 'Password change',
      'messages'  => ['The password was changed']
    ], 200);
	});
	
	/**
	 * Route for updating user name
	 * TODO: Move to action
	 */
	$app->post('user/name/{uuid}', function ($uuid) 
	{
		$validator = Validator::make(Request::all(), [
      'firstname' => 'required|string',
      'lastname' => 'required|string',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Name change',
        'messages'  => $messages
      ], 400);
    }
    
    Auth::user()->firstname = Input::get('firstname');
    Auth::user()->lastname = Input::get('lastname');
    Auth::user()->save();
    
    return response()->json([
      'status'    => 200,
      'data'      => null,
      'heading'   => 'Name change',
      'messages'  => ['The name was changed']
    ], 200);
	});
	
	/**
	 * Get a generated user number
	 */
	$app->get('customers/number/{name}', ['as' => 'api.customers.number', function ($name)
	{
		return response()->json([
      'status'    => 200,
      'data'      => GoodtradeAdmin\CustomerHelper::createCustomerNumber($name),
      'heading'   => 'Customer number',
      'messages'  => null
    ], 200);
	}]);
	
	/**
	 * Special route for validating customers
	 * TODO: Move to action
	 */
	$app->post('customers/verify/', ['as' => 'api.customers.verify', function () 
	{
		$user = GoodtradeAdmin\User::where('uuid', Auth::user()->uuid)->first();
    $input = Request::except(['_token']);
    $updated = [];
    foreach ($input as $key => $value) {
      if ($value === 'on') {
        $customer = GoodtradeAdmin\Customer::where('uuid', $key)->first();
        $customer->number = GoodtradeAdmin\CustomerHelper::createCustomerNumber($customer->name);
        $customer->save();
        $customer->verifiedBy()->save($user);
        $update[] = [
          'uuid'  => $customer->uuid,
          'name'  => $customer->name,
        ];
      }
    }

    return response()->json([
      'status'    => 200,
      'data'      => $updated,
      'heading'   => 'Verified customers',
      'messages'  => $messages
    ], 200);
	}]);
	
});

$app->group(['middleware' => ['guest']], function ($app)
{
	/**
	 * Send a customer request.
	 * TODO: Move to action!
	 */
	$app->post('auth/request', function ()
	{
		$validator = Validator::make(Request::all(), [
      'name'          => 'required|string',
      'contact-email' => 'required|email',
      'corporate'     => 'required',
      'invoice-type-paper'  => 'required|integer',
      'invoice-type-email'  => 'required|integer',
      'invoice-email' => 'required|email',

      // Invoice address
      'invoice-street'  => 'required|string',
      'invoice-city'    => 'required|string',
      'invoice-zip'     => 'required|string',
      'invoice-country' => 'required|string',

      // Delivery address
      'delivery-street'   => 'required|string',
      'delivery-city'     => 'required|string',
      'delivery-zip'      => 'required|string',
      'delivery-country'  => 'required|string',

      // Connected nodes
      'chain'             => 'required|string',
      'customer-segment'  => 'required|string',

      // User validation
      'firstname'   => 'required|string',
      'lastname'    => 'required|string',
      'email'       => 'required|email|max:255',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Customerrequest',
        'messages'  => $messages
      ], 400);
    }

    $invoice_type_paper = 0;
    if (Request::has('invoice-type-paper')) {
      $invoice_type_paper = Request::input('invoice-type-paper');
    }

    $invoice_type_email = 0;
    if (Request::has('invoice-type-email')) {
      $invoice_type_email = Request::input('invoice-type-email');
    }
    
    $customerData = [
      'uuid'      => Rhumsaa\Uuid\Uuid::uuid4()->toString(),
      'name'      => Request::input('name'),
      'corporate' => Request::input('corporate'),
      'store_type'          => Request::input('store-type'),
      'invoice_type_paper'  => $invoice_type_paper,
      'invoice_type_email'  => $invoice_type_email,
    ];

    $customer = GoodtradeAdmin\Customer::create($customerData);

    // Link customer to chain
    $chain = GoodtradeAdmin\Chain::where('uuid', Request::input('chain'))->first();
    $rel = $chain->members()->save($customer);
    
    // Link customer to chain
    $segment = GoodtradeAdmin\Customersegment::where('uuid', Request::input('customer-segment'))->first();
    $rel = $segment->customers()->save($customer);
    

    $emailData = [
      'uuid'      => Rhumsaa\Uuid\Uuid::uuid4()->toString(),
      'address'   =>  Request::input('contact-email'),
    ];

    $email = GoodtradeAdmin\Email::create($emailData);
    $rel = $customer->emails()->save($email);

    $country = GoodtradeAdmin\Country::where('uuid', Request::input('invoice-country'))->first();

    $invoiceData = [
      'uuid'    => Rhumsaa\Uuid\Uuid::uuid4()->toString(),
      'street'  =>  Request::input('invoice-street'),
      'zip'     =>  Request::input('invoice-zip'),
      'city'    =>  Request::input('invoice-city'),
      'country' =>  Request::input('invoice-country'),
    ];

    $invoiceAddr = GoodtradeAdmin\Address::create($invoiceData);
    $customer->addresses()->save($invoiceAddr);
    $country->addresses()->save($invoiceAddr);
    
    $country = GoodtradeAdmin\Country::where('uuid', Request::input('delivery-country'))->first();

    $deliveryData = [
      'uuid'    => Rhumsaa\Uuid\Uuid::uuid4()->toString(),
      'street'  =>  Request::input('delivery-street'),
      'zip'     =>  Request::input('delivery-zip'),
      'city'    =>  Request::input('delivery-city'),
      'country' =>  Request::input('delivery-country'),
    ];

    $deliveryAddr = GoodtradeAdmin\Address::create($deliveryData);
    $country->addresses()->save($deliveryAddr);
    $customer->addresses()->save($deliveryAddr);

    $randomPw = str_random(8);
    $userData = [
      'uuid'      => Rhumsaa\Uuid\Uuid::uuid4()->toString(),
      'firstname' => Request::input('firstname'),
      'lastname'  => Request::input('lastname'),
      'email'     => Request::input('email'),

      // Extra data
      'note'      => '',

      'password'  => bcrypt($randomPw),
      'role'      => 'user',
      'verified'  => 0,
    ];

    $user = GoodtradeAdmin\User::create($userData);
    $rel = $customer->users()->save($user);

    $userData['password'] = $randomPw;
/*
    Mail::queue('emails.welcome', ['user' => $userData], function ($message) use ($userData) {
      $message->from(env('MAIL_DEFAULT_FROM', ''))
              ->to($user['email'])
              ->subject('Testing');
    });
*/

    return response()->json([
      'status'    => 200,
      'data'      => null,
      'heading'   => 'Customer request',
      'messages'  => ['The request was created']
    ], 200);
	});
});

/**
 * Auth routes!
 */
$app->group(['middleware' => ['guest']], function ($app)
{
	$app->post('login', 'AuthController@login');
	$app->post('register', 'AuthController@register');
});
