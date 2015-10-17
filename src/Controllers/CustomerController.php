<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Auth;

use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\Category;
use Wetcat\Litterbox\Models\Chain;
use Wetcat\Litterbox\Models\Address;
use Wetcat\Litterbox\Models\Brand;
use Wetcat\Litterbox\Models\Manufacturer;
use Wetcat\Litterbox\Models\Currency;
use Wetcat\Litterbox\Models\Segment;
use Wetcat\Litterbox\Models\Customersegment;
use Wetcat\Litterbox\Models\Chainsegment;
use Wetcat\Litterbox\Models\Picture;
use Wetcat\Litterbox\Models\User;

use Rhumsaa\Uuid\Uuid;

class CustomerController extends Controller {

  protected $allowedQueries = [
    'name', 'verifiedBy',
  ];

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $customers = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $customers = Customer::with($rels)->get();
    } else {
      $customers = Customer::all();
    }

    $inputs = $request->except(['rel']);

    if ($inputs) {
      foreach ($inputs as $key => $value) {
        if (in_array($key, $this->allowedQueries)) {
          if (!is_array($customers)) {
            $filterable = $customers->toArray();  
          }

          $customers = array_filter($filterable, function ($customer) use ($key, $value) {
            return (stristr($customer[$key], $value) !== false);
          });
        }
      }
    }

    if ($request->has('formatted')) {
      if ($request->input('formatted') === 'semantic') {
        $out = [];
        foreach ($customers as $customer) {
          $out[] = [
            'name' => (is_object($customer) ? $customer->name : $customer['name']),
            'value' => (is_object($customer) ? $customer->uuid : $customer['uuid'])
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
      'data'      => $customers,
      'heading'   => 'Customer',
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
    return view('article.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      // Customer validation
      'name'          => 'required',
      'number'        => 'required',
      'corporate'     => 'required',

      // The customer segment type (uuid for the node)
      'customer-segment'  => 'required|string',

      // Chain validation (just the uuid)
      'chain'         => 'string',

      // Chain segment (just the uuid, this is not required!)
      'chain-segment' => 'string'
    ]);

    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Customer',
        'messages'  => $messages
      ], 400);
    }

    $invoice_type_paper = 0;
    if ($request->has('invoice-type-paper')) {
      $invoice_type_paper = $request->input('invoice-type-paper');
    }

    $invoice_type_email = 0;
    if ($request->has('invoice-type-email')) {
      $invoice_type_email = $request->input('invoice-type-email');
    }

    $customerData = [
      'uuid'      => Uuid::uuid4()->toString(),
      'name'      => $request->input('name'),
      'number'    => $request->input('number'),
      'corporate' => $request->input('corporate'),
      'store_type'          => $request->input('store-type'),
      'invoice_type_paper'  => $invoice_type_paper,
      'invoice_type_email'  => $invoice_type_email,
    ];

    // Create the customer
    $customer = Customer::create($customerData);

    // Link customer to chain
    if ($request->has('chain') && Uuid::isValid($request->input('chain'))) {
      $chain = Chain::where('uuid', $request->input('chain'))->firstOrFail();
      $rel = $chain->members()->save($customer);
    }

    // Link customer to chain-segment if it exists
    if ($request->has('chain-segment') && Uuid::isValid($request->input('chain-segment'))) {
      $chainSegment = Chainsegment::where('uuid', $request->input('chain-segment'))->firstOrFail();
      $rel = $chainSegment->customers()->save($customer);
    } else {
      $segmentData = [
        'uuid'  => Uuid::uuid4()->toString(),
        'name'  => $request->input('chain-segment')
      ];
      $chainSegment = Chainsegment::create($segmentData);
      $rel = $chain->segments()->save($chainSegment);
      $rel = $chainSegment->customers()->save($customer);
    }


    // Link customer to customer-segment
    if ($request->has('customer-segment') && Uuid::isValid($request->input('customer-segment'))) {
      $customerSegment = Customersegment::where('uuid', $request->input('customer-segment'))->firstOrFail();
      $rel = $customerSegment->customers()->save($customer);
    } else {
      $segmentData = [
        'uuid'  => Uuid::uuid4()->toString(),
        'name'  => $request->input('customer-segment')
      ];
      $customerSegment = Customersegment::create($segmentData);
      $rel = $customerSegment->customers()->save($customer);
    }

    // Set the verified by field
    $admin = User::where('uuid', Auth::user()->uuid)->first();
    $customer->verifiedBy()->save($admin);

    return response()->json([
      'status'    => 201,
      'data'      => $customer,
      'heading'   => 'Customer',
      'message'   => null
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
  
  
  /**
   * Verify a customer node.
   */
  public function verify ()
  {
    $user = GoodtradeAdmin\User::where('uuid', Auth::user()->uuid)->first();
    $input = Request::except(['_token']);
    $updated = [];
    foreach ($input as $key => $value) {
      if ($value === 'on') {
        $customer = GoodtradeAdmin\Customer::where('uuid', $key)->first();
        $customer->number = GoodtradeAdmin\CustomerHelper::createCustomerNumber($customer->name);
        $customer->save();
        $customer->verifiedBy()->save($user);
        $update[] = [
          'uuid'  => $customer->uuid,
          'name'  => $customer->name,
        ];
      }
    }

    return response()->json([
      'status'    => 200,
      'data'      => $updated,
      'heading'   => 'Verified customers',
      'messages'  => $messages
    ], 200);
  }]);
  }

}
