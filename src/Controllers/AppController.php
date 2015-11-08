<?php namespace Wetcat\Litterbox\Controllers;

use Wetcat\Litterbox\Requests;
use Wetcat\Litterbox\Controllers\Controller;

use Illuminate\Http\Request;
use Validator;

use anlutro\LaravelSettings\SettingStore as SettingStore;

class AppController extends Controller {

  protected $settings;

  public function __construct(SettingStore $settings)
  {
    $this->settings = $settings;
  }


  /**
   * Read application wide settings
   */
  public function index (Request $request)
  {
    return response()->json([
      'status'    => 200,
      'data'      => $this->settings->all(),
      'heading'   => 'Settings',
      'messages'  => []
    ], 200);
  }
  

  /**
   * Store application wide settings.
   */
  public function store (Request $request)
  {
    $validator = Validator::make($request->all(), [
      'customermarkup' => 'numeric',
      'expectedmargin' => 'numeric',
    ]);
    if ($validator->fails()) {
      $messages = [];
      foreach ($validator->errors()->all() as $message) {
        $messages[] = $message;
      }
      return response()->json([
        'status'    => 400,
        'data'      => null,
        'heading'   => 'Settings',
        'messages'  => $messages
      ], 400);
    }

    if ($request->has('customermarkup')) {
      $this->settings->set('customermarkup', $request->input('customermarkup'));
    }

    if ($request->has('expectedmargin')) {
      $this->settings->set('expectedmargin', $request->input('expectedmargin'));
    }

    // We made it! Send a success!
    return response()->json([
      'status'    => 201,
      'data'      => $this->settings->all(),
      'heading'   => 'Settings',
      'messages'  => []
    ], 201);
  }

}
