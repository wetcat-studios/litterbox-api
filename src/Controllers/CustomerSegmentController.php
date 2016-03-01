<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Customersegment;

use Ramsey\Uuid\Uuid;

class CustomerSegmentController extends Controller {

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
  public function index(Request $request)
  {
    $segments = [];
    
    // Default limit per request
    $limit = 10;
    
    // ...but if there's a set limit we'll follow that
    if ($request->has('limit')) {
      $limit = $request->input('limit');
    }
    
    // Attach relations
    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $q = Customersegment::with($rels);
    } else {
      $q = Customersegment::with([]);
    }
    
    // Do filtering
    if ($request->has('name')) {
      $q->where('name', $request->input('name'));
    }

    $segments = $q->paginate($limit);
    
    return response()->json([
      'status'    => 200,
      'data'      => $segments->toArray(),
      'heading'   => null,
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
      'name'    => 'required|string',
    ]);

    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Customer segment',
        'messages'  => $messages
      ], 400);
    }

    $segmentData = [
      'uuid'  => Uuid::uuid4()->toString(),
      'name' => $request->input('name')
    ];

    $segment = Customersegment::create($segmentData);

    return response()->json([
      'status'    => 201,
      'data'      => $segment,
      'heading'   => 'Customer segment',
      'message'   => ['Customer segment created'],
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
      $customersegment = Customersegment::with($rels)->where('uuid', $id)->get();
    } else {
      $customersegment = Customersegment::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $customersegment,
      'heading'   => 'Customersegment',
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
  public function update(Request $request, $uuid)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'string',
    ]);
    
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Customersegment',
        'messages'  => $messages
      ], 400);
    }
    
    $segment = Customersegment::where('uuid', $uuid)->first();
    
    if (!!$segment) {
      
      if ($request->has('name')) {
        $segment->name = $request->input('name');
      }
      
      $segment->save();
      
    } else {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Customersegment',
        'messages'  => ['Customersegment not found.']
      ], 400);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($uuid)
  {
    $segment = Customersegment::where('uuid', $uuid)->first();

    $segment->delete();

    return response()->json([
      'status'    => 200,
      'data'      => $segment,
      'heading'   => 'Customersegment',
      'messages'  => ['Customersegment ' . $segment->name . ' deleted.']
    ], 200); 
  }

}
