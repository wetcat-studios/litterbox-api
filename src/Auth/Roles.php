<?php namespace Wetcat\Litterbox\Auth;

/**
 * Helper class for managing role constants and names.
 */
class Roles
{
  
	/**
	 * Get the role name from an integer
	 */
	public static function getRoleName ($roleValue)
	{
		switch ($roleValue) {
      case 5: return 'superadmin';
      case 4: return 'admin';
      case 3: return 'order';
      case 2: return 'user';
      default: return 'guest';
    }  
	}
  
  
  /**
	 * Get the role value from a name
	 */
	public static function getRoleValue ($roleName)
	{
		switch ($roleName) {
      case 'superadmin': return 5;
      case 'admin'     : return 4;
      case 'order'     : return 3;
      case 'user'      : return 2;
      case 'guest'     : return 0;
			default          : return 0;
    }  
	}
	
	
	/**
	 * Verify that the supplied role is part of the array of roles.
	 */
	public static function verify ($role, $level)
	{
		// Test the role as integer
		if (is_int($role)) {
			if (is_int($level)) {
				return ($role >= $level);
			} else {
				return ($role >= self::getRoleValue($level));
			}
		}
		
		// Convert the role name to value
		else {
			$roleVal = self::getRoleValue($role);
			if (is_int($level)) {
				return ($roleVal >= $level);
			} else {
				return ($roleVal >= self::getRoleValue($level));
			}
		}
	}
  
}