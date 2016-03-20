<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Manufacturer;

use Ramsey\Uuid\Uuid;

class ArticleManufacturerController extends Controller {


  /**
   * Attach authentication to the actions
   */
  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  /**
   * Show the manfacturers for the Article.
   */
  public function index (Request $request, $articleId)
  {
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['The article was not found'],
      ], 404);
    }
    
    $manufacturer = $article->manufacturer()->first();
    
    $out = $manufacturer->toArray();
    $out['manufacturerNumber'] = $manufacturer->articles()->edge($article)->manufacturerNumber;
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $out,
    ], 200);
  }
  
  
  /**
   * Create a new manufacturer, and automatically attach it to the article.
   */
  public function store (Request $request, $articleId)
  {
    $validator = Validator::make($request->all(), [
      'name'                =>  'string|required',
      'shipping'            =>  'integer|required',
      'rebate'              =>  'integer',
      'manufacturerNumber'  =>  'string|required',
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
    
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    => 404,
        'data'      => null,
        'messages'  => ['The article was not found']
      ], 404);
    }

    $manufacturerData = [
      'uuid'      =>  Uuid::uuid4()->toString(),
      'name'      =>  $request->input('name'),
      'rebate'    =>  $request->input('rebate'),
      'shipping'  =>  $request->input('shipping'),
    ];

    $manufacturer = Manufacturer::create($manufacturerData);

    $rel = $manufacturer->articles()->save($article);
    $rel->manufacturerNumber = $request->input('manufacturerNumber');
    $rel->save();
    
    return response()->json([
      'status'  =>  201,
      'data'    =>  $manufacturer,
    ], 201);
  }
  
  
  /**
   * Connect an article and a manufacturer
   */
  public function update (Request $request, $articleId, $manufacturerId)
  {
    $validator = Validator::make($request->all(), [
      'manufacturerNumber'  =>  'string|required',
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
    
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Article not found'],
      ], 404);
    }
    
    $manufacturer = Manufacturer::where('uuid', $manufacturerId)->first();
    
    if (!$manufacturer) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Manufacturer not found'],
      ], 404);
    }
    
    $rel = $manufacturer->articles()->save($article);
    $rel->manufacturerNumber = $request->input('manufacturerNumber');
    $rel->save();
    
    return response()->json([
      'status'    => 201,
      'messages'  => ['Connected article to manufacturer']
    ], 201);
  }
  
  
  /**
   * Delete a relationship between an article and a manufacturer
   */
  public function destroy ($articleId, $manufacturerId)
  {
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Article was not found'],
      ], 404);
    }
    
    $manufacturer = Manufacturer::where('uuid', $manufacturerId)->first();
    
    if (!$manufacturer) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Manufacturer was not found'],
      ], 404);
    }
    
    $rel = $article->manufacturer()->edge($manufacturer);
    
    if (!$rel) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['No previous relation found'],
      ], 404);
    }
    
    $result = $rel->delete();
    
    if ($result) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['Relationship deleted'],
      ], 200);
    } else {
      return response()->json([
        'status'    =>  400,
        'messages'  =>  ['Failed to delete relationship'],
      ], 400);
    }
  }

}
