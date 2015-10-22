<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Customersegment;

use Ramsey\Uuid\Uuid;

class CustomerSegmentController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $segments = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $segments = Customersegment::with($rels)->get();
    } else {
      $segments = Customersegment::all();
    }

    if ($request->has('query')) {
      $query = $request->input('query');

      $filterable = $segments->toArray();

      $segments = array_filter($filterable, function ($segment) use ($query) {
        return (stripos($segment['name'], $query) !== false);
      });
    }

    if ($request->has('formatted')) {
      if ($request->input('formatted') === 'semantic') {
        $out = [];
        foreach ($segments as $segment) {
          $out[] = [
            'name' => (is_object($segment) ? $segment->name : $segment['name']),
            'value' => (is_object($segment) ? $segment->uuid : $segment['uuid'])
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
      'data'      => $segments,
      'heading'   => 'Customer segment',
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
      'name'    => 'required|string',
    ]);

    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Customer segment',
        'messages'  => $messages
      ], 400);
    }

    $segmentData = [
      'uuid'  => Uuid::uuid4()->toString(),
      'name' => $request->input('name')
    ];

    $segment = Customersegment::create($segmentData);

    return response()->json([
      'status'    => 201,
      'data'      => $segment,
      'heading'   => 'Customer segment',
      'message'   => ['Customer segment created'],
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
