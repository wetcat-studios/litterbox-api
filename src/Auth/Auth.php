<?php namespace Wetcat\Litterbox\Auth;

use Wetcat\Litterbox\Models\User;

use Ramsey\Uuid\Uuid;

use Hash;

use Wetcat\Litterbox\Auth\Token as TokenHelper;
use Wetcat\Litterbox\Auth\Roles as RoleHelper;

/**
 * Auth helper
 */
class Auth
{
  
	public static function checkCredentials ($email, $password)
	{
    $user = User::where('email', $email)->first();

		if (is_null($user)) {
			return null;
		}
		
		if (Hash::check($password, $user->password)) {
			return $user;
		}
		
		return null;
	}

	/**
	 * This REQUIRES the token to be verified before use.
	 */
	public static function getUser ($token)
	{ 
    $parts = explode('.', $token);
    
    // Decode the header
    $header = json_decode(base64_decode(urldecode($parts[0])), true);
    
    // Decode the data
    $data = json_decode(base64_decode(urldecode($parts[1])), true);

    // Get the secret
    $secret = base64_decode(urldecode($parts[2]));
    
    // Get the user for this token
    $user = User::where('uuid', $data['uuid'])->first();
    
    return $user;
	}

	/**
   * Verify the Litterbox Token setup and validity.
   *
   * @returns The role
   */
  public static function verify ($token)
  {
    // If token is missing just dissallow the request
    if (is_null($token)) {
    	throw new \Exception('You need to be authenticated to access this resource.');
      //return $this->sendFailedResponse([]);
    }
    
    $parts = explode('.', $token);
    
    // Make sure the token has correct size
    if (count($parts) < 3) {
    	throw new \Exception('Poorly composed token.');
      //return $this->sendFailedResponse([]);
    }
    
    // Decode the header
    $header = json_decode(base64_decode(urldecode($parts[0])), true);
    if (is_null($header) || !TokenHelper::verifyHeader($header)) {
    	throw new \Exception('Poorly compsed header.');
      //return $this->sendFailedResponse(['Poorly compsed header.']);
    }
    
    // Decode the data
    $data = json_decode(base64_decode(urldecode($parts[1])), true);
    if (is_null($data) || !TokenHelper::verifyData($data)) {
    	throw new \Exception('Poorly composed data.');
      //return $this->sendFailedResponse(['Poorly composed data.']);
    }

    // Get the secret
    $secret = base64_decode(urldecode($parts[2]));
    
    // Get the user for this token
    //$user = User::where('uuid', $data['uuid'])->first();
    
    // Verify the secret
    $user = User::where('token', $secret)->first();

    if (is_null($user)) {
    	throw new \Exception('User does not exist.');
      //return $this->sendFailedResponse(['User does not exist.']);
    }

    // Verify that this is the uuid
    if (strcmp($user->uuid, $data['uuid']) !== 0) {
    	throw new \Exception('Token mismatch.');
    	//return $this->sendFailedResponse(['Token mismatch.']);
    }

    // Compare a few key variables to verify the validity of the token
    if (!strcmp($user->uuid, $secret)) {
    	throw new \Exception('Token data is incorrect.');
      //return $this->sendFailedResponse(['Token data is incorrect.']);
    }
    if (strcmp($user->firstname.' '.$user->lastname, $data['name']) !== 0) {
    	throw new \Exception('Name data is incorrect.');
      //return $this->sendFailedResponse(['Name data is incorrect.']);
    }
    if (strcmp(RoleHelper::getRoleName($user->role), $data['role']) !== 0) {
    	throw new \Exception('Role data is incorrect.');
      //return $this->sendFailedResponse(['Role data is incorrect.']);
    }
    
    return $user->role;
  }
}