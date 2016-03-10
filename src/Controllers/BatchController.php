<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Batch;

use Ramsey\Uuid\Uuid;

class BatchController extends Controller {


  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  public function index (Request $request)
  {
    $batches = Batch::all();
    
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
      'status'  => 200,
      'data'    => $out,
    ], 200);
  }


  public function show (Request $request, $batchId)
  {
    $batch = Batch::where('uuid', $batchId)->first();

    if (!$batch) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Batch was not found'],
      ], 404);
    }
    
    $out = [];
    
    if (isset($batch)) {
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
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $out,
    ], 200);
  }
  
  
  public function update (Request $request, $batchId)
  {
    $validator = Validator::make($request->all(), [
      'batchNumber'   => 'string',
      'article'       => 'string',
      'count'         => 'integer',
      'date'          => 'date',
      'lastDelivery'  => 'date',
      'note'          => 'string',
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
    
    $batch = Batch::where('uuid', $batchId)->first();
    
    if (!$batch) {
      return response()->json([
        'status'    => 404,
        'messages'  => ['Batch not found.']
      ], 404);
    }
    
    $updatedData = [];
    
    if ($request->has('batchNumber')) {
      $updateData['batchNumber'] = $request->input('batchNumber');
    }
    
    if ($request->has('article')) {
      $updateData['article'] = $request->input('article');
    }
    
    if ($request->has('count')) {
      $updateData['count'] = $request->input('count');
    }
    
    if ($request->has('date')) {
      $updateData['date'] = $request->input('date');
    }
    
    if ($request->has('lastDelivery')) {
      $updateData['lastDelivery'] = $request->input('lastDelivery');
    }
    
    if ($request->has('note')) {
      $updateData['note'] = $request->input('note');
    }
    
    $updated = $batch->update($updateData);
    
    if ($updated) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['Updated batch'],
      ], 200);
    } else {
      return response()->json([
        'status'    =>  400,
        'messages'  =>  ['Failed to update batch'],
      ], 400);
    }
  }

  
  public function destroy ($batchId)
  {
    $batch = Batch::where('uuid', $batchId)->first();

    if (!$batch) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Batch was not found'],
      ], 404);
    }
    
    $batch->delete();

    return response()->json([
      'status'    =>  200,
      'messages'  =>  ["Batch '$batch->name' deleted"],
    ], 200);
  }

}
