<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\Group;
use Wetcat\Litterbox\Models\Pricelist;

use Ramsey\Uuid\Uuid;

class GroupController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $groups = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $groups = Group::with($rels)->get();
    } else {
      $groups = Group::all();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $groups,
      'heading'   => 'Group',
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
      // Group data
      'uuid'    => 'required',
      'name'  => 'required',
    ]);
    
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Group',
        'messages'  => $messages
      ], 400);
    }

    $groupData = [
      'uuid'  => Uuid::uuid4()->toString(),
      'name'  => $request->input('name')
    ];

    $messages = [];

    $group = Group::create($groupData);
    
    return response()->json([
      'status'    => 201,
      'data'      => $group,
      'heading'   => 'Group',
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
      $group = Group::with($rels)->where('uuid', $id)->get();
    } else {
      $group = Group::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $group,
      'heading'   => 'Group',
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
        'heading'   => 'Group',
        'messages'  => $messages
      ], 400);
    }
    
    $group = Group::where('uuid', $uuid)->first();
    
    if (!!$group) {
      if ($request->has('name')) {
        $group->name = $request->input('name');
      }
      
      $group->save();
      
      return response()->json([
        'status'    => 200,
        'data'      => $group,
        'heading'   => 'Group',
        'messages'  => ['Group updated.']
      ], 200);
    } else {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Group',
        'messages'  => ['Group not found.']
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
    $group = Group::where('uuid', $uuid)->first();

    $group->delete();

    return response()->json([
      'status'    => 200,
      'data'      => $group,
      'heading'   => 'Group',
      'messages'  => ['Group ' . $group->name . ' deleted.']
    ], 200); 
  }

}