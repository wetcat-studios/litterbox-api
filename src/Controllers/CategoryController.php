<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Category;

use Ramsey\Uuid\Uuid;

class CategoryController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $categories = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $categories = Category::with($rels)->get();
    } else {
      $categories = Category::all();
    }

    if ($request->has('query')) {
      $query = $request->input('query');

      $filterable = $categories->toArray();

      $categories = array_filter($filterable, function ($category) use ($query) {
        return (stripos($category['name'], $query) !== false);
      });
    }

    if ($request->has('formatted')) {
      if ($request->input('formatted') === 'semantic') {
        $out = [];
        foreach ($categories as $category) {
          $out[] = [
            'name' => (is_object($category) ? $category->name : $category['name']),
            'value' => (is_object($category) ? $category->uuid : $category['uuid'])
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
      'data'      => $categories,
      'heading'   => 'Category',
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
      'name'    => 'required|string',
    ]);

    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Category',
        'messages'  => $messages
      ], 400);
    }

    $categoryData = [
      'uuid'  => Uuid::uuid4()->toString(),
      'name' => $request->input('name')
    ];

    $category = Category::create($categoryData);

    return response()->json([
      'status'    => 201,
      'data'      => $category,
      'heading'   => 'Category',
      'message'   => ['Category created'],
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
