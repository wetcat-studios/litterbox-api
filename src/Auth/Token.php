<?php namespace Wetcat\Litterbox\Auth;

use Wetcat\Litterbox\Models\User;

use Ramsey\Uuid\Uuid;

/**
 * Token helper functions
 */
class Token
{
  
  /**
   * Compose a new token from a user model.
   */
  public static function composeToken (\Wetcat\Litterbox\Models\User $fromUser)
  {
    // Set the new token - time based uuid
    $fromUser->token = Uuid::uuid1()->toString();
    $fromUser->save();
      
    // Header
    $header = [
      'alg' => 'AES-256-CBC',
      'typ' => 'litterbox',
    ];
    $header_b64 = urlencode(base64_encode(json_encode($header)));
    
    // Data
    $data = [
      'uuid'  => $fromUser->uuid,
      'name'  => $fromUser->firstname . ' ' . $fromUser->lastname,
      'role'  => Roles::getRoleName($fromUser->role),
    ];
    $data_b64 = urlencode(base64_encode(json_encode($data)));
    
    $secret = urlencode(base64_encode($fromUser->token));
    
    return $header_b64.'.'.$data_b64.'.'.$secret;  
  }
  
  
  /**
   * Verify the contents of the token header
   */
  public static function verifyHeader (array $header)
  {
    if (!array_key_exists('alg', $header) || !array_key_exists('typ', $header)) {
      return false;
    }
    return (strcmp($header['alg'], 'AES-256-CBC') === 0 && strcmp($header['typ'], 'litterbox') === 0); 
  }
  
  
  /**
   * Verify the contents of the data
   */
  public static function verifyData (array $data)
  {
    if (!array_key_exists('uuid', $data) || !array_key_exists('name', $data) || !array_key_exists('role', $data)) {
      return false;
    }
    return Uuid::isValid($data['uuid']);
  }


  /**
   * Get the secret from the token
   */
  public static function getSecret ($token)
  {
    $parts = explode('.', $token);

    return base64_decode(urldecode($parts[2]));
  }

}