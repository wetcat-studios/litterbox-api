<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Segment;

use Ramsey\Uuid\Uuid;

class UsersCustomerController extends Controller {

  /**
   * Instantiate a new UserController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request, $userUuid)
  {
    // Show all customers for a user
    $user = User::where('uuid', $userUuid)->first();
    
    if (!$user) {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => null,
        'messages'  => ['The user could not be found.']
      ], 400);
    }
    
    $customer = $user->customer();
    
    return response()->json([
      'status'    => 200,
      'data'      => $customer,
      'heading'   => null,
      'messages'  => null
    ], 200);
  }


  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show(Request $request, $id)
  {
    
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $uuid
   * @return Response
   */
  public function destroy($uuid)
  {
  }

}
