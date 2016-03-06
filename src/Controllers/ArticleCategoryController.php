<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Category;

use Ramsey\Uuid\Uuid;

class ArticleCategoryController extends Controller {


  /**
   * Attach authentication to the actions that modify the db
   */
  public function __construct ()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  /**
   * Show the categories for the Article.
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
    
    $categories = $article->categories()->get();
    
    $out = [];
    
    foreach ($categories as $category) {
      $item = $category;
      $item->type = $article->categories()->edge($category)->type;
      $out[] = $item;
    }   
        
    return response()->json([
      'status'    => 200,
      'data'      => $out,
    ], 200);
  }
  
  
  /**
   * Create a new category, and automatically attach it to the article.
   */
  public function store (Request $request, $articleId)
  {
    $validator = Validator::make($request->all(), [
      'name'  =>  'required|string',
      'type'  =>  'required|string',
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
        'status'    => 404,
        'data'      => null,
        'messages'  => ['The article was not found']
      ], 404);
    }

    $categoryData = [
      'uuid'  =>  Uuid::uuid4()->toString(),
      'name'  =>  $request->input('name')
    ];

    $category = Category::create($categoryData);

    $rel = $article->categories()->save($category);
    
    $rel->type = $request->input('type');
    $rel->save();
    
    return response()->json([
      'status'    => 201,
      'data'      => $category,
    ], 201);
  }
  
  
  /**
   * Connect an article and a category
   */
  public function update (Request $request, $articleId, $categoryId)
  {
    $validator = Validator::make($request->all(), [
      'type'  =>  'required|string',
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
        'messages'  => ['Article not found'],
      ], 404);
    }
    
    $category = Category::where('uuid', $categoryId)->first();
    
    if (!$category) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Category not found'],
      ], 404);
    }
    
    $rel = $article->categories()->save($category);
    
    $rel->type = $request->input('type');
    $rel->save();
    
    return response()->json([
      'status'    => 201,
      'messages'  => ['Connected article to category']
    ], 201);
  }
  
  
  /**
   * Delete a relationship between an article and a category
   */
  public function destroy ($articleId, $categoryId)
  {
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Article was not found'],
      ], 404);
    }
    
    $category = Category::where('uuid', $categoryId)->first();
    
    if (!$category) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Category was not found'],
      ], 404);
    }
    
    $rel = $article->categories()->edge($category);
    
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
