<?php namespace Wetcat\Litterbox\Middleware;

use Wetcat\Litterbox\Auth\Token as TokenHelper;
use Wetcat\Litterbox\Auth\Roles as RoleHelper;

use Wetcat\Litterbox\Models\User;

/**
 * Super class for all Litterbox middleware, contains helper functions.
 */
class LitterboxMiddleware
{ 
  
  /**
   * Send the json response for a failed request.
   */
  protected function sendFailedResponse ($messages)
  {
    return response()->json([
      'status'    => 401,
      'data'      => [],
      'heading'   => null,
      'messages'  => $messages,
    ], 401);
  }

}