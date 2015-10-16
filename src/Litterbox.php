<?php namespace Wetcat\Litterbox;

/*

   Copyright 2015 Andreas Göransson

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.

*/

use Wetcat\Litterbox\Providers\Users\Provider as UserProvider;

/**
 * Starting point for all interactions with the Litterbox API
 */
class Litterbox
{

  /**
   * Provides access to all the Users.
   */
  protected $userProvider;
	

  /**
   * Create a new Neo object.
   */
  public function __construct(
    $alias,
    $schema,
    $host,
    $port,
    $auth,
    $user,
    $pass
  ) {
    $client = ClientBuilder::create()
      ->addConnection($alias, $scheme, $host, $port, $auth, $user, $pass)
      ->setAutoFormatResponse(true)
      ->setDefaultTimeout($timeout)
      ->build();
      
      $this->userProvider = new UserProvider($client);
  }


  /**
   * Get the accounts provider.
   *
   * @return AccountProvider
   */
  public function accounts ()
  {
    return $this->accountProvider;
  }

}
