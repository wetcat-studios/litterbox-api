<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Manufacturer;

use Ramsey\Uuid\Uuid;

class ManufacturerController extends Controller {


  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  public function index (Request $request)
  {
    $manufacturers = Manufacturer::all();
    
    return response()->json([
      'status'    =>  200,
      'data'      =>  $manufacturers->toArray(),
    ], 200);
  }
  
  
  public function show (Request $request, $id)
  {
    if ($request->has('rel')) {
      $manufacturer = Manufacturer::with($rels)->where('uuid', $id)->get();
    } else {
      $manufacturer = Manufacturer::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $manufacturer,
      'heading'   => 'Manufacturer',
      'messages'  => null
    ], 200);
  }
  
  
  public function store (Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name'      =>  'string|required',
      'rebate'    =>  'integer',
      'shipping'  =>  'integer',
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

    $manufacturerData = [
      'uuid'      =>  Uuid::uuid4()->toString(),
      'name'      =>  $request->input('name'),
      'rebate'    =>  $request->input('rebate'),
      'shipping'  =>  $request->input('shipping'),
    ];

    $manufacturer = Manufacturer::create($manufacturerData);

    return response()->json([
      'status'  =>  201,
      'data'    =>  $manufacturer,
    ], 201);
  }
  
  
  public function update (Request $request, $manufacturerId)
  {
    $validator = Validator::make($request->all(), [
      'name'      =>  'string',
      'rebate'    =>  'integer',
      'shipping'  =>  'integer',
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
    
    $manufacturer = Manufacturer::where('uuid', $manufacturerId)->first();

    if (!$manufacturer) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Manufacturer was not found'],
      ], 404);
    }
    
    $updatedData = $request->only([
      'name',
      'rebate',
      'shipping',
    ]);
    
    $updated = $manufacturer->update($updatedData);
    
    if ($updated) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['Updated manufacturer'],
      ], 200);
    } else {
      return response()->json([
        'status'    =>  400,
        'messages'  =>  ['Failed to update manufacturer'],
      ], 400);
    }
  }
  
  
  public function destroy ($manufacturerId)
  {
    $manufacturer = Manufacturer::where('uuid', $manufacturerId)->first();

    if (!$manufacturer) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Manufacturer was not found'],
      ], 404);
    }
    
    $manufacturer->delete();

    return response()->json([
      'status'    =>  200,
      'messages'  =>  ["Manufacturer '$manufacturer->name' deleted"],
    ], 200);
  }

}
