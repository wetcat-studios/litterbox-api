<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Brand;

use Ramsey\Uuid\Uuid;

class ArticleBrandController extends Controller {


  public function __construct ()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  /**
   * Show the brand for the Article.
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
    
    $brand = $article->brand()->first();
    
    return response()->json([
      'status'    => 200,
      'data'      => $brand,
    ], 200);
  }
  
  
  /**
   * Create a new brand, and automatically attach it to the article.
   */
  public function store (Request $request, $articleId)
  {
    $validator = Validator::make($request->all(), [
      'name'        => 'required|string',
      'url'         => 'string',
      'description' => 'string',
      'filename'    => 'string',
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
    
    $brandData = [
      'uuid'  =>  Uuid::uuid4()->toString(),
      'name'  =>  $request->input('name'),
    ];

    if ($request->has('url'))
      $brandData['url'] = $request->input('url');

    if ($request->has('description'))
      $brandData['description'] = $request->input('description');
      
    if ($request->has('filename'))
      $brandData['filename'] = $request->input('filename');

    $brand = Brand::create($brandData);
    
    $rel = $brand->articles()->save($article);

    return response()->json([
      'status'    => 201,
      'data'      => $brand,
    ], 201);
  }
  
  
  /**
   * Connect an article and a brand
   */
  public function update ($articleId, $brandId)
  {
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Article not found'],
      ], 404);
    }
    
    $brand = Brand::where('uuid', $brandId)->first();
    
    if (!$brand) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Brand not found'],
      ], 404);
    }
    
    $rel = $brand->articles()->save($article);
    
    return response()->json([
      'status'    => 201,
      'messages'  => ['Connected article to brand']
    ], 201);
  }

  /**
   * Delete a relationship between an article and a brand.
   */
  public function destroy ($articleId, $brandId)
  {
    $brand = Brand::where('uuid', $brandId)->first();
    
    if (!$brand) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Brand was not found'],
      ], 404);
    }
    
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Article was not found'],
      ], 404);
    }
    
    $rel = $brand->articles()->edge($article);
    
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
