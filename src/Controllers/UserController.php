<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Mail;
use Auth;
use Hash;

use Wetcat\Litterbox\Models\User;
use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\Manufacturer;
use Wetcat\Litterbox\Models\Phone;
use Wetcat\Litterbox\Models\Segment;

use Wetcat\Litterbox\Auth\Roles as RoleHelper;
use Wetcat\Litterbox\Auth\Auth as AuthHelper;

use Ramsey\Uuid\Uuid;

class UserController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $users = [];

    if ($request->has('rel')) {
      $users = User::with($rels)->get();
    } else {
      $users = User::all();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $users,
      'heading'   => 'User',
      'messages'  => null
    ], 200);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      // User data
      'firstname' => 'required|max:255',
      'lastname'  => 'required|max:255',
      'email'     => 'required|email|max:255|unique:users',
      'role'      => 'required',

      // Extra data
      'note'      => 'string',

      // Owner node (that is, the node to link this user to)
      'owner'       => 'string', // UUID
      'ownertype'   => 'string', // Node type

      // Segment node (optional)
      'segment' => 'string', // UUID

      'autoverify' => 'boolean', // Used to determine if we should automverify the user
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'User',
        'messages'  => $messages
      ], 400);
    }
    
    // Make sure a user with the email doesn't exist!
    $emailLookup = User::where('email', $request->input('email'))->first();
    if (!!$emailLookup) {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'User',
        'messages'  => ['A user with that E-Mail already exists']
      ], 400);
    }

    $randomPw = str_random(8);

    $userData = [
      'uuid'      => Uuid::uuid4()->toString(),
      'firstname' => $request->input('firstname'),
      'lastname'  => $request->input('lastname'),
      'email'     => $request->input('email'),

      // Extra data
      'note'      => $request->input('note'),

      'password'  => Hash::make($randomPw),
      'role'      => ( is_numeric($request->input('role')) ? intval($request->input('role')) : RoleHelper::getRoleValue($request->input('role')) )
    ];

    $user = User::create($userData);

    $messages = [];

    if ($request->has('owner') && $request->has('ownertype') && Uuid::isValid($request->input('owner'))) {
      switch ($request->input('ownertype')) {
        case 'customer':
          $customer = Customer::where('uuid', $request->input('owner'))->first();
          $rel = $customer->users()->save($user);
          $messages[] = 'User was added to the customer';
          break;
        
        case 'manufacturer':
          $manufacturer = Manufacturer::where('uuid', $request->input('owner'))->first();
          $rel = $manufacturer->users()->save($user);
          $messages[] = 'User was added to the manufacturer';
          break;

        case 'segment':
          $segment = Segment::where('uuid', $request->input('owner'))->first();
          $rel = $segment->users()->save($user);
          $messages[] = 'User was added to the segment';
          break;
      }
    }

    // TODO: Automatically create the phone number for the user. For now we'll let the users 
    // create the phone numbers themselves from their profile page.

    // Also attach the password to the email (overwrite the hashed pw, we don't need that anymore)
    $userData['password'] = $randomPw;
    
    $msg = 'En ny användare har skapats i ditt namn, ditt lösenord är [ ' . $randomPw . ' ], du kan ändra detta lösenord första gången du loggar in.';
    
    // Send an email
    Mail::raw($msg, function ($emailmsg) use ($user) {
        $emailmsg->from('no-reply@goodtrade.se', 'Goodtrade AB');
        $emailmsg->to($user->email, $user->name)->subject('Välkommen');
    });
    
    // We made it! Send a success!
    return response()->json([
      'status'    => 201,
      'data'      => $user,
      'heading'   => 'User',
      'messages'  => $messages
    ], 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id, Request $request)
  {
    if ($request->has('rel')) {
      $user = User::with($rels)->where('uuid', $id)->get();
    } else {
      $user = User::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $user,
      'heading'   => 'User',
      'messages'  => null
    ], 200);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request, $id)
  {
    // Make sure the user is either superadmin or editing same user as is logged in
    $token = $request->header('X-Litterbox-Token');
    $currentUser = AuthHelper::getUser($token);
    $userIsAdmin = RoleHelper::verify($currentUser->role, 'admin');
    
    // Get the selected user
    $user = User::where('uuid', $id)->first();
    $isCurrentUser = (strcmp($user->uuid, $id) == 0);
    
    if (!$isCurrentUser && !$userIsAdmin) {
      return response()->json([
        'status'    => 401,
        'data'      => null,
        'heading'   => 'User',
        'messages'  => ['You don\'t have permission to change this users data.']
      ], 401);
    }
    
    $messages = ['Updated attributes'];
    
    // Ignore password and email
    //$updatedData = [];
    foreach ($request->except('password', 'email') as $key => $value) {
      //$updatedData[$key] = $value;
      $user->$key = $value;
      $messages[] = $key;
    }
    //$user->update($updatedData);
    $user->save();
    
    return response()->json([
      'status'    => 200,
      'data'      => $user,
      'heading'   => 'User',
      'messages'  => $messages
    ], 200);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
    $me = User::where('uuid', Auth::user()->uuid)->first();

    $user = User::where('uuid', $id)->first();

    if (!!$user && !!$me) {

      // You can't delete yourself
      if ($me->uuid === $user->uuid){
        return response()->json([
          'status'    => 400,
          'data'      => $user,
          'heading'   => 'User',
          'message'   => ['You can\'t delete yourself!'],
        ], 400);
      }

      // Super admins can't be deleted
      if ($user->role === 'superadmin') {
        return response()->json([
          'status'    => 400,
          'data'      => $user,
          'heading'   => 'User',
          'message'   => ['Super admins can\'t be deleted!'],
        ], 400);
      }

      // If target is admin you need to be superadmin
      if ($user->role === 'admin' && $me->role !== 'superadmin') {
        return response()->json([
          'status'    => 400,
          'data'      => $user,
          'heading'   => 'User',
          'message'   => ['You need to be super admin to delete an admin!'],
        ], 400);
      }

      // If we go here we can delete the user!
      $user->delete();  

      return response()->json([
        'status'    => 200,
        'data'      => $user,
        'heading'   => 'User',
        'message'   => ['User was deleted.'],
      ], 200);
    }
  }

}
