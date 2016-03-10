<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Batch;

use Ramsey\Uuid\Uuid;

class ArticleBatchController extends Controller {


  public function __construct ()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  /**
   * Show the batches for the Article.
   */
  public function index (Request $request, $articleId)
  {
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['The article was not found']
      ], 404);
    }
    
    $batches = $article->batches()->get();
    
    $out = [];
    
    if (isset($batches) && count($batches) > 0) {
      foreach ($batches as $batch) {
        $b = [];
        // Core data
        $b['uuid'] = $batch->uuid;
        if (isset($batch->batchNumber))
          $b['batchNumber'] = $batch->batchNumber;
        if (isset($batch->date))
          $b['date'] = $batch->date;
        if (isset($batch->lastDelivery))
          $b['lastDelivery'] = $batch->lastDelivery;
        if (isset($batch->note))
          $b['note'] = $batch->note;
        if (isset($batch->created_at))
          $b['created_at'] = $batch->created_at;
        if (isset($batch->deleted_at))
          $b['deleted_at'] = $batch->deleted_at;
        // Relationship (count)
        $rel = $batch->article()->edge();
        $b['count'] = $rel->count;
        // Append composed batch
        $out[] = $b;
      }
    }
    
    return response()->json([
      'status'    =>  200,
      'data'      =>  $out,
    ], 200);
  }
  
  
  /**
   * Create a new batch, and automatically attach it to the article.
   */
  public function store (Request $request, $articleId)
  {
    $validator = Validator::make($request->all(), [
      'batchNumber'   =>  'string|required',
      'count'         =>  'integer|required',
      'date'          =>  'date|required',
      'lastDelivery'  =>  'date|required',
      'note'          =>  'string',
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
    
    $batchData = [
      'uuid'          =>  Uuid::uuid4()->toString(),
      'batchNumber'   =>  $request->input('batchNumber'),
      'date'          =>  $request->input('date'),
      'lastDelivery'  =>  $request->input('lastDelivery'),
      'note'          =>  $request->input('note'),
    ];

    if ($request->has('note'))
      $batchData['note'] = $request->input('note');

    $batch = Batch::create($batchData);
    
    $rel = $article->batches()->save($batch);
    $rel->count = $request->input('count');
    $rel->save();
    
    return response()->json([
      'status'    => 201,
      'data'      => $batch,
    ], 201);
  }
  
  
  /**
   * Connect an article and a batch
   */
  public function update ($articleId, $batchId)
  {
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Article not found'],
      ], 404);
    }
    
    $batch = Batch::where('uuid', $batchId)->first();
    
    if (!$batch) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Batch not found'],
      ], 404);
    }
    
    $rel = $article->batches()->save($batch);
    
    return response()->json([
      'status'    => 201,
      'messages'  => ['Connected article to batch']
    ], 201);
  }

  /**
   * Delete a relationship between an article and a batch.
   */
  public function destroy ($articleId, $batchId)
  {
    $batch = Batch::where('uuid', $batchId)->first();
    
    if (!$batch) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Batch was not found'],
      ], 404);
    }
    
    $article = Article::where('uuid', $articleId)->first();
    
    if (!$article) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Article was not found'],
      ], 404);
    }
    
    $rel = $article->batches()->edge($batch);
    
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
