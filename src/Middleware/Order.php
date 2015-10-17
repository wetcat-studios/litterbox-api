<?php namespace Wetcat\Litterbox\Middleware;

use Closure;

class Order
{
  
  protected $roles = ['order', 'admin', 'superadmin'];
  
  /**
    * Run the request filter.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
  public function handle(\Illuminate\Http\Request $request, Closure $next)
  {
      $token = $request->header('X-Auth-Token');
      
      // If there is no token, just dissallow the access request.
      if (is_null($token)) {
        return $this->sendFailedResponse(['You need to be authenticated to access this resource.']);
      }
      
      // Or attempt to authenticate the user.
      else {
        $user = User::where('token', $token)->first();
        if (!!$user) {
          // Check that the user has 'order' role or more
          if (!in_array($user->role, $roles)) {
            return $this->sendFailedResponse(['You don\'t have permission to access this resource.']);
          }
        } else {
          return $this->sendFailedResponse(['Bad authentication token.']);
        }
      }
  }
  
  
  /**
    * Send the json response for a failed request.
    */
  private function sendFailedResponse ($messages)
  {
    return response()->json([
      'status'    => 401,
      'data'      => [],
      'heading'   => null,
      'messages'  => $messages,
    ], 401);
  }

}