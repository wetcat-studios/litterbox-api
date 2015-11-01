Under development!

#install

This assumes you've already installed Laravel 5.1.19 (neoeloquent is not compatible with later changes to the builder in Laravel)

1. Install package with `composer require wetcat/litterbox-api dev-master`

2. Add provider

```php
Wetcat\Litterbox\LitterboxServiceProvider::class,
```

3. Add facades

```php
'Image' => Intervention\Image\Facades\Image::class,
```

3. Publish config `php artisan vendor:publish` and modify to suit your Neo server.

4. Update configs.

4. Add datbase settings

```php
'default' => 'neo4j',
```

```php
'connections' => [
  'neo4j' => [
    'driver' => 'neo4j',
    'host'   => 'localhost',
    'port'   => '7474',
    'username' => null,
    'password' => null
  ]
]
```

5. Register middleware in `App\Http\Kernel.php`

```php
protected $routeMiddleware = [
  'cors'                  => Barryvdh\Cors\HandleCors::class,
  'litterbox-guest'       => \Wetcat\Litterbox\Middleware\Guest::class,
  'litterbox-auth'        => \Wetcat\Litterbox\Middleware\Auth::class,
  'litterbox-order'     => \Wetcat\Litterbox\Middleware\Order::class,
  'litterbox-admin'       => \Wetcat\Litterbox\Middleware\Admin::class,
  'litterbox-superadmin'  => \Wetcat\Litterbox\Middleware\Superadmin::class,
];
```

6. Set up CORS `app\config\cors.php`

```php
return [
  'supportsCredentials' => false,
  'allowedOrigins' => ['*'],
  'allowedHeaders' => ['Content-Type', 'Accept', 'X-Litterbox-Token'],
  'allowedMethods' => ['GET', 'POST', 'PUT',  'DELETE', 'OPTIONS'],
  'exposedHeaders' => [],
  'maxAge' => 0,
  'hosts' => [],  
]
```

7. Make sure to disable CSRF tokens in `app\Http\Kernel.php`

```php
protected $middleware = [
    \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    \App\Http\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    //\App\Http\Middleware\VerifyCsrfToken::class,
];
```