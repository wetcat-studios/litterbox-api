<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Chain;

use Ramsey\Uuid\Uuid;

class ChainController extends Controller {


  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  public function index (Request $request)
  {
    $chains = Chain::all();
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $chains->toArray(),
    ], 200);
  }


  public function show (Request $request, $chainId)
  {
    $chain = Chain::where('uuid', $chainId)->first();

    if (!$chain) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Chain was not found'],
      ], 404);
    }
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $chain,
    ], 200);
  }
  
  
  public function store (Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name'      =>  'string|required',
      'corporate' =>  'string',
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

    $chainData = [
      'uuid'      =>  Uuid::uuid4()->toString(),
      'name'      =>  $request->input('name'),
      'corporate' =>  $request->input('corporate'),
    ];
    
    $chain = Chain::create($chainData);

    return response()->json([
      'status'    =>  201,
      'data'      =>  $chain,
    ], 201);
  }
  
  
  public function update (Request $request, $chainId)
  {
    $validator = Validator::make($request->all(), [
      'name'      =>  'string',
      'corporate' =>  'string',
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
    
    $chain = Chain::where('uuid', $chainId)->first();

    if (!$chain) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Category was not found'],
      ], 404);
    }
    
    $updatedData = [];
    
    if ($request->has('name')) {
      $updatedData['name'] = $request->input('name');
    }
    
    if ($request->has('corporate')) {
      $updatedData['corporate'] = $request->input('corporate');
    }
    
    if (count($updatedData) == 0) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['No updated data'],
      ], 200);
    }
    
    $updated = $chain->update($updatedData);
    
    if ($updated) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['Updated chain'],
      ], 200);
    } else {
      return response()->json([
        'status'    =>  400,
        'messages'  =>  ['Failed to update chain'],
      ], 400);
    }
  }
  
  
  public function destroy ($chainId)
  {
    $chain = Chain::where('uuid', $chainId)->first();

    if (!$chain) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Chain was not found'],
      ], 404);
    }
    
    $chain->delete();

    return response()->json([
      'status'    =>  200,
      'messages'  =>  ["Chain '$chain->name' deleted"],
    ], 200);
  }

}
