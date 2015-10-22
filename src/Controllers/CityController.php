<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Address;
use Wetcat\Litterbox\Models\Country;
use Wetcat\Litterbox\Models\City;

use Ramsey\Uuid\Uuid;

class CityController extends Controller {

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
      $cities = City::with($rels)->get();
    } else {
      $cities = City::all();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $cities,
      'heading'   => 'City',
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
      'country' => 'required',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'City',
        'messages'  => $messages
      ], 400);
    }

    $uuid4 = Uuid::uuid4();
    $cityData = [
      'name'    => $request->input('name')
    ];

    $messages = [];

    $city = City::firstOrCreate($cityData);

    if (!isset($city->uuid)) {
      $city->uuid = $uuid4->toString();
      $city->save();

      $messages[] = 'Created city ' . $city->name . '.';
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
