<?php

namespace DivineOmega\LaravelRouteRestrictor\Middleware;

use App;
use Auth;
use Closure;

class BasicAuthentication
{
  public function handle($request, Closure $next)
  {
    if (!$this->validate($request->header('PHP_AUTH_USER'), $request->header('PHP_AUTH_PW'))) {
        App::abort(403);
    }
    return $next($request);
  }

  private function validate($user, $password)
  {
    // Get current route name
    $routeName = \Request::route()->getName();

    // If we have a named route
    if ($routeName) {

      // Check if route username and password are set
      if ($routeUsername = env('routerestrictor.route.'.$routeName.'.username') && $routePassword = env('routerestrictor.route.'.$routeName.'.password')) {

        // Check against route password
        if (trim($user) === $routeUsername && trim($password) === $routePassword) {
          return true;
        }
      }

    }


    // Check if global username and password are set
    if ($globalUsername = env('routerestrictor.global.username') && $globalPassword = env('routerestrictor.global.password')) {

      // Check against global password
      if (trim($user) === $globalUsername && trim($password) === $globalPassword) {
        return true;
      }

    }

    return false;
  }

}
