<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Shippingmethod;
use Wetcat\Litterbox\Models\Segment;

use Ramsey\Uuid\Uuid;

class ShippingmethodController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $methods = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $methods = Shippingmethod::with($rels)->get();
    } else {
      $methods = Shippingmethod::all();
    }

    if ($request->has('query')) {
      $query = $request->input('query');

      $filterable = $methods->toArray();

      $methods = array_filter($filterable, function ($method) use ($query) {
        return (stripos($method['name'], $query) !== false);
      });
    }

    if ($request->has('formatted')) {
      if ($request->input('formatted') === 'semantic') {
        $out = [];
        foreach ($methods as $method) {
          $out[] = [
            'name' => (is_object($method) ? $method->name : $method['name']),
            'value' => (is_object($method) ? $method->uuid : $method['uuid'])
          ];
        }
        return response()->json([
          'success' => true,
          'results' => $out
        ]);
      } 
    }

    return response()->json([
      'status'    => 200,
      'data'      => $methods,
      'heading'   => 'Shipping method',
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
      'name'        => 'required|string',
      'owner'       => 'required|string',
      'restricted'  => 'integer',
    ]);

    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Shippingmethod',
        'messages'  => $messages
      ], 400);
    }

    $owner = Segment::where('uuid', $request->input('owner'))->first();

    // If the name is an uuid, it already exists!
    if (Uuid::isValid($request->input('name'))) {

      $method = Shippingmethod::where('uuid', $request->input('name'))->first();

      $rel = $owner->shipping()->save($method);

      return response()->json([
        'status'    => 200,
        'data'      => $method,
        'heading'   => 'Shippingmethod',
        'message'   => ['Shippingmethod already exists, it was connected.'],
      ], 200);
    } 

    // Otherwise create it!
    else {
      $restricted = 0;
      if ($request->has('restricted')) {
        $restricted = $request->input('restricted');
      }

      $methodData = [
        'uuid'  => Uuid::uuid4()->toString(),
        'name' => $request->input('name'),
        'restricted' => $restricted,
      ];

      $method = Shippingmethod::create($methodData);

      $rel = $owner->shipping()->save($method);
      
      return response()->json([
        'status'    => 201,
        'data'      => $method,
        'heading'   => 'Shippingmethod',
        'message'   => ['Shippingmethod was created.'],
      ], 201);
    }


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
