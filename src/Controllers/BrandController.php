<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use Wetcat\Litterbox\Models\Brand;
use Wetcat\Litterbox\Models\Picture;

use Ramsey\Uuid\Uuid;

class BrandController extends Controller {

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
    $brands = [];
    
    // Default limit per request
    $limit = 10;
    
    // ...but if there's a set limit we'll follow that
    if ($request->has('limit')) {
      $limit = $request->input('limit');
    }
    
    // Attach relations
    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $q = Brand::with($rels);
    } else {
      $q = Brand::with([]);
    }
    
    // Do filtering
    if ($request->has('name')) {
      $q->where('name', $request->input('name'));
    }

    $brands = $q->paginate($limit);
    
    return response()->json([
      'status'    => 200,
      'data'      => $brands->toArray(),
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
  public function show(Request $request, $id)
  {
    if ($request->has('rel')) {
      $brand = Brand::with($rels)->where('uuid', $id)->get();
    } else {
      $brand = Brand::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $brand,
      'heading'   => 'Brand',
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
        'heading'   => 'Brand',
        'messages'  => $messages
      ], 400);
    }
    
    $brand = Brand::where('uuid', $uuid)->first();
    
    if (!!$brand) {
      
      if ($request->has('name')) {
        $brand->name = $request->input('name');
      }
      
      if ($request->has('url')) {
        $brand->url = $request->input('url');
      }
      
      if ($request->has('description')) {
        $brand->description = $request->input('description');
      }
      
      $brand->save();
      
      $out = Brand::with(['pictures.thumbnail', 'thumbnail'])->where('uuid', $brand->uuid)->first();
      
      return response()->json([
        'status'    => 200,
        'data'      => $out,
        'heading'   => 'Brand',
        'messages'  => ['Brand updated.']
      ], 200);
    } else {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Brand',
        'messages'  => ['Brand not found.']
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
    $brand = Brand::where('uuid', $uuid)->first();

    $brand->delete();

    return response()->json([
      'status'    => 200,
      'data'      => $brand,
      'heading'   => 'Brand',
      'messages'  => ['Brand ' . $brand->name . ' deleted.']
    ], 200); 
  }

}
