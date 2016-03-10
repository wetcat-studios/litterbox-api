<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Intrastat;

use Ramsey\Uuid\Uuid;

class ArticleIntrastatController extends Controller {


  public function __construct ()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  /**
   * Show the intrastat code for the Article.
   */
  public function index (Request $request, $articleId)
  {
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['The article was not found']
      ], 404);
    }
    
    $code = $article->intrastat()->first();
    
    return response()->json([
      'status'    => 200,
      'data'      => $code,
    ], 200);
  }
  
  
  /**
   * Create a new intrastat code, and automatically attach it to the article.
   */
  public function store (Request $request, $articleId)
  {
    $validator = Validator::make($request->all(), [
      'name'  => 'string|required',
      'code'  => 'string|required',
    ]);

    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'messages'  => $messages
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
    
    $codeData = [
      'uuid'  =>  Uuid::uuid4()->toString(),
      'code'  =>  $request->input('code'),
    ];

    $code = Intrastat::create($codeData);
    
    $rel = $code->articles()->save($article);

    return response()->json([
      'status'    => 201,
      'data'      => $code,
    ], 201);
  }
  
  
  /**
   * Connect an article and an intrastat code
   */
  public function update ($articleId, $codeId)
  {
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Article not found'],
      ], 404);
    }
    
    $code = Intrastat::where('uuid', $codeId)->first();
    
    if (!$code) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Intrastat code not found'],
      ], 404);
    }
    
    $rel = $code->articles()->save($article);
    
    return response()->json([
      'status'    => 201,
      'messages'  => ['Connected article to intrastat code']
    ], 201);
  }

  /**
   * Delete a relationship between an article and a intrastat code.
   */
  public function destroy ($articleId, $codeId)
  {
    $code = Intrastat::where('uuid', $codeId)->first();
    
    if (!$code) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Intrastat code was not found'],
      ], 404);
    }
    
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Article was not found'],
      ], 404);
    }
    
    $rel = $code->articles()->edge($article);
    
    if (!$rel) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['No previous relation found'],
      ], 404);
    }
    
    $result = $rel->delete();
    
    if ($result) {
      return response()->json([
        'status'    => 200,
        'messages'  => ['Relationship deleted'],
      ], 200);
    } else {
      return response()->json([
        'status'    => 400,
        'messages'  => ['Failed to delete relationship'],
      ], 400);
    }
  }

}
