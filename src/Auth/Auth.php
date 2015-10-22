<?php namespace Wetcat\Litterbox\Auth;

use Wetcat\Litterbox\Models\User;

use Ramsey\Uuid\Uuid;

use Hash;

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

}