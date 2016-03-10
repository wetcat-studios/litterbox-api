<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Intrastat;

use Ramsey\Uuid\Uuid;

class IntrastatController extends Controller {


  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  public function index (Request $request)
  {
    $codes = Intrastat::all();
    
    return response()->json([
      'status'  => 200,
      'data'    => $codes->toArray(),
    ], 200);
  }


  public function show (Request $request, $codeId)
  {
    $code = Intrastat::where('uuid', $codeId)->first();

    if (!$code) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Intrastat code was not found'],
      ], 404);
    }
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $code,
    ], 200);
  }
  
  
  public function store (Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name'        =>  'string|required',
      'code'        =>  'string|required',
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

    $codeData = [
      'uuid'  =>  Uuid::uuid4()->toString(),
      'name'  =>  $request->input('name'),
      'code'  =>  $request->input('code'),
    ];

    $code = Intrastat::create($codeData);

    return response()->json([
      'status'  =>  201,
      'data'    =>  $code,
    ], 201);
  }
  
  
  public function update (Request $request, $codeId)
  {
    $validator = Validator::make($request->all(), [
      'name'        =>  'string',
      'code'        =>  'string',
    ]);
    
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    =>  400,
        'messages'  =>  $messages
      ], 400);
    }
    
    $code = Intrastat::where('uuid', $codeId)->first();
    
    if (!$code) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Intrastat code not found.']
      ], 404);
    }
    
    $updatedData = [];
    
    if ($request->has('name')) {
      $code->name = $request->input('name');
    }
    
    if ($request->has('code')) {
      $code->code = $request->input('code');
    }
    
    $updated = $code->update($updatedData);
    
    if ($updated) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['Updated intrastat code'],
      ], 200);
    } else {
      return response()->json([
        'status'    =>  400,
        'messages'  =>  ['Failed to update intrastat code'],
      ], 400);
    }
  }

  
  public function destroy ($codeId)
  {
    $code = Intrastat::where('uuid', $codeId)->first();

    if (!$code) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Intrastat code was not found'],
      ], 404);
    }
    
    $code->delete();

    return response()->json([
      'status'    =>  200,
      'messages'  =>  ["Intrastat code '$code->name' deleted"],
    ], 200);
  }

}
