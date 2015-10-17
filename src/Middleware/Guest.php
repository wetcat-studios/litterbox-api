<?php namespace Wetcat\Litterbox\Middleware;

use Closure;

use Wetcat\Litterbox\Models\User;

class Guest
{
  
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
    
    // If there is a token, make sure it's not valid!
    if (!is_null($token)) {
      $user = User::where('token', $token)->first();
      if (!!$user) {
        return $this->sendFailedResponse(['Resource is only intended for guests.']);
      } else {
        // Okay, no valid token so we just accept the guest request.
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