<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Ingredient;

use Ramsey\Uuid\Uuid;

class InvoiceController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index (Request $request)
  {
    $invoices = $this->fortie->invoices()->all();
    
    return response()->json([
      'status'    => 200,
      'data'      => $invoices,
      'heading'   => 'Invoices',
      'messages'  => null
    ], 200);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create ()
  {
      //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  Request  $request
   * @return Response
   */
  public function store (Request $request)
  {
    
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit ($id)
  {
      //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  Request  $request
   * @param  int  $id
   * @return Response
   */
  public function update (Request $request, $id)
  {
      //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy ($id)
  {
      //
  }
}
