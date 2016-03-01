<?php

namespace DivineOmega\LaravelRouteRestrictor\Middleware;

use App;
use Auth;
use Closure;
use Route;
use Response;

class BasicAuthentication
{
  public function handle($request, Closure $next)
  {
    $username = $request->getUser();
    $password = $request->getPassword();

    if (!$this->validate($request,$username, $password)) {
      header('WWW-Authenticate: Basic');
      App::abort(401, 'Unauthorized. Please check your username and password.');
    }
    return $next($request);
  }

  private function validate($request, $user, $password)
  {
    // Get current route name
    // Note: we do not have access to the current route in middleware, because
    // it has not been fully dispatched, therefore we must use the backwards
    // method of finding the route which matches the current request.
    $routeName = Route::getRoutes()->match($request)->getName();

    // If we have a named route
    if ($routeName) {

      // Check if route username and password are set
      if ($routeUsername = env('ROUTE_RESTRICTOR_ROUTE_'.strtoupper($routeName).'_USERNAME') && $routePassword = env('ROUTE_RESTRICTOR_ROUTE_'.strtoupper($routeName).'_PASSWORD')) {

        // Check against route password
        if (trim($user) == $routeUsername && trim($password) == $routePassword) {
          return true;
        } else {
          return false;
        }
      }

    }


    // Check if global username and password are set
    if ($globalUsername = env('ROUTE_RESTRICTOR_GLOBAL_USERNAME') && $globalPassword = env('ROUTE_RESTRICTOR_GLOBAL_PASSWORD')) {

      // Check against global password
      if (trim($user) == $globalUsername && trim($password) == $globalPassword) {
        return true;
      } else {
        return false;
      }

    }

    return true;
  }

}
