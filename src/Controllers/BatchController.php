<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Category;
use Wetcat\Litterbox\Models\Batch;
use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Brand;
use Wetcat\Litterbox\Models\Manufacturer;
use Wetcat\Litterbox\Models\Currency;
use Wetcat\Litterbox\Models\Delivery;
use Wetcat\Litterbox\Models\Segment;

use Ramsey\Uuid\Uuid;

class BatchController extends Controller {

  /**
   * Instantiate a new UserController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('litterbox-auth');
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $batches = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $batches = Batch::with($rels)->get();
    } else {
      $batches = Batch::all();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $batches,
      'heading'   => 'Batch',
      'messages'  => null
    ], 200);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'number'        => 'required',
      'article'       => 'required|string',
      'count'         => 'required|integer',
      'date'          => 'required',
      'lastdelivery'  => 'required',
      'note'          => 'string',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Batch',
        'messages'  => $messages
      ], 400);
    }

    $batchData = [
      'uuid'    => Uuid::uuid4()->toString(),
      'number'  => $request->input('number'),
      'date'    => $request->input('date'),
      'lastdelivery'  => $request->input('lastdelivery'),
      'note'    => $request->input('note'),
    ];

    $batch = Batch::create($batchData);

    // Find and connect to Article
    $article = Article::where('uuid', $request->input('article'))->first();
    $rel = $article->batches()->save($batch);
    $rel->count = $request->input('count');
    $rel->save();

/*
Don't know what this is...
    for ($i = 0; $i < count($request->input('article')); $i++) {
      $article = Article::where('uuid', $request->input('article')[$i])->first();
      if (!!$article) {
        $rel = $article->orders()->save($order);

        $rel->count = $request->input('count')[$i];
        $rel->save();
      }
    }
*/

    return response()->json([
      'status'    => 201,
      'data'      => $batch,
      'heading'   => 'Batch',
      'message'   => ['Batch created.'],
    ], 201);


    /*
    $validator = Validator::make($request->all(), [
      // Batch validation
      'number'    => 'required',
      'stock'     => 'required',
      'date'      => 'required',

      // Article validation
      'article'   => 'required|string',

      // Delivery validation
      'delivery'  => 'required|string',
    ]);

    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Batch',
        'messages'  => $messages
      ], 400);
    }

    $batchData = [
      'uuid'    => Uuid::uuid4()->toString(),
      'number'  => $request->input('number'),
      'stock'   => $request->input('stock'),
      'date'    => $request->input('date')
    ];

    $messages = [];

    // Create the batch
    $batch = Batch::create($batchData);

    if (!!$batch && $batch->exists) {
      $messages[] = 'Batch created.';
    }

    // Find and connect to Article
    $article = Article::where('uuid', $request->input('article'))->firstOrFail();
    $relation = $article->batches()->save($batch);

    // Find and connect to Delivery
    $delivery = Delivery::where('uuid', $request->input('delivery'))->firstOrFail();
    $relation = $delivery->batches()->save($batch);

    // We made it! Send a success!
    return response()->json([
      'status'    => 201,
      'data'      => $batch,
      'heading'   => 'Batch',
      'messages'  => $messages
    ], 201);
    */
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show(Request $request, $id)
  {
    if ($request->has('rel')) {
      $batch = Batch::with($rels)->where('uuid', $id)->get();
    } else {
      $batch = Batch::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $batch,
      'heading'   => 'Batch',
      'messages'  => null
    ], 200);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
    //
  }

}
