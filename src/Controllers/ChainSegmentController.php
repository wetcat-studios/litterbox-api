<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Chainsegment;

use Ramsey\Uuid\Uuid;

class ChainSegmentController extends Controller {


  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  public function index (Request $request)
  {
    $segments = Chainsegment::all();
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $segments->toArray(),
    ], 200);
  }


  public function show (Request $request, $segmentId)
  {
    $segment = Chainsegment::where('uuid', $segmentId)->first();

    if (!$segment) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Chain segment was not found'],
      ], 404);
    }
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $segment,
    ], 200);
  }
  
  
  public function store (Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name'            =>  'string|required',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    =>  400,
        'messages'  =>  $messages,
      ], 400);
    }

    $segmentData = [
      'uuid'            =>  Uuid::uuid4()->toString(),
      'name'            =>  $request->input('name'),
    ];
    
    $segment = Chainsegment::create($segmentData);

    return response()->json([
      'status'    =>  201,
      'data'      =>  $segment,
      'messages'  =>  null
    ], 201);
  }
  
  
  public function update (Request $request, $segmentId)
  {
    $validator = Validator::make($request->all(), [
      'name'            =>  'string',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'messages'  => $messages
      ], 400);
    }
    
    $segment = Chainsegment::where('uuid', $segmentId)->first();

    if (!$segment) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Chain segment was not found'],
      ], 404);
    }
    
    $updatedData = [];
    
    if ($request->has('name')) {
      $updatedData['name'] = $request->input('name');
    }
    
    if (count($updatedData) == 0) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['No data submitted'],
      ], 200);
    }
    
    $updated = $segment->update($updatedData);
    
    if ($updated) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['Updated segment'],
      ], 200);
    } else {
      return response()->json([
        'status'    =>  400,
        'messages'  =>  ['Failed to update segment'],
      ], 400);
    }
  }
  
  
  public function destroy ($segmentId)
  {
    $segment = Chainsegment::where('uuid', $segmentId)->first();

    if (!$segment) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Chain segment was not found'],
      ], 404);
    }
    
    $segment->delete();

    return response()->json([
      'status'    =>  200,
      'messages'  =>  ["Chain segment '$segment->name' deleted"],
    ], 200);
  }

}
