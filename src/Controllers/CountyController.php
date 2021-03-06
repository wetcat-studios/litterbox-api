<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\County;
use Wetcat\Litterbox\Models\Address;
use Wetcat\Litterbox\Models\City;

use Ramsey\Uuid\Uuid;

class CountyController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $cities = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $counties = County::with($rels)->get();
    } else {
      $counties = County::all();
    }

    if ($request->has('query')) {
      $query = $request->input('query');

      $filterable = $chains->toArray();

      $counties = array_filter($filterable, function ($county) use ($query) {
        return (stripos($county['name'], $query) !== false);
      });
    }

    return response()->json([
      'status'    => 200,
      'data'      => $counties,
      'heading'   => 'County',
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
      // City data
      'uuid'    => 'required',
      'name'  => 'required',

      // Country node
      'address' => 'required|string',

      // City node
      'city'    => 'required|string',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'County',
        'messages'  => $messages
      ], 400);
    }

    $uuid4 = Uuid::uuid4();
    $countyData = [
      'name'    => $request->input('name')
    ];

    $messages = [];

    $county = County::firstOrCreate($cityData);

    if (!isset($county->uuid)) {
      $county->uuid = $uuid4->toString();
      $county->save();

      $messages[] = 'Created county ' . $county->name . '.';
    }

    // Attach address
    if ($request->has('address') && Uuid::isValid($request->input('address'))) {
      $address = Address::where('uuid', $request->input('address'))->first();
      $rel = $county->addresses()->save($address);
    }

    // Attach city
    if ($request->has('city') && Uuid::isValid($request->input('city'))) {
      $city = City::where('uuid', $request->input('city'))->first();
      $rel = $city->counties()->save($county);
    }

    if (count($city->country)) {
      // Connect the city to a country
      $country = Country::where('uuid', $request->input('country'))->first();
      $rel = $country->cities()->save($address);

      $messages[] = 'Attached city ' . $city->name . ' to ' . $country->name . '.';
    }
    
    // We made it! Send a success!
    return response()->json([
      'status'    => 201,
      'data'      => $city,
      'heading'   => 'City',
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
      $county = County::with($rels)->where('uuid', $id)->get();
    } else {
      $county = County::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $county,
      'heading'   => 'County',
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
