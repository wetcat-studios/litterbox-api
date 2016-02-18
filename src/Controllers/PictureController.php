<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use File;

use Wetcat\Litterbox\Models\Picture;
use Wetcat\Litterbox\Models\Thumbnail;

use Ramsey\Uuid\Uuid;
use Image;

class PictureController extends Controller {

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
    $pictures = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $pictures = Picture::with($rels)->get();
    } else {
      $pictures = Picture::all();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $pictures,
      'heading'   => 'Picture',
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
      'picture' => 'required|image',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Picture',
        'messages'  => $messages
      ], 400);
    }

    $uuid = Uuid::uuid1()->toString();

    $ext = $request->file('picture')->getClientOriginalExtension();
    $mime = $request->file('picture')->getMimeType();
    $dir = public_path() . '/uploads/' . $mime;
    $filename = $uuid . ".{$ext}";

    $thumbdir = public_path() . '/uploads/' . $mime . '/thumbs/';
    $thumbname = $uuid . "_thumb.{$ext}";

    $arr = getimagesize($request->file('picture'));
    $width = ($arr[0] > $arr[1] ? '48' : null);
    $height = ($arr[1] > $arr[0] ? '48' : null);
    
    //$uploadResult = $request->file('picture')->move($dir, $filename);

    try {
      File::makeDirectory($dir, 0775, true, true);
      File::makeDirectory($thumbdir, 0775, true, true);

      
      // upload new image
      Image::make($request->file('picture'))
        // original
        ->save($dir . '/' . $filename)
        // thumbnail
        //->fit('48', '48')
        ->resize($width, $height, function ($constraint) {
          $constraint->aspectRatio();
          $constraint->upsize();
        })
        ->save($thumbdir . '/' . $thumbname)
        ->destroy();

        // Save picture node
        $pictureData = [
          'original'  => $request->file('picture')->getClientOriginalName(),
          'extension' => $ext,
          'size'      => $request->file('picture')->getSize(),
          'mime'      => $mime,
          'filename'  => $filename,
          'path'      => $dir
        ];
        $picture = Picture::create($pictureData);

        // Save thumbnail node
        $thumbnailData = [
          'original'  => $request->file('picture')->getClientOriginalName(),
          'extension' => $ext,
          'mime'      => $mime,
          'filename'  => $thumbname,
          'path'      => $thumbdir
        ];
        $thumbnail = Thumbnail::create($thumbnailData);
        $rel = $picture->thumbnail()->save($thumbnail);

      return response()->json([
        'status'    => 200,
        'data'      => $picture,
        'heading'   => 'Picture',
        'messages'  => ['File was uploaded']
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Picture',
        'messages'  => ['Upload failed', $e->getMessage()]
      ], 400);
    }
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
      $picture = Picture::with($rels)->where('uuid', $id)->get();
    } else {
      $picture = Picture::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $picture,
      'heading'   => 'Picture',
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
