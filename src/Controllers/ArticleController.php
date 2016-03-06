<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Category;
use Wetcat\Litterbox\Models\Brand;
use Wetcat\Litterbox\Models\Manufacturer;
use Wetcat\Litterbox\Models\Currency;
use Wetcat\Litterbox\Models\Picture;
use Wetcat\Litterbox\Models\Segment;
use Wetcat\Litterbox\Models\Ingredient;
use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\Intrastat;

use Ramsey\Uuid\Uuid;

class ArticleController extends Controller {


  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  public function index (Request $request)
  {
    $articles = Article::all();
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $articles->toArray(),
    ], 200);
  }


  public function show (Request $request, $articleId)
  {
    $article = Article::where('uuid', $articleId)->first();

    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Article was not found'],
      ], 404);
    }
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $article,
    ], 200);
  }
  
  
  public function store (Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name'              =>  'required|string',
      'articleNumber'     =>  'required|string',
      'ean'               =>  'required|string',
      'price'             =>  'required',
      'discountRate'      =>  'integer',
      'restockThreshold'  =>  'integer',
      'restockAmount'     =>  'integer',
      'filename'          =>  'string',
      'intrastat'         =>  'string',
      'description'       =>  'string',
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

    $article = Article::create([
      'uuid'              =>  Uuid::uuid4()->toString(),
      'name'              =>  $request->input('name'),
      'articleNumber'     =>  $request->input('articleNumber'),
      'ean'               =>  $request->input('ean'),
      'price'             =>  $request->input('price'),
      'discountRate'      =>  $request->input('discountRate'),
      'restockThreshold'  =>  $request->input('restockThreshold'),
      'restockAmount'     =>  $request->input('restockAmount'),
      'filename'          =>  $request->input('filename'),
      'intrastat'         =>  $request->input('intrastat'),
      'description'       =>  $request->input('description')
    ]);

    return response()->json([
      'status'    =>  201,
      'data'      =>  $article,
      'messages'  =>  null
    ], 201);
  }
  
  
  public function update (Request $request, $articleId)
  {
    $validator = Validator::make($request->all(), [
      'name'              =>  'string',
      'articleNumber'     =>  'string',
      'ean'               =>  'string',
      'price'             =>  'integer',
      'discountRate'      =>  'integer',
      'restockThreshold'  =>  'integer',
      'restockAmount'     =>  'integer',
      'filename'          =>  'string',
      'intrastat'         =>  'string',
      'description'       =>  'string',
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
    
    $article = Article::where('uuid', $articleId)->first();

    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Article was not found'],
      ], 404);
    }
    
    $updatedData = $request->only([
      'name',
      'articleNumber',
      'ean',
      'price',
      'discountRate',
      'restockThreshold',
      'restockAmount',
      'filename',
      'intrastat',
      'description',
    ]);
    
    $updated = $article->update($updatedData);
    
    if ($updated) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['Updated article'],
      ], 200);
    } else {
      return response()->json([
        'status'    =>  400,
        'messages'  =>  ['Failed to update article'],
      ], 400);
    }
  }
  
  
  public function destroy ($articleId)
  {
    $article = Article::where('uuid', $articleId)->first();

    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Article was not found'],
      ], 404);
    }
    
    $article->delete();

    return response()->json([
      'status'    =>  200,
      'messages'  =>  ["Article '$article->name' deleted"],
    ], 200);
  }

}
