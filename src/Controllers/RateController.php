<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Currency;
use Wetcat\Litterbox\Models\Rate;

use Ramsey\Uuid\Uuid;

class RateController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $rates = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $rates = Rate::with($rels)->get();
    } else {
      $rates = Rate::all();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $rates,
      'heading'   => 'Rate',
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
      'currency'  => 'required|string',
      'rate'      => 'required|regex:/^[0-9]+[.,]?[0-9]*/',
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

    if (Uuid::isValid($request->input('currency'))) {
      $currency = Currency::where('uuid', $request->input('currency'))->first();  
    } else {
      $currencyData = [
        'uuid'  => Uuid::uuid4()->toString(),
        'name'  => $request->input('currency')
      ];
      $currency = Currency::create($currencyData);
    }
    
    // Make sure the Rate is always using dot-notation for international purposes!
    $rate = str_replace(',', ".", $request->input('rate'));

    $rateData = [
      'uuid'  => Uuid::uuid4()->toString(),
      'rate'  => $rate
    ];

    // Create the Rate
    $rate = Rate::create($rateData);

    // Connect the rate to the currency
    $rel = $currency->rates()->save($rate);

    $out = Rate::with('currency')->where('uuid', $rate->uuid)->first();

    return response()->json([
      'status'  => 200,
      'data'    => $out,
      'heading'   => 'Rate',
      'messages' => ['Added new rate ('.$rate->rate.') to '.$currency->name]
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
      $rate = Rate::with($rels)->where('uuid', $id)->get();
    } else {
      $rate = Rate::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $rate,
      'heading'   => 'Rate',
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
  public function update(Request $request, $uuid)
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
