<?php

namespace DivineOmega\LaravelRouteRestrictor\Middleware;

use App;
use Auth;
use Closure;
use Exception;

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
      // Check if global username and password are set
      if (!$globalUsername = env('routerestrictor.global.username') || !$globalPassword = env('routerestrictor.global.password')) {
        throw new Exception('laravel route restrictor global username and password are not set in environment file.');
      }

      // Check against global password
      if (trim($user) === $globalUsername && trim($password) === $globalPassword) {
        return true;
      }

      return false;
    }


}
