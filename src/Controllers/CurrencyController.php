<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Currency;
use Wetcat\Litterbox\Models\Rate;

use Ramsey\Uuid\Uuid;

class CurrencyController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $currencies = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $currencies = Currency::with($rels)->get();
    } else {
      $currencies = Currency::all();
    }

    if ($request->has('query')) {
      $query = $request->input('query');

      $filterable = $currencies->toArray();

      $currencies = array_filter($filterable, function ($currency) use ($query) {
        return (stripos($currency['name'], $query) !== false);
      });
    }

    if ($request->has('formatted')) {
      if ($request->input('formatted') === 'semantic') {
        $out = [];
        foreach ($currencies as $currency) {
          $out[] = [
            'name' => (is_object($currency) ? $currency->name : $currency['name']),
            'value' => (is_object($currency) ? $currency->uuid : $currency['uuid'])
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
      'data'      => $currencies,
      'heading'   => 'Currency',
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
      'name' => 'required',
      'rate' => 'required|regex:/^[0-9]+[.,]?[0-9]*/',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Currency',
        'messages'  => $messages
      ], 400);
    }

    // Make sure the Rate is always using dot-notation for international purposes!
    $rate = str_replace(',', ".", $request->input('rate'));

    $currencyData = [
      'uuid'  => Uuid::uuid4()->toString(),
      'name'  => $request->input('name')
    ];

    $rateData = [
      'uuid'  => Uuid::uuid4()->toString(),
      'rate'  => $rate
    ];

// TODO: Fix this part! Look up the country based on CURENCY CODE/s
    
    // Find the country
    //$country = Country::whereIn('currencies', )

    // Create the Rate
    $rate = Rate::create($rateData);

    // Create the Currency
    $currency = Currency::create($currencyData);
    $rel = $currency->rates()->save($rate);

    $out = Currency::with('rates')->where('uuid', $currency->uuid)->first();
    
    return response()->json([
      'status'    => 200,
      'data'      => $out,
      'heading'   => 'Currency',
      'messages'  => ['Currency ' . $currency->name . ' created']
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
      $currency = Currency::with($rels)->where('uuid', $id)->get();
    } else {
      $currency = Currency::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $currency,
      'heading'   => 'Currency',
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
    $validator = Validator::make($request->all(), [
      'name' => 'string',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Currency',
        'messages'  => $messages
      ], 400);
    }
    
    $currency = Currency::where('uuid', $uuid)->first();
    
    if (!!$currency) {
      
      if ($request->has('name')) {
        $currency->name = $request->input('name');
      }
      
      $currency->save();
      
    } else {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Currency',
        'messages'  => ['Currency not found.']
      ], 400);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($uuid)
  {
    $currency = Currency::where('uuid', $uuid)->first();

    $currency->delete();

    return response()->json([
      'status'    => 200,
      'data'      => $currency,
      'heading'   => 'Currency',
      'messages'  => ['Currency ' . $currency->name . ' deleted.']
    ], 200); 
  }

}
