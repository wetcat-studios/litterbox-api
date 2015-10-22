<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Manufacturer;
use Wetcat\Litterbox\Models\Currency;

use Ramsey\Uuid\Uuid;

class ManufacturerController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $manufacturers = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $manufacturers = Manufacturer::with($rels)->get();
    } else {
      $manufacturers = Manufacturer::all();
    }

    if ($request->has('query')) {
      $query = $request->input('query');

      $filterable = $manufacturers->toArray();

      $manufacturers = array_filter($filterable, function ($manufacturer) use ($query) {
        return (stripos($manufacturer['name'], $query) !== false);
      });
    }

    if ($request->has('formatted')) {
      if ($request->input('formatted') === 'semantic') {
        $out = [];
        foreach ($manufacturers as $manufacturer) {
          $out[] = [
            'name' => (is_object($manufacturer) ? $manufacturer->name : $manufacturer['name']),
            'value' => (is_object($manufacturer) ? $manufacturer->uuid : $manufacturer['uuid'])
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
      'data'      => $manufacturers,
      'heading'   => 'Manufacturer',
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
      // Manufacturer validation
      'name'      => 'required',
      'rebate'    => 'integer',
      'shipping'  => 'integer',

      // Currency node
      'currency'  => 'required|string', // Uuid
    ]);

    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'  => 400,
        'data'    => null,
        'messages' => $messages
      ], 400);
    }

    $manufacturerData = [
      'uuid'      => Uuid::uuid4()->toString(),
      'name'      => $request->input('name'),
      'rebate'    => $request->input('rebate'),
      'shipping'  => $request->input('shipping'),
    ];

    // Create the manufacturer
    $manufacturer = Manufacturer::create($manufacturerData);

    // Connect the currency
    $currency = Currency::where('uuid', $request->input('currency'))->first();
    $rel = $currency->manufacturers()->save($manufacturer);

    return response()->json([
      'status'    => 201,
      'data'      => $manufacturer,
      'heading'   => 'Manufacturer',
      'messages'  => ['Manufacturer created']
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
