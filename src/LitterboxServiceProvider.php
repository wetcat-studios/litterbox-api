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

use Illuminate\Support\ServiceProvider;

use Config;

class LitterboxServiceProvider extends ServiceProvider
{

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;
  
	
	/**
	 * Include the commands
	 */
  protected $commands = [
  ];


  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
		if (!$this->app->routesAreCached()) {
        require __DIR__.'/routes.php';
    }
		
    $this->publishes([
      __DIR__.'/config/config.php' => config_path('litterbox.php'),
    ]);
  }


  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__.'/config/config.php', 'litterbox'
    );
    
    $this->commands($this->commands);

    $this->registerLitterbox();
  }


  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides()
  {
    return [];
  }


  /**
   * Creates a new Litterbox object
   *
   * @return void
   */
  protected function registerLitterbox()
  {
    $this->app->singleton('Wetcat\Litterbox\Litterbox', function ($app) 
    {
      $alias    = Config::get('litterbox.default.alias', Config::get('litterbox::default.alias'));
      $schema   = Config::get('litterbox.default.schema', Config::get('litterbox::default.schema'));
      $host     = Config::get('litterbox.default.host', Config::get('litterbox::default.host'));
      $port     = Config::get('litterbox.default.port', Config::get('litterbox::default.port'));
      $auth     = Config::get('litterbox.default.auth', Config::get('litterbox::default.auth'));
      $user     = Config::get('litterbox.default.user', Config::get('litterbox::default.user'));
      $pass     = Config::get('litterbox.default.pass', Config::get('litterbox::default.pass'));

      return new Litterbox(
        $alias,
        $schema,
        $host,
        $port,
        $auth,
        $user,
        $pass
      );
    });
  }

}
