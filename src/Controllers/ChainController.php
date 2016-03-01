<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\Chain;
use Wetcat\Litterbox\Models\Country;
use Wetcat\Litterbox\Models\Picture;

use Ramsey\Uuid\Uuid;

class ChainController extends Controller {

  /**
   * Instantiate a new UserController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $chains = [];
    
    // Default limit per request
    $limit = 10;
    
    // ...but if there's a set limit we'll follow that
    if ($request->has('limit')) {
      $limit = $request->input('limit');
    }
    
    // Attach relations
    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $q = Chain::with($rels);
    } else {
      $q = Chain::with([]);
    }
    
    // Do filtering
    if ($request->has('name')) {
      $q->where('name', $request->input('name'));
    }

    $chains = $q->paginate($limit);
    
    return response()->json([
      'status'    => 200,
      'data'      => $chains->toArray(),
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
      'country'   => 'required|string', // Either UUID or iso
      'name'      => 'required|string',
      'corporate' => 'string', // Optional corporate id
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Chain',
        'messages'  => $messages
      ], 400);
    }

    $chainData = [
      'uuid'      => Uuid::uuid4()->toString(),
      'name'      => $request->input('name'),
      'corporate' => $request->input('corporate'),
    ];

    $chain = Chain::create($chainData);

    if ($request->has('country') && Uuid::isValid($request->input('country'))) {
      $country = Country::where('uuid', $request->input('country'))->first();
      $rel = $country->chains()->save($chain);
    } else if ($request->has('country')) {
      $country = Country::where('iso', $request->input('country'))->first();
      
      if (!!$country) {
        $rel = $country->chains()->save($chain);
      } else {
        $countryData = [
          'uuid'    => Uuid::uuid4()->toString(),
          'iso'     => $request->input('country'),
          'enabled' => 1
        ];
        $country = Country::create($countryData);
        $rel = $country->chains()->save($chain);
      }
    }

    return response()->json([
      'status'    => 201,
      'data'      => $chain,
      'heading'   => 'Chain',
      'message'   => ['Chain created.'],
    ], 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id, Request $request)
  {
    $chain = null;

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $chain = Chain::with($rels)->where('uuid', $id)->first();
    } else {
      $chain = Chain::where('uuid', $id)->first();
    }

    $inputs = $request->except(['rel']);

    if ($inputs) {
      foreach ($inputs as $key => $value) {
        if (in_array($key, $this->allowedQueries)) {
          if (!is_array($chain)) {
            $filterable = $chain->toArray();  
          }

          $chain = array_filter($filterable, function ($chain) use ($key, $value) {
            return (stristr($chain[$key], $value) !== false);
          });
        }
      }
    }

    return response()->json([
      'status'    => 200,
      'data'      => $chain,
      'heading'   => 'Chain',
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
