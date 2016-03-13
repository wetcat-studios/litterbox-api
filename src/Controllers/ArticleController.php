<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;
use Input;

use Wetcat\Litterbox\Models\Article;

use Ramsey\Uuid\Uuid;

class ArticleController extends Controller {


  public function __construct()
  {
    $this->middleware('litterbox-auth', ['only' => ['store', 'update', 'destroy']]);
    $this->middleware('litterbox-admin', ['only' => ['store', 'update', 'destroy']]);
  }
  
  
  public function index (Request $request)
  {
    $articles = Article::all();
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $articles->toArray(),
    ], 200);
  }


  public function show (Request $request, $articleId)
  {
    $article = Article::where('uuid', $articleId)->first();

    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Article was not found'],
      ], 404);
    }
    
    return response()->json([
      'status'  =>  200,
      'data'    =>  $article,
    ], 200);
  }
  
    
  public function store (Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name'              =>  'string|required',
      'articleNumber'     =>  'string|required',
      'ean'               =>  'string|required',
      'discountRate'      =>  'integer',
      'restockThreshold'  =>  'integer',
      'restockAmount'     =>  'integer',
      'filename'          =>  'string',
      'intrastat'         =>  'string',
      'description'       =>  'string',
      'packages'          =>  'string',
      
      'packageWeight'     =>  'string',
      'packageWidth'      =>  'string',
      'packageLength'     =>  'string',
      'packageHeight'     =>  'string',
      
      'colliWeight'       =>  'string',
      'colliWidth'        =>  'string',
      'colliLength'       =>  'string',
      'colliHeight'       =>  'string',
      
      'colliPerEuPallet'    =>  'string',
      'colliPerEuLav'       =>  'string',
      'colliPerHalfPallet'  =>  'string',
      'colliPerHalfLav'     =>  'string',
      'colliPerShipPallet'  =>  'string',
      'colliPerShipLav'     =>  'string',
      
      'kj'            =>  'string',
      'kcal'          =>  'string',
      'fat'           =>  'string',
      'saturatedFat'  =>  'string',
      'carbs'         =>  'string',
      'sugar'         =>  'string',
      'fibers'        =>  'string',
      'proteins'      =>  'string',
      'salt'          =>  'string',
      
      'productCost'       =>  'string|required',
      'unitPrice'         =>  'string|required',
      'salesPrice'        =>  'string|required',
      'calculatedMargin'  =>  'string|required',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    =>  400,
        'messages'  =>  $messages,
      ], 400);
    }

    $articleData = [
      'uuid'              =>  Uuid::uuid4()->toString(),
      'name'              =>  $request->input('name'),
      'articleNumber'     =>  $request->input('articleNumber'),
      'ean'               =>  $request->input('ean'),
      'productCost'       =>  $request->input('productCost'),
      'unitPrice'         =>  $request->input('unitPrice'),
      'salesPrice'        =>  $request->input('salesPrice'),
      'calculatedMargin'  =>  $request->input('calculatedMargin'),
    ];
    
    if ($request->has('discountRate'))
      $articleData['discountRate'] = $request->input('discountRate');
      
    if ($request->has('restockThreshold'))
      $articleData['restockThreshold'] = $request->input('restockThreshold');
      
    if ($request->has('restockAmount'))
      $articleData['restockAmount'] = $request->input('restockAmount');
      
    if ($request->has('filename'))
      $articleData['filename'] = $request->input('filename');
      
    if ($request->has('intrastat'))
      $articleData['intrastat'] = $request->input('intrastat');
      
    if ($request->has('description'))
      $articleData['description'] = $request->input('description');
      
    if ($request->has('packages'))
      $articleData['packages'] = $request->input('packages');
      
    if ($request->has('packageWeight'))
      $articleData['packageWeight'] = $request->input('packageWeight');
      
    if ($request->has('packageWidth'))
      $articleData['packageWidth'] = $request->input('packageWidth');
      
    if ($request->has('packageLength'))
      $articleData['packageLength'] = $request->input('packageLength');
      
    if ($request->has('packageHeight'))
      $articleData['packageHeight'] = $request->input('packageHeight');
      
    if ($request->has('colliWeight'))
      $articleData['colliWeight'] = $request->input('colliWeight');
      
    if ($request->has('colliWidth'))
      $articleData['colliWidth'] = $request->input('colliWidth');
      
    if ($request->has('packageLength'))
      $articleData['packageLength'] = $request->input('packageLength');
      
    if ($request->has('packageHeight'))
      $articleData['packageHeight'] = $request->input('packageHeight');
      
    if ($request->has('colliPerEuPallet'))
      $articleData['colliPerEuPallet'] = $request->input('colliPerEuPallet');
      
    if ($request->has('colliPerEuLav'))
      $articleData['colliPerEuLav'] = $request->input('colliPerEuLav');
      
    if ($request->has('colliPerHalfPallet'))
      $articleData['colliPerHalfPallet'] = $request->input('colliPerHalfPallet');
      
    if ($request->has('colliPerHalfLav'))
      $articleData['colliPerHalfLav'] = $request->input('colliPerHalfLav');
      
    if ($request->has('colliPerShipPallet'))
      $articleData['colliPerShipPallet'] = $request->input('colliPerShipPallet');
      
    if ($request->has('colliPerShipLav'))
      $articleData['colliPerShipLav'] = $request->input('colliPerShipLav');
      
    if ($request->has('kj'))
      $articleData['kj'] = $request->input('kj');
      
    if ($request->has('kcal'))
      $articleData['kcal'] = $request->input('kcal');
      
    if ($request->has('fat'))
      $articleData['fat'] = $request->input('fat');
      
    if ($request->has('saturatedfat'))
      $articleData['saturatedfat'] = $request->input('saturatedfat');
      
    if ($request->has('carbs'))
      $articleData['carbs'] = $request->input('carbs');
      
    if ($request->has('sugar'))
      $articleData['sugar'] = $request->input('sugar');
      
    if ($request->has('fibers'))
      $articleData['fibers'] = $request->input('fibers');
      
    if ($request->has('proteins'))
      $articleData['proteins'] = $request->input('proteins');
      
    if ($request->has('salt'))
      $articleData['salt'] = $request->input('salt');

    $article = Article::create($articleData);
    
    return response()->json([
      'status'    =>  201,
      'data'      =>  $article,
      'messages'  =>  null
    ], 201);
  }
  
  
  public function update (Request $request, $articleId)
  {
    $validator = Validator::make($request->all(), [
      'name'              =>  'string',
      'articleNumber'     =>  'string',
      'ean'               =>  'string',
      'discountRate'      =>  'integer',
      'restockThreshold'  =>  'integer',
      'restockAmount'     =>  'integer',
      'filename'          =>  'string',
      'intrastat'         =>  'string',
      'description'       =>  'string',
      'packages'          =>  'string',
      
      'packageWeight'     =>  'string',
      'packageWidth'      =>  'string',
      'packageLength'     =>  'string',
      'packageHeight'     =>  'string',
      
      'colliWeight'       =>  'string',
      'colliWidth'        =>  'string',
      'colliLength'       =>  'string',
      'colliHeight'       =>  'string',
      
      'colliPerEuPallet'    =>  'string',
      'colliPerEuLav'       =>  'string',
      'colliPerHalfPallet'  =>  'string',
      'colliPerHalfLav'     =>  'string',
      'colliPerShipPallet'  =>  'string',
      'colliPerShipLav'     =>  'string',
      
      'kj'            =>  'string',
      'kcal'          =>  'string',
      'fat'           =>  'string',
      'saturatedfat'  =>  'string',
      'carbs'         =>  'string',
      'sugar'         =>  'string',
      'fibers'        =>  'string',
      'proteins'      =>  'string',
      'salt'          =>  'string',
      
      'productCost'       =>  'string',
      'unitPrice'         =>  'string',
      'salesPrice'        =>  'string',
      'calculatedMargin'  =>  'string',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'messages'  => $messages
      ], 400);
    }
    
    $article = Article::where('uuid', $articleId)->first();

    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Article was not found'],
      ], 404);
    }
    
    $updatedData = [];
    
    if ($request->has('name'))
      $updatedData['name'] = $request->input('name');
      
    if ($request->has('articleNumber'))
      $updatedData['articleNumber'] = $request->input('articleNumber');
      
    if ($request->has('ean'))
      $updatedData['ean'] = $request->input('ean');
      
    if ($request->has('discountRate'))
      $updatedData['discountRate'] = $request->input('discountRate');
      
    if ($request->has('restockThreshold'))
      $updatedData['restockThreshold'] = $request->input('restockThreshold');
      
    if ($request->has('restockAmount'))
      $updatedData['restockAmount'] = $request->input('restockAmount');
      
    if ($request->has('filename'))
      $updatedData['filename'] = $request->input('filename');
      
    if ($request->has('intrastat'))
      $updatedData['intrastat'] = $request->input('intrastat');
      
    if ($request->has('description'))
      $updatedData['description'] = $request->input('description');
      
    if ($request->has('packages'))
      $updatedData['packages'] = $request->input('packages');
      
    if ($request->has('packageWeight'))
      $updatedData['packageWeight'] = $request->input('packageWeight');
      
    if ($request->has('packageWidth'))
      $updatedData['packageWidth'] = $request->input('packageWidth');
      
    if ($request->has('packageLength'))
      $updatedData['packageLength'] = $request->input('packageLength');
      
    if ($request->has('packageHeight'))
      $updatedData['packageHeight'] = $request->input('packageHeight');
      
    if ($request->has('colliWeight'))
      $updatedData['colliWeight'] = $request->input('colliWeight');
      
    if ($request->has('colliWidth'))
      $updatedData['colliWidth'] = $request->input('colliWidth');
      
    if ($request->has('packageLength'))
      $updatedData['packageLength'] = $request->input('packageLength');
      
    if ($request->has('packageHeight'))
      $updatedData['packageHeight'] = $request->input('packageHeight');
      
    if ($request->has('colliPerEuPallet'))
      $updatedData['colliPerEuPallet'] = $request->input('colliPerEuPallet');
      
    if ($request->has('colliPerEuLav'))
      $updatedData['colliPerEuLav'] = $request->input('colliPerEuLav');
      
    if ($request->has('colliPerHalfPallet'))
      $updatedData['colliPerHalfPallet'] = $request->input('colliPerHalfPallet');
      
    if ($request->has('colliPerHalfLav'))
      $updatedData['colliPerHalfLav'] = $request->input('colliPerHalfLav');
      
    if ($request->has('colliPerShipPallet'))
      $updatedData['colliPerShipPallet'] = $request->input('colliPerShipPallet');
      
    if ($request->has('colliPerShipLav'))
      $updatedData['colliPerShipLav'] = $request->input('colliPerShipLav');
      
    if ($request->has('kj'))
      $updatedData['kj'] = $request->input('kj');
      
    if ($request->has('kcal'))
      $updatedData['kcal'] = $request->input('kcal');
      
    if ($request->has('fat'))
      $updatedData['fat'] = $request->input('fat');
      
    if ($request->has('saturatedfat'))
      $updatedData['saturatedfat'] = $request->input('saturatedfat');
      
    if ($request->has('carbs'))
      $updatedData['carbs'] = $request->input('carbs');
      
    if ($request->has('sugar'))
      $updatedData['sugar'] = $request->input('sugar');
      
    if ($request->has('fibers'))
      $updatedData['fibers'] = $request->input('fibers');
      
    if ($request->has('proteins'))
      $updatedData['proteins'] = $request->input('proteins');
      
    if ($request->has('salt'))
      $updatedData['salt'] = $request->input('salt');
      
    if ($request->has('productCost'))
      $updatedData['productCost'] = $request->input('productCost');
    
    if ($request->has('unitPrice'))
      $updatedData['unitPrice'] = $request->input('unitPrice');
    
    if ($request->has('salesPrice'))
      $updatedData['salesPrice'] = $request->input('salesPrice');
    
    if ($request->has('calculatedMargin'))
      $updatedData['calculatedMargin'] = $request->input('calculatedMargin');
    
    $updated = $article->update($updatedData);
    
    if ($updated) {
      return response()->json([
        'status'    =>  200,
        'messages'  =>  ['Updated article'],
      ], 200);
    } else {
      return response()->json([
        'status'    =>  400,
        'messages'  =>  ['Failed to update article'],
      ], 400);
    }
  }
  
  
  public function destroy ($articleId)
  {
    $article = Article::where('uuid', $articleId)->first();

    if (!$article) {
      return response()->json([
        'status'    =>  404,
        'messages'  =>  ['Article was not found'],
      ], 404);
    }
    
    $article->delete();

    return response()->json([
      'status'    =>  200,
      'messages'  =>  ["Article '$article->name' deleted"],
    ], 200);
  }

}
