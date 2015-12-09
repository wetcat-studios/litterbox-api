<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Pricelist;
use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Group;

use Ramsey\Uuid\Uuid;

use Wetcat\Litterbox\Auth\Roles as RoleHelper;

class PricelistController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index($group, Request $request)
  {
    $lists = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $lists = Pricelist::with($rels)->whereHas('group', function ($q) use ($group) {
          $q->where('uuid', $group);
      })->get();
    } else {
      $lists = Pricelist::whereHas('group', function ($q) use ($group) {
          $q->where('uuid', $group);
      })->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $lists,
      'heading'   => 'Pricelist',
      'messages'  => null
    ], 200); 
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store($group, Request $request)
  {
    $validator = Validator::make($request->all(), [
      'start' => 'required',
      'end'   => 'required',
      
      'group' => 'string|required',
    ]);
    
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Pricelist',
        'messages'  => $messages
      ], 400);
    }

    $pricelistData = [
      'uuid'  => Uuid::uuid4()->toString(),
      'start'  => $request->input('start'),
      'end'  => $request->input('end'),
    ];

    $messages = [];

    $list = Pricelist::create($pricelistData);
   
    // When the pricelist has been created, create relationships to all articles...
    $articles = Article::all();
    foreach ($articles as $article) {
      $relation = $list->articles()->save($article);
      
      // TODO: Apply rebate/s ?
      
      $relation->productCost = $article->productCost;
      $relation->unitPrice = $article->unitPrice;
      $relation->salesPrice = $article->salesPrice;
      $relation->save();
    }
    
    // Link pricelist to group
    if ($request->has('group') && Uuid::isValid($request->input('group'))) {
      $group = Group::where('uuid', $request->input('group'))->firstOrFail();
      $rel = $group->pricelists()->save($list);
    }  else if ($request->has('group') && strlen($request->input('group')) > 0) {
      $groupData = [
        'uuid'  => Uuid::uuid4()->toString(),
        'name'  => $request->input('group')
      ];
      $group = Group::create($groupData);
      $rel = $group->pricelists()->save($list);
    }
    
    return response()->json([
      'status'    => 201,
      'data'      => $list,
      'heading'   => 'Pricelist',
      'messages'  => $messages
    ], 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($group, $pricelist, Request $request)
  {
    if ($request->has('rel')) {
      $list = Pricelist::with($rels)->where('uuid', $pricelist)->get();
    } else {
      $list = Pricelist::where('uuid', $pricelist)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $list,
      'heading'   => 'Pricelist',
      'messages'  => null
    ], 200);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($group, $pricelist, Request $request)
  {
    $validator = Validator::make($request->all(), [
      'start' => 'string',
      'end'   => 'string',
      'recalculate' => 'integer',
    ]);
    
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Pricelist',
        'messages'  => $messages
      ], 400);
    }
    
    $list = Pricelist::where('uuid', $pricelist)->first();
    
    if (!!$list) {
      
      if ($request->has('start')) {
        $list->start = $request->input('start');
      }
      
      if ($request->has('end')) {
        $list->end = $request->input('end');
      }
      
      $list->save();
      
      return response()->json([
        'status'    => 200,
        'data'      => $list,
        'heading'   => 'Pricelist',
        'messages'  => ['Pricelist updated.']
      ], 200);
    } else {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Pricelist',
        'messages'  => ['Pricelist not found.']
      ], 400);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($group, $pricelist)
  {
    $list = Pricelist::where('uuid', $pricelist)->first();

    $list->delete();

    return response()->json([
      'status'    => 200,
      'data'      => $list,
      'heading'   => 'Pricelist',
      'messages'  => ['Pricelist ' . $list->name . ' deleted.']
    ], 200);
  }

}
