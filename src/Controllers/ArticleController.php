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
use Wetcat\Litterbox\Models\Customer;
use Wetcat\Litterbox\Models\Intrastat;

use Ramsey\Uuid\Uuid;

class ArticleController extends Controller {

  /**
   * Instantiate a new UserController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('litterbox-auth');
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
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
      'price'         => 'required',
      'discountrate'  => 'integer',
      'restockthreshold' => 'integer',
      'restockamount'    => 'integer',
//      'expired'         => 'required',
//      'sustainability'    => 'required|integer', // Moved to segment
      'filename'        => 'string',
      'intrastat'       => 'string',
      'description'     => 'string', // Optional description
      
      // Package (one single unit) // None of these 
//      'packageweight'  => 'required',
//      'packagewidth'   => 'required',
//      'packagelength'  => 'required',
//      'packageheight'  => 'required',
      
      // Colli (a full set of units)
//      'colliweight'  => 'required',
//      'colliwidth'   => 'required',
//      'collilength'  => 'required',
//      'colliheight'  => 'required',

      // Packaging units
      'packagepercolli'    => 'integer',
      'collipereupallet'   => 'integer',
      'collipereulav'      => 'integer',
      'colliperhalfpallet' => 'integer',
      'colliperhalflav'    => 'integer',
      'collipershippallet' => 'integer',
      'collipershiplav'    => 'integer',
      
      // Nutrients
      'kj'            => 'numeric',
      'kcal'          => 'numeric',
      'fat'           => 'numeric',
      'saturatedfat'  => 'numeric',
      'carbs'         => 'numeric',
      'sugar'         => 'numeric',
      'fibers'        => 'numeric',
      'proteins'      => 'numeric',
      'salt'          => 'numeric',
      
      // Ingredients (comma separated text)
      'ingredients' => 'string',

      // Category validation
      'category'    => 'required|string', // Single category (primary)
      'categories'  => 'string', // This is a CSV string of "categories"

      // Brand validation (just the uuid)
      'brand' => 'required|string',

      // Manufacturer validation
      'manufacturer'     => 'required|string',
      'manufacturernumber' => 'string',
      
      // Customer links
      'customers'   => 'string', // UUID for the selected customers
      
      // These are calculated once when the article is created, they can also
      // be updated whenever certain variables in the system are changed.
      'productCost' => 'numeric|required', 
      'unitPrice'   => 'numeric|required',
      'salesPrice'  => 'numeric|required',
      'calculatedMargin' => 'numeric|required',
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

    // Create the article on all data, except the links to other nodes
    $articleData = $request->except('filename', 'category', 'categories', 'segment', 'brand', 'manufacturer', 'ingredients', 'customers');
    $articleData['uuid'] = Uuid::uuid4()->toString();

    // Create article (the model will select which values we will fill with...)
    $article = Article::create($articleData);
    
    // Find and connect to Primary Category
    if (Uuid::isValid($request->input('category'))) {
      $category = Category::where('uuid', $request->input('category'))->first();
      $rel = $category->articles()->save($article);
      $rel->type = 'primary';
      $rel->save();
    } else {
      // Or if it's a string - create
      $category = Category::where('name', $request->input('category'))->first();
      if (!!$category) {
        $rel = $category->articles()->save($article);
        $rel->type = 'primary';
        $rel->save();
      } else {
        $category = Category::create([
          'uuid'  => Uuid::uuid4()->toString(),
          'name'  => $request->input('category')
        ]);
        $rel = $category->articles()->save($article);
        $rel->type = 'primary';
        $rel->save();
      }
    }

    // Find and connect to Secondary categories
    if ($request->has('categories')) {
      $categories = explode(',', $request->input('categories'));
      foreach ($categories as $cat) {
        if (Uuid::isValid($cat)) {
          $category = Category::where('uuid', $cat)->first();
          $rel = $category->articles()->save($article);
          $rel->type = 'secondary';
          $rel->save();
        } else {
          // Or if it's a string - create
          $category = Category::where('name', $cat)->first();
          if (!!$category) {
            $rel = $category->articles()->save($article);
            $rel->type = 'secondary';
            $rel->save();
          } else {
            $category = Category::create([
              'uuid'  => Uuid::uuid4()->toString(),
              'name'  => $cat
            ]);
            $rel = $category->articles()->save($article);
            $rel->type = 'secondary';
            $rel->save();
          }
        }
      }
    }
    
    // Find and connect to customers
    if ($request->has('customers')) {
      $customers = explode(',', $request->input('customers'));
      foreach ($customers as $customer) {
        if (Uuid::isValid($customer)) {
          $customerNode = Customer::where('uuid', $customer)->first();
          $rel = $customerNode->articles()->save($article);
          $rel->save();
        } else {
          // Can't be anything but a valid UUID!'
        }
      }
    }
    
    // Find and connect to Intrastat
    $intrastatId = $request->input('intrastat');
    $intrastat = Intrastat::where('uuid', $intrastatId)->first();
    $relation = $intrastat->articles()->save($article);
    
    // Find and connect to Brand
    $brandId = $request->input('brand');
    $brand = Brand::where('uuid', $brandId)->firstOrFail();
    $relation = $brand->articles()->save($article);

    // Find and connect to Segment
    $segmentId = $request->input('segment');
    $segment = Segment::where('uuid', $segmentId)->firstOrFail();
    $relation = $segment->articles()->save($article);

    // Find and connect to manufacturer (also add the manufacturer article number to relation)
    $manufacturerString = $request->input('manufacturer');
    // If the manufacturer string is a UUID the manufacturer should just be linked...
    if (Uuid::isValid(trim($manufacturerString))) {
      $manufacturer = Manufacturer::where('uuid', trim($manufacturerString))->first();
    }
    // ...otherwise create a new manufacturer node with just a name and uuid
    else {
      $manufacturer = Manufacturer::create([
        'uuid'  => Uuid::uuid4()->toString(),
        'name'  => $manufacturerString
      ]);
    }
    $rel = $manufacturer->articles()->save($article);
    
    // Save the number to the relation
    $relation->number = $request->input('manufacturernumber');
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
    if ($request->has('rel')) {
      $article = Article::with($rels)->where('uuid', $id)->get();
    } else {
      $article = Article::where('uuid', $id)->get();
    }

    return response()->json([
      'status'    => 200,
      'data'      => $article,
      'heading'   => 'Article',
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
  public function destroy($uuid)
  {
    $article = Article::where('uuid', $uuid)->first();

    $article->delete();

    return response()->json([
      'status'    => 200,
      'data'      => $article,
      'heading'   => 'Article',
      'messages'  => ['Article ' . $article->name . ' deleted.']
    ], 200);
  }

}
