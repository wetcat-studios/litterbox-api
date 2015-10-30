<?php namespace Wetcat\Litterbox\Middleware;

use Closure;

use Wetcat\Litterbox\Auth\Roles as RoleHelper;
use Wetcat\Litterbox\Auth\Auth as AuthHelper;

class Superadmin extends LitterboxMiddleware
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
      $role = AuthHelper::verify($token);
    } catch (\Exception $exception) {
      $this->sendFailedResponse($exception->getMessage());
    }
    
    // Verify that the user has the correct role
    if ( !RoleHelper::verify($role, 'superadmin') ){
      return $this->sendFailedResponse(['You don\'t have permission to access this resource.']);
    }
    
    return $next($request);
  }

}