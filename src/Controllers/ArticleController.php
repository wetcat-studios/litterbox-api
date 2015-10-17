<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Article;
use Wetcat\Litterbox\Models\Category;
use Wetcat\Litterbox\Models\Brand;
use Wetcat\Litterbox\Models\Manufacturer;
use Wetcat\Litterbox\Models\Currency;
use Wetcat\Litterbox\Models\Picture;
use Wetcat\Litterbox\Models\Segment;
use Wetcat\Litterbox\Models\Ingredient;

use Rhumsaa\Uuid\Uuid;

class ArticleController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index(Request $request)
  {
    $articles = [];

    if ($request->has('rel')) {
      $rels = explode('_', $request->input('rel'));
      $articles = Article::with($rels)->get();
    } else {
      $articles = Article::all();
    }

    // Attach the stock numbers (incomming, outgoing, total)
    foreach ($articles as $article) {
      $article->incomming = $article->incomming();
      $article->outgoing = $article->outgoing();
      $article->total = $article->total();
    }

    if ($request->has('query')) {
      $query = $request->input('query');

      $filterable = $articles->toArray();

      $articles = array_filter($filterable, function ($article) use ($query) {
        return (stripos($article['name'], $query) !== false);
      });
    }

    if ($request->has('formatted')) {
      if ($request->input('formatted') === 'semantic') {
        $out = [];
        foreach ($articles as $article) {
          $out[] = [
            'name' => (is_object($article) ? $article->name : $article['name']),
            'value' => (is_object($article) ? $article->uuid : $article['uuid'])
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
      'data'      => $articles,
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
    // Not used
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      // Article base validation
      'name'    => 'required|string',
      'number'  => 'required',
      'ean'     => 'required|string',
      'price_in'      => 'required',
      'discountrate'  => 'integer',
      'restock_threshold' => 'required|integer',
      'restock_amount'    => 'required|integer',
//      'expired'         => 'required',
//      'sustainability'    => 'required|integer', // Moved to segment
      'filename'             => 'string',

      // Package (one single unit)
      'package_weight'  => 'required',
      'package_width'   => 'required',
      'package_length'  => 'required',
      'package_height'  => 'required',
      
      // Colli (a full set of units)
      'colli_weight'  => 'required',
      'colli_width'   => 'required',
      'colli_length'  => 'required',
      'colli_height'  => 'required',

      // Packaging units
      'package_per_colli'     => 'required|integer',
      'colli_per_eu_pallet'   => 'required|integer',
      'colli_per_eu_lav'      => 'required|integer',
      'colli_per_half_pallet' => 'required|integer',
      'colli_per_half_lav'    => 'required|integer',
      'colli_per_ship_pallet' => 'required|integer',
      'colli_per_ship_lav'    => 'required|integer',
      
      // Nutrients
      'nutrients_energy'        => 'required',
      'nutrients_fat'           => 'required',
      'nutrients_saturatedfat'  => 'required',
      'nutrients_carbs'         => 'required',
      'nutrients_sugar'         => 'required',
      'nutrients_fibers'        => 'required',
      'nutrients_protein'       => 'required',
      'nutrients_salt'          => 'required',
      
      // Ingredients (comma separated text)
      'ingredients' => 'string',

      // Category validation (just the uuid)
      'category'  => 'required|string',

      // Brand validation (just the uuid)
      'brand' => 'required|string',

      // Manufacturer validation
      'manufacturer'     => 'required|string',
      'manufacturer_number' => 'required|string',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Article',
        'messages'  => $messages
      ], 400);
    }

    $articleData = $request->except('category_vat', 'manufacturer_shipping', 'manufacturer_margin', 'price_out', 'price_recommended');
    $articleData['uuid'] = Uuid::uuid4()->toString();

    // Create article (the model will select which values we will fill with...)
    $article = Article::create($articleData);
    
    // Find and connect to Category
    $categories = explode(',', $request->input('category'));
    foreach ($categories as $category) {
      if (Uuid::isValid($category)) {
        $category = Category::where('uuid', $category)->first();
        $rel = $category->articles()->save($article);
      }
    }

    // Find and connect to Brand
    $brandId = $request->input('brand');
    $brand = Brand::where('uuid', $brandId)->firstOrFail();
    $relation = $brand->articles()->save($article);

    // Find and connect to Segment
    $segmentId = $request->input('segment');
    $segment = Segment::where('uuid', $segmentId)->firstOrFail();
    $relation = $segment->articles()->save($article);

    // Find and connect to manufacturer (also add the manufacturer article number to relation)
    $manufacturerId = $request->input('manufacturer');
    $manufacturer = Manufacturer::where('uuid', $manufacturerId)->firstOrFail();
    $relation = $manufacturer->articles()->save($article);
    
    $relation->number = $request->input('manufacturer_number');
    $relation->save();

    // Find and connect to Currency
//    $currencyId = $request->input('currency');
//    $currency = Currency::where('uuid', $currencyId)->firstOrFail();
//    $relation = $currency->articles()->save($article);

    // Save link to all ingredients
    if ($request->has('ingredients')) {
      $ingredients = explode(',', $request->input('ingredients'));
      foreach ($ingredients as $value) {
        $ingredient = null;

        if (Uuid::isValid(trim($value))) {
          $ingredient = Ingredient::where('uuid', trim($value))->first();
        } else {
          $ingredientData = [
            'name'  => trim($value),
            'uuid'  => Uuid::uuid4()->toString()
          ];

          $ingredient = Ingredient::create($ingredientData);

          $messages[] = 'Created ingredient ' . $ingredient->name . '.';
        }

        $article->ingredients()->save($ingredient);
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
            $rel = $article->pictures()->save($picture);
          }  
        }
      } else {
        // Single file
        $picture = Picture::where('filename', $request->input('filename'))->first();
        if (!!$picture) {
          $rel = $article->pictures()->save($picture);
        }
      }

      
    }

    // We made it! Send a success!
    return response()->json([
      'status'    => 201,
      'data'      => $article,
      'heading'   => 'Article',
      'messages'  => ['Article was created']
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
