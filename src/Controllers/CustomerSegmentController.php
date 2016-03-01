<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Chain;
use Wetcat\Litterbox\Models\Chainsegment;

use Ramsey\Uuid\Uuid;

class ChainSegmentController extends Controller {

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
      $q = ChainSegment::with($rels);
    } else {
      $q = ChainSegment::with([]);
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

      // The chain to connect to (uuid)
      'chain'   => 'required|string'
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'  => 400,
        'data'    => null,
        'heading'   => 'Chain segment',
        'messages' => $messages
      ], 400);
    }

    $uuid4 = Uuid::uuid4();

    $segmentData = [
      'uuid'  => $uuid4->toString(),
      'name' => $request->input('name')
    ];

    $segment = Chainsegment::create($segmentData);

    // Connect to the chain
    $chain = Chain::where('uuid', $request->input('chain'))->first();
    $rel = $chain->segments()->save($segment);

    return response()->json([
      'status'  => 201,
      'data'    => $segment,
      'heading'   => 'Chain segment',
      'message' => ['Chain segment created'],
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
      $chain = Chain::with($rels)->where('uuid', $id)->get();
    } else {
      $chain = Chain::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $chain,
      'heading'   => 'Chain',
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
