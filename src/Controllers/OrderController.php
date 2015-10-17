<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Order;
use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\Article;

use Rhumsaa\Uuid\Uuid;

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
      'customer'      => 'required|string',
      'articles'      => 'required|array',
      'articleCounts' => 'required|array',
      'numArticles'   => 'required|integer',
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

    $customer = Customer::where('uuid', $request->input('customer'))->first();

    if (!!$customer) {
      $orderData = [
        'uuid'    => Uuid::uuid4()->toString(),
        'number'  => Uuid::uuid1()->toString(),
        'numArticles' => $request->input('numArticles'),
      ];

      $order = Order::create($orderData);
      $rel = $customer->orders()->save($order);

      for ($i = 0; $i < count($request->input('articles')); $i++) {
        $article = Article::where('uuid', $request->input('articles')[$i])->first();
        if (!!$article) {
          $rel = $article->orders()->save($order);

          $rel->count = $request->input('articleCounts')[$i];
          $rel->save();
        }
      }

      return response()->json([
        'status'    => 201,
        'data'      => $order,
        'heading'   => 'Order',
        'message'   => ['Order created.'],
      ], 201);
    } 

    // Failed to find customer
    else {
      $messages[] = 'Could not find customer.';
    }

    return response()->json([
      'status'    => 400,
      'data'      => null,
      'heading'   => 'Order',
      'messages'  => $messages
    ], 400);
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
