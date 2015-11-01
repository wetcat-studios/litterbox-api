<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Thumbnail;

use Ramsey\Uuid\Uuid;

class ThumbnailController extends Controller {

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

    // Try finding by parent node
    $user = User::where('uuid', $request->input('owner'))->first();
    $customer = Customer::where('uuid', $request->input('owner'))->first();
    $manufacturer = Manufacturer::where('uuid', $request->input('owner'))->first();

    $messages = [];

    // Find the correct type of node!
    if (!!$user && $user->exists) {
      $rel = $user->emails()->save($email);
      $messages[] = 'Email was added to the user';
    } else if (!!$customer && $customer->exists) {
      $rel = $customer->emails()->save($email);
      $messages[] = 'Email was added to the customer';
    } else if (!!$manufacturer && $manufacturer->exists) {
      $rel = $manufacturer->emails()->save($email);
      $messages[] = 'Email was added to the manufacturer';
    }

    // Or if it was not one of the three accepted node types we'll simply send an error!
    else {
      // Delete the email (no need to keep it in db!)
      $email->forceDelete();

      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Email',
        'messages'  => ["Invalid node type, can't attach the email."]
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
  public function show(Request $request, $id)
  {
    if ($request->has('rel')) {
      $thumbnail = Thumbnail::with($rels)->where('uuid', $id)->get();
    } else {
      $thumbnail = Thumbnail::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $thumbnail,
      'heading'   => 'Thumbnail',
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
