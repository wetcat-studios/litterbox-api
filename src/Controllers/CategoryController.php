<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Category;

use Ramsey\Uuid\Uuid;

class CategoryController extends Controller {


  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  public function index (Request $request)
  {
    $categories = Category::all();
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $categories->toArray(),
    ], 200);
  }


  public function show (Request $request, $categoryId)
  {
    $category = Category::where('uuid', $categoryId)->first();

    if (!$category) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Category was not found'],
      ], 404);
    }
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $category,
    ], 200);
  }
  
  
  public function store (Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name'              =>  'required|string',
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

    $category = Category::create([
      'uuid'  =>  Uuid::uuid4()->toString(),
      'name'  =>  $request->input('name'),
    ]);

    return response()->json([
      'status'    =>  201,
      'data'      =>  $category,
      'messages'  =>  null
    ], 201);
  }
  
  
  public function update (Request $request, $categoryId)
  {
    $validator = Validator::make($request->all(), [
      'name'              =>  'string',
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
    
    $category = Category::where('uuid', $categoryId)->first();

    if (!$category) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Category was not found'],
      ], 404);
    }
    
    $updatedData = [];
    
    if ($request->has('name')) {
      $updatedData['name'] = $request->input('name');
    }
    
    if (count($updatedData) == 0) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['No updated data'],
      ], 200);
    }
    
    $updated = $category->update($updatedData);
    
    if ($updated) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['Updated category'],
      ], 200);
    } else {
      return response()->json([
        'status'    =>  400,
        'messages'  =>  ['Failed to update category'],
      ], 400);
    }
  }
  
  
  public function destroy ($categoryId)
  {
    $category = Category::where('uuid', $categoryId)->first();

    if (!$category) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Category was not found'],
      ], 404);
    }
    
    $category->delete();

    return response()->json([
      'status'    =>  200,
      'messages'  =>  ["Category '$category->name' deleted"],
    ], 200);
  }

}
