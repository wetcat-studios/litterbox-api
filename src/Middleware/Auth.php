<?php namespace Wetcat\Litterbox\Middleware;

use Closure;



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
    $this->verify($token);
    
    return $next($request);
  }

}