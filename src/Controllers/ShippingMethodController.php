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
      'ownertype'   => 'required|string'
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

    // If the name is an uuid, it already exists!
    if (Uuid::isValid($request->input('name'))) {
      $method = Shippingmethod::where('uuid', $request->input('name'))->first();
    } else {
      $method = Shippingmethod::where('name', $request->input('name'))->first();

      if (!!$method) {
        // Don't create it if the name already exists
      } else {
        $methodData = [
          'uuid'  => Uuid::uuid4()->toString(),
          'name' => $request->input('name'),
          'restricted' => $request->input('restricted'),
        ];

        $method = Shippingmethod::create($methodData);
        $messages[] = 'Shipping method was created';
      }
    }

    if ($request->has('owner') && $request->has('ownertype') && Uuid::isValid($request->input('owner'))) {
      switch ($request->input('ownertype')) {
        case 'segment':
          $segment = Segment::where('uuid', $request->input('owner'))->first();
          $rel = $segment->shipping()->save($method);
          $messages[] = 'Shipping method was added to the segment';
          break;
      }
    } else {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Address',
        'messages'  => ["Invalid owner node type."]
      ], 400);
    }


    // We made it! Send a success!
    return response()->json([
      'status'    => 201,
      'data'      => $method,
      'heading'   => 'Address',
      'messages'  => $messages
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
      $shippingmethod = Shippingmethod::with($rels)->where('uuid', $id)->get();
    } else {
      $shippingmethod = Shippingmethod::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $shippingmethod,
      'heading'   => 'Shippingmethod',
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
