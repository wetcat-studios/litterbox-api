<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;

use Wetcat\Litterbox\Models\Country;

use Rhumsaa\Uuid\Uuid;

class CountryController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    if ($request->has('enabled')) {
      $countries = Country::where('enabled', 1)->get();
      return response()->json([
        'status'    => 200,
        'data'      => $countries,
        'heading'   => 'Country',
        'messages'  => null
      ], 200);
    } else {
      $countries = Country::all();
      return response()->json([
        'status'    => 200,
        'data'      => $countries,
        'heading'   => 'Country',
        'messages'  => null
      ], 200);
    }    
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
    // Reset all countries to off
    $countries = Country::all();
    foreach ($countries as $key => $country) {
      if ($country->enabled === 1) {
        $country->enabled = 0;
        $country->save();
      }
    }

    $data = [];

    $input = $request->all();
    foreach ($input as $key => $value) {
      if ($value === 'on') {
        $country = Country::firstOrNew(['iso' => $key]);
        //$country = Country::where('alpha2code', 'AF')->first();

        //return $country;
        if ($country->exists) {
          $country->update(['enabled' => 1]);
        } else {
          $country->iso = $key;
          $country->enabled = 1;
          $country->uuid = Uuid::uuid4()->toString();
          $country->save();
        }
      }
    }

    return response()->json([
      'status'  => 200,
      'data'    => $data,
      'heading'   => 'Country',
      'messages' => ['Countries updated']
    ], 200);
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
