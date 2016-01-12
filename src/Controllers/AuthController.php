<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Hash;
use Mail;

use Wetcat\Litterbox\Models\User;

use Ramsey\Uuid\Uuid;

use Wetcat\Litterbox\Auth\Auth as AuthHelper;

use Wetcat\Litterbox\Auth\Token as TokenHelper;

class AuthController extends Controller {

	/**
   * Attempt to login (POST). This will try to find a user with the 
   * given email and password, and then set a UUID token which will be 
   * returned to called as "token".
   *
   * This token should then be attached to all requests to the API as
   * "X-Auth-Token" in the header.
   */
	public function login (Request $request)
	{
		$validator = Validator::make($request->all(), [
      'email'   	=> 'required|email',
			'password'	=> 'required'
    ]);
		if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Login',
        'messages'  => $messages
      ], 400);
    }

    $user = AuthHelper::checkCredentials($request->input('email'), $request->input('password'));
    
	  if (!is_null($user)) {
			$composedToken = TokenHelper::composeToken($user);
      
      return response()->json([
        'status'    => 200,
        'data'      => [$composedToken],
        'heading'   => 'Login',
        'messages'  => ['Login successful']
      ], 200);
		} else {
			return response()->json([
        'status'    => 401,
        'data'      => null,
        'heading'   => 'Login',
        'messages'  => ['Bad login credentials.']
      ], 401);
		}
	}

  
  /**
   * Create a new user (POST)
   *
   * This will return the user, excluding the password, but will
   * include the token.
   */
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email'     => 'required|email',
      'firstname' => 'required|string',
      'lastname'  => 'required|string',
      'password'  => 'required|confirmed',
      'role'      => 'required|integer'
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Register',
        'messages'  => $messages
      ], 400);
    }

    $userData = [
      'uuid'      => Uuid::uuid4()->toString(),
      'email'     => $request->input('email'),
      'firstname' => $request->input('firstname'),
      'lastname'  => $request->input('lastname'),
      'password'  => bcrypt($request->input('password')),
      'token'     => Uuid::uuid1()->toString(),
      'role'      => $request->input('role')
    ];
    
    $user = User::create($userData);
    
    // User created!
    if (!!$user) {
      $data = [
        'email'     => $user->email,
        'firstname' => $user->firstname,
        'lastname'  => $user->lastname,
        'token'     => $user->token,
      ];
      return response()->json([
        'status'    => 201,
        'data'      => $data,
        'heading'   => 'Register',
        'messages'  => ['User registered successfully']
      ], 201);
    }
    
    // Something went horribly wrong...
    else {
      return response()->json([
        'status'    => 201,
        'data'      => null,
        'heading'   => 'Register',
        'messages'  => ['Failed to register new user.']
      ], 201);
    }
  }

  
  /**
   * Update a user password
   */
  public function password (Request $request)
  {
    $validator = Validator::make($request->all(), [
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
    if (!Hash::check($request->input('oldpassword'), $currentPassword)) {
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

    $user->password = Hash::make($request->input('newpassword1'));
    $user->save();

    // Issue new email!

    return response()->json([
      'status'    => 200,
      'data'      => null,
      'heading'   => 'Password change',
      'messages'  => ['The password was changed']
    ], 200);
  }
  
  
  /**
   * Update a user names
   * TODO: Remove this... should use User::update() instead!
   */
  public function name (Request $request)
  {
    $validator = Validator::make($request->all(), [
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
  }
  
  
  /**
   * Request a new password to be sent from the server
   */
  public function forgot (Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|string',
    ]);
    
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Password reset',
        'messages'  => $messages
      ], 400);
    }
    
    $user = User::where('email', $request->input('email'))->first();
    
    if (!!$user) {
      $resettoken = Uuid::uuid4()->toString();
      
      // Just set a random password
      $user->password = Hash::make(Uuid::uuid1()->toString());
      // Store the reset token
      $user->resettoken = $resettoken;
      $user->save();
      
      $msg = 'Din återställningskod är ' . $resettoken;
      
      // Send an email
      Mail::raw($msg, function ($emailmsg) use ($user) {
          $emailmsg->from('no-reply@goodtrade.se', 'Goodtrade AB');
          $emailmsg->to($user->email, $user->name)->subject('Ditt lösenord är återställt');
      });
      
      return response()->json([
        'status'    => 200,
        'data'      => null,
        'heading'   => 'Password reset',
        'messages'  => ['The password was reset, please check your email']
      ], 200);
    } else {
      return response()->json([
        'status'    => 404,
        'data'      => null,
        'heading'   => 'Password reset',
        'messages'  => ['User with that email does not exist']
      ], 400);
    }
  }
  
  
  /**
   * Create a new Customer request.
   */
  public function request (Request $request)
  {
    $validator = Validator::make($request->all(), [
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
    if ($request->has('invoice-type-paper')) {
      $invoice_type_paper = $request->input('invoice-type-paper');
    }

    $invoice_type_email = 0;
    if ($request->has('invoice-type-email')) {
      $invoice_type_email = $request->input('invoice-type-email');
    }
    
    $customerData = [
      'uuid'      => Uuid::uuid4()->toString(),
      'name'      => $request->input('name'),
      'corporate' => $request->input('corporate'),
      'store_type'          => $request->input('store-type'),
      'invoice_type_paper'  => $invoice_type_paper,
      'invoice_type_email'  => $invoice_type_email,
    ];

    $customer = GoodtradeAdmin\Customer::create($customerData);

    // Link customer to chain
    $chain = GoodtradeAdmin\Chain::where('uuid', $request->input('chain'))->first();
    $rel = $chain->members()->save($customer);
    
    // Link customer to chain
    $segment = GoodtradeAdmin\Customersegment::where('uuid', $request->input('customer-segment'))->first();
    $rel = $segment->customers()->save($customer);
    

    $emailData = [
      'uuid'      =>  Uuid::uuid4()->toString(),
      'address'   =>  $request->input('contact-email'),
    ];

    $email = GoodtradeAdmin\Email::create($emailData);
    $rel = $customer->emails()->save($email);

    $country = GoodtradeAdmin\Country::where('uuid', $request->input('invoice-country'))->first();

    $invoiceData = [
      'uuid'    =>  Uuid::uuid4()->toString(),
      'street'  =>  $request->input('invoice-street'),
      'zip'     =>  $request->input('invoice-zip'),
      'city'    =>  $request->input('invoice-city'),
      'country' =>  $request->input('invoice-country'),
    ];

    $invoiceAddr = GoodtradeAdmin\Address::create($invoiceData);
    $customer->addresses()->save($invoiceAddr);
    $country->addresses()->save($invoiceAddr);
    
    $country = GoodtradeAdmin\Country::where('uuid', $request->input('delivery-country'))->first();

    $deliveryData = [
      'uuid'    =>  Uuid::uuid4()->toString(),
      'street'  =>  $request->input('delivery-street'),
      'zip'     =>  $request->input('delivery-zip'),
      'city'    =>  $request->input('delivery-city'),
      'country' =>  $request->input('delivery-country'),
    ];

    $deliveryAddr = GoodtradeAdmin\Address::create($deliveryData);
    $country->addresses()->save($deliveryAddr);
    $customer->addresses()->save($deliveryAddr);

    $randomPw = str_random(8);
    $userData = [
      'uuid'      => Uuid::uuid4()->toString(),
      'firstname' => $request->input('firstname'),
      'lastname'  => $request->input('lastname'),
      'email'     => $request->input('email'),

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
  }
}
