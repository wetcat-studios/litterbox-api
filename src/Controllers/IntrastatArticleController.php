<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Intrastat;

use Ramsey\Uuid\Uuid;

class IntrastatArticleController extends Controller {


  public function __construct ()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  /**
   * Show the articles for the intrastat code.
   */
  public function index (Request $request, $codeId)
  {
    $code = Intrastat::where('uuid', $codeId)->first();
    
    if (!$code) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['The intrastat code was not found']
      ], 404);
    }
    
    $articles = $code->articles()->get();
    
    return response()->json([
      'status'    => 200,
      'data'      => $articles->toArray(),
    ], 200);
  }
  
  
  /**
   * Connect an intrastat and an article code
   */
  public function update ($codeId, $articleId)
  {
    
    $code = Intrastat::where('uuid', $codeId)->first();
    
    if (!$code) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Intrastat code not found'],
      ], 404);
    }
    
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Article not found'],
      ], 404);
    }
    
    $rel = $code->articles()->edge($article);
    
    if ($rel) {
      return response()->json([
        'status'    => 400,
        'messages'  => ['Previous relation found'],
      ], 400);
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
  public function destroy ($codeId, $articleId)
  {
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Article was not found'],
      ], 404);
    }
    
    $code = Intrastat::where('uuid', $codeId)->first();
    
    if (!$code) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Intrastat code was not found'],
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
