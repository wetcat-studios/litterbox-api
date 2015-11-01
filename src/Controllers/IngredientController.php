<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Ingredient;

use Ramsey\Uuid\Uuid;

class IngredientController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $ingredients = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $ingredients = Ingredient::with($rels)->get();
    } else {
      $ingredients = Ingredient::all();
    }

    if ($request->has('query')) {
      $query = $request->input('query');

      $filterable = $ingredients->toArray();

      $ingredients = array_filter($filterable, function ($ingredient) use ($query) {
        return (stripos($ingredient['name'], $query) !== false);
      });
    }

    if ($request->has('formatted')) {
      if ($request->input('formatted') === 'semantic') {
        $out = [];
        foreach ($ingredients as $ingredient) {
          $out[] = [
            'name' => (is_object($ingredient) ? $ingredient->name : $ingredient['name']),
            'value' => (is_object($ingredient) ? $ingredient->uuid : $ingredient['uuid'])
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
      'data'      => $ingredients,
      'heading'   => 'Ingredient',
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
      'name'    => 'required|string'
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Ingredient',
        'messages'  => $messages
      ], 400);
    }

    $ingredient = Ingredient::where('uuid', $request->input('currency'))->first();

    $uuid4 = Uuid::uuid4();
    $rateData = [
      'rate'  => $request->input('rate'),
      'uuid'  => $uuid4->toString()
    ];

    // Create the Rate
    $rate = Rate::create($rateData);

    // Connect the rate to the currency
    $rel = $currency->rates()->save($rate);

    return response()->json([
      'status'    => 200,
      'data'      => null,
      'heading'   => 'Ingredient',
      'messages'  => ['Added new ingredient ' . $ingredient->name]
    ], 200);
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
      $ingredient = Ingredient::with($rels)->where('uuid', $id)->get();
    } else {
      $ingredient = Ingredient::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $ingredient,
      'heading'   => 'Ingredient',
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
