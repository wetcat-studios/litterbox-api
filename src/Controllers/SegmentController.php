<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Segment;

use Ramsey\Uuid\Uuid;

class SegmentController extends Controller {


  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  public function index (Request $request)
  {
    $segments = Segment::all();
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $segments->toArray(),
    ], 200);
  }


  public function show (Request $request, $segmentId)
  {
    $segment = Segment::where('uuid', $segmentId)->first();

    if (!$segment) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Segment was not found'],
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
      'sustainability'  =>  'integer|required',
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

    $segment = Segment::create([
      'uuid'            =>  Uuid::uuid4()->toString(),
      'name'            =>  $request->input('name'),
      'sustainability'  =>  $request->input('sustainability'),
    ]);

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
      'sustainability'  =>  'integer',
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
    
    $segment = Segment::where('uuid', $segmentId)->first();

    if (!$segment) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Segment was not found'],
      ], 404);
    }
    
    $updatedData = [];
    
    if ($request->has('name')) {
      $updatedData['name'] = $request->input('name');
    }
    
    if ($request->has('sustainability')) {
      $updatedData['sustainability'] = $request->input('sustainability');
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
    $segment = Segment::where('uuid', $segmentId)->first();

    if (!$segment) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Segment was not found'],
      ], 404);
    }
    
    $segment->delete();

    return response()->json([
      'status'    =>  200,
      'messages'  =>  ["Segment '$segment->name' deleted"],
    ], 200);
  }

}
