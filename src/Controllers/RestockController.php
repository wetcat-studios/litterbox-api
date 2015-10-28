<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Manufacturer;
use Wetcat\Litterbox\Models\Restock;

use Ramsey\Uuid\Uuid;

class RestockController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $orders = [];
    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $orders = Restock::with($rels)->get();
    } else {
      $orders = Restock::all();
    }
    
    return response()->json([
      'status'    => 200,
      'data'      => $orders,
      'heading'   => 'Restock',
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
   * @param  Request  $request
   * @return Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'manufacturer'  => 'required|string', // UUID
      'rows'          => 'required', //Array of uuids
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Rate',
        'messages'  => $messages
      ], 400);
    }

    $restockData = [
      'uuid'  => Uuid::uuid4()->toString()
    ];

    $restock = Restock::create($restockData);

    // Link to manufacturer
    $manufacturer = Manufacturer::where('uuid', $request->input('manufacturer'))->first();
    $rel = $manufacturer->orders()->save($restock);

    // Link to article/s
    foreach ($request->input('rows') as $row) {
      $article = Article::where('uuid', $row['article'])->first();
      $rel = $article->restocks()->save($restock);
      $rel->count = $row['count'];
      $rel->save();
    }

    // Get the newly created restock with all the relationships
    $out = Restock::with('manufacturer', 'articles')->where('uuid', $restock->uuid)->first();

    return response()->json([
      'status'    => 201,
      'data'      => $out,
      'heading'   => 'Restock',
      'message'   => ['Restock request created.'],
    ], 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
      //
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
   * @param  Request  $request
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request, $id)
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
