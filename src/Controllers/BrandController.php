<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Brand;
use Wetcat\Litterbox\Models\Picture;

use Rhumsaa\Uuid\Uuid;

class BrandController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $brands = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $brands = Brand::with($rels)->get();
    } else {
      $brands = Brand::all();
    }

    if ($request->has('query')) {
      $query = $request->input('query');

      $filterable = $brands->toArray();

      $brands = array_filter($filterable, function ($brand) use ($query) {
        return (stripos($brand['name'], $query) !== false);
      });
    }

    if ($request->has('formatted')) {
      if ($request->input('formatted') === 'semantic') {
        $out = [];
        foreach ($brands as $brand) {
          $out[] = [
            'name' => (is_object($brand) ? $brand->name : $brand['name']),
            'value' => (is_object($brand) ? $brand->uuid : $brand['uuid'])
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
      'data'      => $brands,
      'heading'   => 'Brand',
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
      'name'        => 'required|string',
      'url'         => 'string',
      'description' => 'string',
      'filename'    => 'string',
    ]);

    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Brand',
        'messages'  => $messages
      ], 400);
    }

    $brandData = [
      'uuid'  => Uuid::uuid4()->toString(),
      'name'  => $request->input('name'),
    ];

    if ($request->has('url'))
      $brandData['url'] = $request->input('url');

    if ($request->has('description'))
      $brandData['description'] = $request->input('description');

    $brand = Brand::create($brandData);

    // Attach the picture
    if ($request->has('filename')) {
      // Single or multiple pictures?
      if (strpos($request->input('filename'), ',') !== false) {
        // Multiple files
        $filenames = explode(',', $request->input('filename'));
        foreach ($filenames as $filename) {
          $picture = Picture::where('filename', $filename)->first();
          if (!!$picture) {
            $rel = $brand->pictures()->save($picture);
          }  
        }
      } else {
        // Single file
        $picture = Picture::where('filename', $request->input('filename'))->first();
        if (!!$picture) {
          $rel = $brand->pictures()->save($picture);
        }
      }    
    }

    return response()->json([
      'status'    => 201,
      'data'      => $brand,
      'heading'   => 'Brand',
      'message'   => ['Brand created.'],
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
