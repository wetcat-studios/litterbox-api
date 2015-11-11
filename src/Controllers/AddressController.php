<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Address;
use Wetcat\Litterbox\Models\Country;
use Wetcat\Litterbox\Models\County;
use Wetcat\Litterbox\Models\User;
use Wetcat\Litterbox\Models\Manufacturer;
use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\City;

use Ramsey\Uuid\Uuid;

class AddressController extends Controller {

  /**
   * Instantiate a new UserController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('litterbox-auth');
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
    
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $addresses = [];
    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $addresses = Address::with($rels)->get();
      
      //$edge = $location->user()->edge($location->user);
      
    } else {
      $addresses = Address::all();
    }
    
    return response()->json([
      'status'    => 200,
      'data'      => $addresses,
      'heading'   => null,
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
      'type'    => 'required|string',
      'street'  => 'required|string',
      'zip'     => 'required|string',

      // City node
      'city'    => 'required|string', // City name

      // County node
      'county'  => 'required|string', // Name or UUID
      
      // Country node
      'country' => 'required|string', // alpha2code or UUID

      // The owner node
      'owner' => 'required|string', // uuid
      'ownertype' => 'required|string' // Name of type
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Address',
        'messages'  => $messages
      ], 400);
    }

    $messages = [];

    $uuid4 = Uuid::uuid4();
    $addressData = [
      'uuid'    => $uuid4->toString(),
      'type'    => $request->input('type'),
      'street'  => $request->input('street'),
      //'city'    => $request->input('city'),
      'zip'     => $request->input('zip'),
    ];

    $address = Address::create($addressData);

    // Connect the address to a country
    if ($request->has('country') && Uuid::isValid($request->input('country'))) {
      $country = Country::where('uuid', $request->input('country'))->first();
      $rel = $country->addresses()->save($address);
    } else if ($request->has('country')) {
      $country = Country::where('iso', $request->input('country'))->first();
      
      if (!!$country) {
        $rel = $country->addresses()->save($address);
      } else {
        $countryData = [
          'uuid'    => Uuid::uuid4()->toString(),
          'iso'     => $request->input('country'),
          'enabled' => 1
        ];
        $country = Country::create($countryData);
        $rel = $country->addresses()->save($address);
      }
    }


    // Connect the address to a city
    if ($request->has('city') && Uuid::isValid($request->input('city'))) {
      $city = City::where('uuid', $request->input('city'))->first();
      $rel = $city->addresses()->save($address);
    } else if ($request->has('city')) {
      $city = City::where('name', $request->input('city'))->first();

      if (!!$city) {
        $rel = $city->addresses()->save($address);
      } else {
        $cityData = [
          'uuid'  => Uuid::uuid4()->toString(),
          'name'  => $request->input('city'),
        ];
        $city = City::create($cityData);
        $rel = $city->addresses()->save($address);
      }
    }
    

    // Connect the address to a county
    if ($request->has('county') && Uuid::isValid($request->input('county'))) {
      $county = County::where('uuid', $request->input('county'))->first();
      $rel = $county->addresses()->save($address);
    } else if ($request->has('county')) {
      $county = County::where('name', $request->input('county'))->first();

      if (!!$county) {
        $rel = $county->addresses()->save($address);
      } else {
        $countyData = [
          'uuid'  => Uuid::uuid4()->toString(),
          'name'  => $request->input('county'),
        ];
        $county = County::create($countyData);
        $rel = $county->addresses()->save($address);
      }
    }


    if ($request->has('owner') && $request->has('ownertype') && Uuid::isValid($request->input('owner'))) {
      switch ($request->input('ownertype')) {
        case 'user':
          $user = User::where('uuid', $request->input('owner'))->first();
          $rel = $user->addresses()->save($address);
          $messages[] = 'Address was added to the user';
          break;

        case 'customer':
          $customer = Customer::where('uuid', $request->input('owner'))->first();
          $rel = $customer->addresses()->save($address);
          $messages[] = 'Address was added to the customer';
          break;

        case 'manufacturer':
          $manufacturer = Manufacturer::where('uuid', $request->input('owner'))->first();
          $rel = $manufacturer->addresses()->save($address);
          $messages[] = 'Address was added to the manufacturer';
          break;
      }
    }

    // Or if it was not one of the three accepted node types we'll simply send an error!
    else {
      // Remove the address if no owner node was found
      $address->forceDelete();

      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Address',
        'messages'  => ["Invalid node type, can't attach an address."]
      ], 400);
    }

    // We made it! Send a success!
    return response()->json([
      'status'    => 201,
      'data'      => $address,
      'heading'   => 'Address',
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
      $address = Address::with($rels)->where('uuid', $id)->get();
    } else {
      $address = Address::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $address,
      'heading'   => 'Address',
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
