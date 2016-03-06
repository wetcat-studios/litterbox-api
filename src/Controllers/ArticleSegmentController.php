<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Segment;

use Ramsey\Uuid\Uuid;

class ArticleSegmentController extends Controller {


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
    
    $segment = $article->segment()->first();
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $segment,
    ], 200);
  }
  
  
  /**
   * Create a new segment, and automatically attach it to the article.
   */
  public function store (Request $request, $articleId)
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

    $segmentData = [
      'uuid'            =>  Uuid::uuid4()->toString(),
      'name'            =>  $request->input('name'),
      'sustainability'  =>  $request->input('sustainability'),
    ];

    $segment = Segment::create($segmentData);

    $rel = $segment->articles()->save($article);
    
    return response()->json([
      'status'  =>  201,
      'data'    =>  $segment,
    ], 201);
  }
  
  
  /**
   * Connect an article and a segment
   */
  public function update (Request $request, $articleId, $segmentId)
  {
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Article not found'],
      ], 404);
    }
    
    $segment = Segment::where('uuid', $segmentId)->first();
    
    if (!$segment) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Segment not found'],
      ], 404);
    }
    
    $rel = $segment->articles()->save($article);
    
    return response()->json([
      'status'    => 201,
      'messages'  => ['Connected article to segment']
    ], 201);
  }
  
  
  /**
   * Delete a relationship between an article and a segment
   */
  public function destroy ($articleId, $segmentId)
  {
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Article was not found'],
      ], 404);
    }
    
    $segment = Segment::where('uuid', $segmentId)->first();
    
    if (!$segment) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Segment was not found'],
      ], 404);
    }
    
    $rel = $article->segment()->edge($segment);
    
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
