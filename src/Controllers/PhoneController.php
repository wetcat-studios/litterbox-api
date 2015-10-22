<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Phone;
use Wetcat\Litterbox\Models\User;
use Wetcat\Litterbox\Models\Manufacturer;
use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\Country;

use Ramsey\Uuid\Uuid;

use Unirest\Request as Unirequest;

class PhoneController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $countries = Country::where('enabled', 1)->get(['iso']);
    
    $codeArr = [];
    foreach ($countries as $country) {
      $codeArr[] = $country->iso;
    }
    
    $urlext = implode(';', $codeArr);

    $response = Unirequest::get('https://restcountries-v1.p.mashape.com/alpha/?codes='.$urlext,
      array(
        'X-Mashape-Key' => env('MASHAPE', ''),
        'Accept' => 'application/json'
      )
    );
  
    $countriesAssoc = [];
    foreach ($response->body as $key => $value) {
      $countriesAssoc[$value->alpha2Code] = $value;
    }


    $phones = [];

    if ($request->has('rel')) {
      $phones = Order::with($request->input('rel'))->get();

      $phones = Phone::with('country ' . $request->input('rel'))->whereHas('country', function ($query) use ($codeArr) {
        $query->whereIn('iso', $codeArr);
      })->get();
    } else {
      $phones = Phone::with('country')->whereHas('country', function ($query) use ($codeArr) {
        $query->whereIn('iso', $codeArr);
      })->get();
    }

    foreach ($phones as $key => $value) {
      $value['country']['name'] = $countriesAssoc[$value['country']['iso']]->name;
      $value['country']['callingCode'] = $countriesAssoc[$value['country']['iso']]->callingCodes[0];
    }

    return response()->json([
      'status'    => 200,
      'data'      => $phones,
      'heading'   => 'Phone',
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
      'uuid' => 'required|string',
      'country' => 'required',
      'number' => 'required|numeric',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Phone',
        'messages'  => $messages
      ], 400);
    }

    $uuid = $request->input('uuid');
    $iso = $request->input('country');
    $number = $request->input('number');

    $response = Unirequest::get('https://restcountries-v1.p.mashape.com/alpha/?codes='.$iso,
      array(
        'X-Mashape-Key' => env('MASHAPE', ''),
        'Accept' => 'application/json'
      )
    );
    $fullCountry = $response->body;
    $countryCode = $fullCountry[0]->callingCodes[0];

    $country = Country::where('iso', $iso)->first();

    // Try finding by User
    $user = User::where('uuid', $uuid)->first();
    $customer = Customer::where('uuid', $uuid)->first();
    $manufacturer = Manufacturer::where('uuid', $uuid)->first();

    $uuid4 = Uuid::uuid4();

    $data = [];
    $messages = [];

    // Find the correct type of node!
    if (!!$user && $user->exists) {
      $phone = new Phone([
        'code'    => $countryCode,
        'number'  => $number,
        'uuid'    => $uuid4->toString(),
      ]);
      $rel = $user->phones()->save($phone);
      $rel = $country->phones()->save($phone);

      $data = [
        'callingCode' => $countryCode,
        'number'      => $number,
        'country'     => $fullCountry[0]->name
      ];
      $messages[] = 'Phone was added to the user';
    } else if (!!$customer && $customer->exists) {
      $phone = new Phone([
        'code'    => $countryCode,
        'number'  => $number,
        'uuid'    => $uuid4->toString(),
      ]);
      $rel = $customer->phones()->save($phone);
      $rel = $country->phones()->save($phone);

      $data = [
        'callingCode' => $countryCode,
        'number'      => $number,
        'country'     => $fullCountry[0]->name
      ];
      $messages[] = 'Phone was added to the customer';
    } else if (!!$manufacturer && $manufacturer->exists) {
      $phone = new Phone([
        'code'    => $countryCode,
        'number'  => $number,
        'uuid'    => $uuid4->toString(),
      ]);
      $rel = $manufacturer->phones()->save($phone);
      $rel = $country->phones()->save($phone);

      $data = [
        'callingCode' => $countryCode,
        'number'      => $number,
        'country'     => $fullCountry[0]->name
      ];
      $messages[] = 'Phone was added to the manufacturer';
    } 

    // Or if it was not one of the three accepted node types we'll simply send an error!
    else {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Phone',
        'messages'  => ["Invalid node type, can't attach a phone number."]
      ], 400);
    }

    // We made it! Send a success!
    return response()->json([
      'status'    => 201,
      'data'      => $data,
      'heading'   => 'Phone',
      'messages'  => $messages
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
  public function update(Request $request, $uuid)
  {
    $validator = Validator::make($request->all(), [
      'number' => 'required|numeric',
    ]);

    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Phone',
        'messages'  => $messages
      ], 400);
    }

    $phone = Phone::where('uuid', $uuid)->first();

    if ($phone->exists) {
      $phone->update(['number' => $request->input('number')]);

      return response()->json([
        'status'    => 200,
        'data'      => $phone,
        'heading'   => 'Phone',
        'messages'  => ['The phone was created.']
      ], 200);
    } else {
      return response()->json([
        'status'    => 404,
        'data'      => null,
        'heading'   => 'Phone',
        'messages'  => ['The phone was not found']
      ], 404);
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
    $phone = Phone::where('uuid', $uuid)->first();

    if ($phone->exists) {
      $phone->delete();

      return response()->json([
        'status'    => 200,
        'data'      => $phone,
        'heading'   => 'Phone',
        'messages'  => ['Phone ' . $phone->number . ' was deleted']
      ], 200);
    } else {
      return response()->json([
        'status'    => 404,
        'data'      => null,
        'heading'   => 'Phone',
        'messages'  => ['The phone was not found']
      ], 404);
    }
  }

}
