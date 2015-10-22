<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Campaign;
use Wetcat\Litterbox\Models\Brand;
use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Category;
use Wetcat\Litterbox\Models\Picture;

use Ramsey\Uuid\Uuid;

class CampaignController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $campaigns = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $campaigns = Campaign::with($rels)->get();
    } else {
      $campaigns = Campaign::all();
    }

    if ($request->has('query')) {
      $query = $request->input('query');

      $filterable = $campaigns->toArray();

      $campaigns = array_filter($filterable, function ($campaign) use ($query) {
        return (stripos($campaign['name'], $query) !== false);
      });
    }

    if ($request->has('formatted')) {
      if ($request->input('formatted') === 'semantic') {
        $out = [];
        foreach ($campaigns as $campaign) {
          $out[] = [
            'name' => (is_object($campaign) ? $campaign->name : $campaign['name']),
            'value' => (is_object($campaign) ? $campaign->uuid : $campaign['uuid'])
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
      'data'      => $campaigns,
      'heading'   => 'Campaign',
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
   * @param  Request  $request
   * @return Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name'      => 'required|string',
      'starts'    => 'required|date',
      'ends'      => 'required|date',
      'brand'     => 'string', // UUID
      'articles'  => 'string', // CSV, UUID
      'category'  => 'string', // UUID
      'rebate'    => 'integer',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Campaign',
        'messages'  => $messages
      ], 400);
    }

    $campaignData = [
      'uuid'    => Uuid::uuid4()->toString(),
      'name'    => $request->input('name'),
      'starts'  => $request->input('starts'),
      'ends'    => $request->input('ends'),
      'rebate'  => $request->input('rebate'),
    ];

    $campaign = Campaign::create($campaignData);

    if ($request->has('brand') && Uuid::isValid($request->input('brand'))) {
      $brand = Brand::where('uuid', $request->input('brand'))->first();
      $rel = $brand->campaigns()->save($campaign);
    }

    if ($request->has('category') && Uuid::isValid($request->input('category'))) {
      $category = Category::where('uuid', $request->input('category'))->first();
      $rel = $category->campaigns()->save($category);
    }

    if ($request->has('articles')) {
      $articles = explode(',', $request->input('articles'));
      foreach ($articles as $article) {
        if (Uuid::isValid($article)) {
          $art = Article::where('uuid', $article)->first();
          $rel = $art->campaigns()->save($campaign);
        }
      }
    }

    // Attach the picture
    if ($request->has('filename')) {
      // Single or multiple pictures?
      if (strpos($request->input('filename'), ',') !== false) {
        // Multiple files
        $filenames = explode(',', $request->input('filename'));
        foreach ($filenames as $filename) {
          $picture = Picture::where('filename', $filename)->first();
          if (!!$picture) {
            $rel = $campaign->pictures()->save($picture);
          }  
        }
      } else {
        // Single file
        $picture = Picture::where('filename', $request->input('filename'))->first();
        if (!!$picture) {
          $rel = $campaign->pictures()->save($picture);
        }
      }
    }

    return response()->json([
      'status'    => 201,
      'data'      => $campaign,
      'heading'   => 'Campaign',
      'message'   => ['Campaign created.'],
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
   * @param  Request  $request
   * @param  int  $id
   * @return Response
   */
  public function update(Request $request, $id)
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
