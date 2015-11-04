<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Order;
use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\Article;

use Ramsey\Uuid\Uuid;

class OrderController extends Controller {

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
      $orders = Order::with($rels)->get();
    } else {
      $orders = Order::all();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $orders,
      'heading'   => 'Order',
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
      'customer'  => 'required|string',
      'rows'      => 'required', // Each rows is a uuid and a count
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Order',
        'messages'  => $messages
      ], 400);
    }
    
    $orderData = [
      'uuid'  => Uuid::uuid4()->toString()
    ];

    $order = Order::create($orderData);
    
    // Link to customer
    $customer = Customer::where('uuid', $request->input('customer'))->first();
    $rel = $customer->orders()->save($order);

    // Link to article/s
    foreach ($request->input('rows') as $row) {
      $article = Article::where('uuid', $row['article'])->first();
      $rel = $article->orders()->save($order);
      $rel->count = $row['count'];
      $rel->save();
    }
    
    // Get the newly created order with all the relationships
    $out = Order::with('customer', 'articles')->where('uuid', $order->uuid)->first();

    return response()->json([
      'status'    => 201,
      'data'      => $out,
      'heading'   => 'Order',
      'message'   => ['Order request created.'],
    ], 201);
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
      $order = Order::with($rels)->where('uuid', $id)->get();
    } else {
      $order = Order::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $order,
      'heading'   => 'Order',
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
