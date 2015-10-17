Under development!

#install

This assumes you've already installed Laravel 5.1.19 (neoeloquent is not compatible with later changes to the builder in Laravel)

1. Install package with `composer require wetcat/litterbox-api dev-master`

2. Add providers

```php
Vinelab\NeoEloquent\NeoEloquentServiceProvider::class,
Wetcat\Litterbox\LitterboxServiceProvider::class,
```

3. Publish config `php artisan vendor:publish` and modify to suit your Neo server.

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
    'litterbox-auth' => \Wetcat\Litterbox\Middleware\Auth::class,
    'litterbox-storage' => \Wetcat\Litterbox\Middleware\Order::class,
    'litterbox-admin' => \Wetcat\Litterbox\Middleware\Admin::class,
    'litterbox-superadmin' => \Wetcat\Litterbox\Middleware\Superadmin::class,
];
```