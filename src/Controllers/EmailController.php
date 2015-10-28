<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Email;
use Wetcat\Litterbox\Models\User;
use Wetcat\Litterbox\Models\Manufacturer;
use Wetcat\Litterbox\Models\Customer;

use Ramsey\Uuid\Uuid;

class EmailController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $emails = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $emails = Email::with($rels)->get();
    } else {
      $emails = Email::all();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $emails,
      'heading'   => 'Email',
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
      // Address data
      'address' => 'required',
      'type'    => 'required',
      
      // The owner node
      'owner'   => 'required|string',
      'ownertype' => 'required|string' // Name of type
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Email',
        'messages'  => $messages
      ], 400);
    }

    $uuid4 = Uuid::uuid4();
    $emailData = [
      'uuid'    => $uuid4->toString(),
      'address' => $request->input('address'),
      'type'    => $request->input('type'),
    ];

    $email = Email::create($emailData);

    if ($request->has('owner') && $request->has('ownertype') && Uuid::isValid($request->input('owner'))) {
      switch ($request->input('ownertype')) {
        case 'user':
          $user = User::where('uuid', $request->input('owner'))->first();
          $rel = $user->emails()->save($email);
          $messages[] = 'Email was added to the user';
          break;

        case 'customer':
          $customer = Customer::where('uuid', $request->input('owner'))->first();
          $rel = $customer->emails()->save($email);
          $messages[] = 'Email was added to the customer';
          break;

        case 'manufacturer':
          $manufacturer = Manufacturer::where('uuid', $request->input('owner'))->first();
          $rel = $manufacturer->emails()->save($email);
          $messages[] = 'Email was added to the manufacturer';
          break;
      }
    }

    // Or if it was not one of the three accepted node types we'll simply send an error!
    else {
      // Remove the address if no owner node was found
      $email->forceDelete();

      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Address',
        'messages'  => ["Invalid node type, can't attach an email."]
      ], 400);
    }

    // We made it! Send a success!
    return response()->json([
      'status'    => 201,
      'data'      => $email,
      'heading'   => 'Email',
      'messages'  => $messages
    ], 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    //
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
  public function update($id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
    //
  }

}
