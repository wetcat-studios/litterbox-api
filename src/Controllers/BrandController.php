<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Brand;
use Wetcat\Litterbox\Models\Picture;

use Ramsey\Uuid\Uuid;

class BrandController extends Controller {


  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  public function index (Request $request)
  {
    $brands = Brand::all();
    
    return response()->json([
      'status'  => 200,
      'data'    => $brands->toArray(),
    ], 200);
  }


  public function show (Request $request, $brandId)
  {
    $brand = Brand::where('uuid', $brandId)->first();

    if (!$brand) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Brand was not found'],
      ], 404);
    }
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $brand,
    ], 200);
  }
  
  
  public function store (Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name'        =>  'string|required',
      'url'         =>  'string',
      'description' =>  'string',
      'filename'    =>  'string',
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

    $brandData = [
      'uuid'  =>  Uuid::uuid4()->toString(),
      'name'  =>  $request->input('name'),
    ];

    if ($request->has('url'))
      $brandData['url'] = $request->input('url');

    if ($request->has('description'))
      $brandData['description'] = $request->input('description');

    $brand = Brand::create($brandData);

    return response()->json([
      'status'  =>  201,
      'data'    =>  $brand,
    ], 201);
  }
  
  
  public function update (Request $request, $brandId)
  {
    $validator = Validator::make($request->all(), [
      'name'        =>  'string',
      'url'         =>  'string',
      'description' =>  'string',
      'filename'    =>  'string',
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
    
    $brand = Brand::where('uuid', $brandId)->first();
    
    if (!$brand) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Brand not found.']
      ], 404);
    }
    
    $updatedData = $request->only([
      'name',
      'url',
      'description',
      'filename'
    ]);
    
    $updated = $brand->update($updatedData);
    
    if ($updated) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['Updated brand'],
      ], 200);
    } else {
      return response()->json([
        'status'    =>  400,
        'messages'  =>  ['Failed to update brand'],
      ], 400);
    }
  }

  
  public function destroy ($brandId)
  {
    $brand = Brand::where('uuid', $brandId)->first();

    if (!$brand) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Brand was not found'],
      ], 404);
    }
    
    $brand->delete();

    return response()->json([
      'status'    =>  200,
      'messages'  =>  ["Brand '$brand->name' deleted"],
    ], 200);
  }

}
