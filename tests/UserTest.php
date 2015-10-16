<?php

use Wetcat\Litterbox\Models\User;

class UserTest extends PHPUnit_Framework_TestCase
{
	
	/** @test */
	function a_user_has_a_firstname_and_a_lastname ()
	{
		$user = new User('Andreas', 'Göransson', 'andreasgoransson0@gmail.com', 'asd123');
		
		$this->assertEquals('Andreas', $user->firstname());
		$this->assertEquals('Göransson', $user->lastname());
		$this->assertEquals('andreasgoransson0@gmail.com', $user->email());
		$this->assertEquals('asd123', $user->password());
	}


	/** @test */
	function a_user_has_a_role ()
	{
		
	}
	
}