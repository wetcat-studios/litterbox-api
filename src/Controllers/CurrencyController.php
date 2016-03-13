<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Currency;

use Ramsey\Uuid\Uuid;

class CurrencyController extends Controller {


  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  public function index (Request $request)
  {
    $currencies = Currency::all();
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $currencies->toArray(),
    ], 200);
  }


  public function show (Request $request, $currencyId)
  {
    $currency = Currency::where('uuid', $currencyId)->first();

    if (!$currency) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Currency was not found'],
      ], 404);
    }
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $currency,
    ], 200);
  }
  
    
  public function store (Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name'              =>  'string|required',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    =>  400,
        'messages'  =>  $messages,
      ], 400);
    }

    $currencyData = [
      'uuid'  =>  Uuid::uuid4()->toString(),
      'name'  =>  $request->input('name'),
    ];
    
    $currency = Currency::create($currencyData);
    
    return response()->json([
      'status'    =>  201,
      'data'      =>  $currency,
      'messages'  =>  null
    ], 201);
  }
  
  
  public function update (Request $request, $currencyId)
  {
    $validator = Validator::make($request->all(), [
      'name'  =>  'string',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'messages'  => $messages
      ], 400);
    }
    
    $currency = Currency::where('uuid', $currencyId)->first();

    if (!$currency) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Currency was not found'],
      ], 404);
    }
    
    $updatedData = [];
    
    if ($request->has('name'))
      $updatedData['name'] = $request->input('name');
    
    $updated = $currency->update($updatedData);
    
    if ($updated) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['Updated currency'],
      ], 200);
    } else {
      return response()->json([
        'status'    =>  400,
        'messages'  =>  ['Failed to update currency'],
      ], 400);
    }
  }
  
  
  public function destroy ($currencyId)
  {
    $currency = Currency::where('uuid', $currencyId)->first();

    if (!$currency) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Currency was not found'],
      ], 404);
    }
    
    $currency->delete();

    return response()->json([
      'status'    =>  200,
      'messages'  =>  ["Currency '$currency->name' deleted"],
    ], 200);
  }

}
