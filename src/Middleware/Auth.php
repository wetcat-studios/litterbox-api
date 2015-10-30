<?php namespace Wetcat\Litterbox\Middleware;

use Closure;

use Wetcat\Litterbox\Auth\Auth as AuthHelper;

class Auth extends LitterboxMiddleware
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
    $token = $request->header('X-Litterbox-Token');
    
    // Verify that the user is authenticated using helper method
    try {
      AuthHelper::verify($token);
    } catch (\Exception $exception) {
      return $this->sendFailedResponse(['You need to be authenticated to access this resource.']);
    }
    
    return $next($request);
  }

}