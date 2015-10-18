<?php namespace Wetcat\Litterbox\Middleware;

use Wetcat\Litterbox\Auth\Token as TokenHelper;

use Wetcat\Litterbox\Models\User;

/**
 * Super class for all Litterbox middleware, contains helper functions.
 */
class LitterboxMiddleware
{
  
  /**
   * Verify the Litterbox Token setup and validity.
   *
   * @returns The role
   */
  protected function verify ($token)
  {
    // If token is missing just dissallow the request
    if (is_null($token)) {
      return $this->sendFailedResponse(['You need to be authenticated to access this resource.']);
    }
    
    $parts = explode('.', $token);
    
    // Make sure the token has correct size
    if (count($parts) < 3) {
      return $this->sendFailedResponse(['Poorly composed token.']);
    }
    
    // Decode the header
    $header = json_decode(base64_decode(urldecode($parts[0])), true);
    if (is_null($header) || !TokenHelper::verifyHeader($header)) {
      return $this->sendFailedResponse(['Poorly compsed header.']);
    }
    
    // Decode the data
    $data = json_decode(base64_decode(urldecode($parts[1])), true);
    if (is_null($data) || !TokenHelper::verifyData($data)) {
      return $this->sendFailedResponse(['Poorly composed data.']);
    }
    
    // Get the secret
    $secret = base64_decode(urldecode($parts[2]));
    
    // Get the user for this token
    $user = User::where('uuid', $data['uuid'])->first();
    
    // Compare a few key variables to verify the validity of the token
    if (!strcmp($user->uuid, $secret)) {
      return $this->sendFailedResponse(['Token data is incorrect.']);
    }
    if (strcmp($user->firstname.' '.$user->lastname, $data['name']) !== 0) {
      return $this->sendFailedResponse(['Name data is incorrect.']);
    }
    if (strcmp($user->role, $data['role']) !== 0) {
      return $this->sendFailedResponse(['Role data is incorrect.']);
    }
    
    return $user->role;
  }
  
  
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