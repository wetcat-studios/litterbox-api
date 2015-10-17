<?php namespace Wetcat\Litterbox\Controllers;

use Illuminate\Http\Request;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

class RestockController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
      //
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
      'country' => 'required',
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
        'heading'   => 'Rate',
        'messages'  => $messages
      ], 400);
    }

    $restockData = [
      'uuid'  => Uuid::uuid4()->toString(),
      'name' => $request->input('name')
    ];

    $chain = Chain::create($chainData);

    $country = Country::where('iso', $request->input('country'))->first();
    $rel = $country->chains()->save($chain);

    return response()->json([
      'status'    => 201,
      'data'      => $chain,
      'heading'   => 'Rate',
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
