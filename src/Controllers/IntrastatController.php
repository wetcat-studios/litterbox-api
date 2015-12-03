<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Intrastat;

use Ramsey\Uuid\Uuid;

class IntrastatController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $intrastat = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $intrastat = Intrastat::with($rels)->get();
    } else {
      $intrastat = Intrastat::all();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $intrastat,
      'heading'   => 'Intrastat',
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
      // Address data
      'code'  => 'required',
      'name'  => 'required'
    ]);
    
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Intrastat',
        'messages'  => $messages
      ], 400);
    }

    $intraStat = [
      'uuid'    => Uuid::uuid4()->toString(),
      'code'    => $request->input('code'),
      'name'    => $request->input('name'),
    ];

    $intrastat = Intrastat::create($intraStat);

    $messages[] = 'Intrastat created';
    
    // We made it! Send a success!
    return response()->json([
      'status'    => 201,
      'data'      => $intrastat,
      'heading'   => 'Intrastat',
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
      $intrastat = Intrastat::with($rels)->where('uuid', $id)->get();
    } else {
      $intrastat = Intrastat::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $intrastat,
      'heading'   => 'Intrastat',
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
