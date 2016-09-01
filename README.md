# Laravel Route Restrictor

Laravel 5.1 Middleware to restrict a site or specific routes using HTTP basic authentication

## Setup

1. Add `"divineomega/laravel-route-restrictor": "1.*"` to the `require` section of your `composer.json` file.
2. Run `composer update divineomega/laravel-route-restrictor` (or just `composer update`).
3. Add `\DivineOmega\LaravelRouteRestrictor\Http\Middleware\BasicAuthentication::class` to the `$middleware` array in your `app/Http/Kernel.php` file.
4. Add `RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization}]` immediately below `RewriteEngine On` in your `public/.htaccess` file. This is required for web servers that are configured to use CGI as their PHP handler.

## Global restriction

In order to restrict all routes in your Laravel application, add the following to your `.env` file.

```
ROUTE_RESTRICTOR_GLOBAL_USERNAME=cat
ROUTE_RESTRICTOR_GLOBAL_PASSWORD=hat
```

## Restricting specific routes

To restrict specific routes, you must be using [named routes](https://laravel.com/docs/5.1/routing#named-routes). Once you have a named route you wish to restrict, add the following to your `.env` file.

```
ROUTE_RESTRICTOR_ROUTE_MYNAMEDROUTE_USERNAME=fat
ROUTE_RESTRICTOR_ROUTE_MYNAMEDROUTE_PASSWORD=cat
```
Replace `MYNAMEDROUTE` with your route's name, capitalised. You can add as many route specific restrictions as you wish by repeating the above section as required.

Note: If you have both route specific restrictions and a global restriction, both will work, but route specific restrictions will take priority.
