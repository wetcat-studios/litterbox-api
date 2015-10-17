<?php

// API
Route::group(['middleware' => 'litterbox-auth'], function ()
{
  Route::resource('addresses', 'Wetcat\Litterbox\Controllers\AddressController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('articles', 'Wetcat\Litterbox\Controllers\ArticleController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('batches', 'Wetcat\Litterbox\Controllers\BatchController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('brands', 'Wetcat\Litterbox\Controllers\BrandController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('campaigns', 'Wetcat\Litterbox\Controllers\CampaignController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('categories', 'Wetcat\Litterbox\Controllers\CategoryController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('chains', 'Wetcat\Litterbox\Controllers\ChainController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('chainsegments', 'Wetcat\Litterbox\Controllers\ChainSegmentController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('cities', 'Wetcat\Litterbox\Controllers\CityController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('countries', 'Wetcat\Litterbox\Controllers\CountryController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('counties', 'Wetcat\Litterbox\Controllers\CountyController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('currencies', 'Wetcat\Litterbox\Controllers\CurrencyController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('customers', 'Wetcat\Litterbox\Controllers\CustomerController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('customersegments', 'Wetcat\Litterbox\Controllers\CustomerSegmentController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('emails', 'Wetcat\Litterbox\Controllers\EmailController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('ingredients', 'Wetcat\Litterbox\Controllers\IngredientController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('manufacturers', 'Wetcat\Litterbox\Controllers\ManufacturerController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('orders', 'Wetcat\Litterbox\Controllers\OrderController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('phones', 'Wetcat\Litterbox\Controllers\PhoneController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('pictures', 'Wetcat\Litterbox\Controllers\PictureController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('rates', 'Wetcat\Litterbox\Controllers\RateController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('restock', 'Wetcat\Litterbox\Controllers\RestockController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('segments', 'Wetcat\Litterbox\Controllers\SegmentController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('shippingmethods', 'Wetcat\Litterbox\Controllers\ShippingmethodController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
  Route::resource('users', 'Wetcat\Litterbox\Controllers\UserController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);

  // Special route for updating password
  Route::post('user/password/{uuid}', function ($uuid) {
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

  // Special route for updating user names
  Route::post('user/name/{uuid}', function ($uuid) {
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


  Route::get('test', function () {
    $customers = GoodtradeAdmin\Customer::with('verifiedBy')->get();
    $out = [];
    foreach ($customers as $value) {
      $customer = $value->toArray();
      if ($customer['verified_by'] === null) {
        $out[] = $value;
      }
    }
    return $out;
  });

  Route::get('customers/number/{name}', ['as' => 'api.customers.number', function ($name){
    return response()->json([
      'status'    => 200,
      'data'      => GoodtradeAdmin\CustomerHelper::createCustomerNumber($name),
      'heading'   => 'Customer number',
      'messages'  => null
    ], 200);
  }]);

  // Special route for validating customers
  Route::post('customers/verify', ['as' => 'api.customers.verify', function () {
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

Route::group(['middleware' => ['guest']], function ()
{
  /**
   * Create a new customer and user, but invalidate them!
   */
  Route::post('auth/request', function () {
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