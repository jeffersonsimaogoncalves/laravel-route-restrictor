<?php

namespace DivineOmega\LaravelRouteRestrictor\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * This is the basic authentication class.
 *
 * @author Jordan Hall <jordan.hall@rapidweb.biz>
 * @author James Brooks <james@alt-three.com>
 */
class BasicAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Note: This ugly hack is required for web servers in which PHP is run
        // via a CGI handler. In these cases, PHP does not have access to the
        // $_SERVER['PHP_AUTH_USER'] and $_SERVER['PHP_AUTH_PW'] variables.
        // Therefore, we must use a .htaccess rule to rewrite the raw basic
        // authentication data into a $_SERVER variable, and then the below
        // code will convert this to the $_SERVER['PHP_AUTH_USER'] and
        // $_SERVER['PHP_AUTH_PW'] variables we need.
        // For this to work, the following line must be placed in your
        // `public/.htaccess` file under `RewriteEngine On`.
        // RewriteRule .* - [E=REMOTE_USER:%{HTTP:Authorization}]
        if (isset($_SERVER["REDIRECT_REMOTE_USER"]) && $_SERVER["REDIRECT_REMOTE_USER"] != '') {
            $d = base64_decode(substr($_SERVER["REDIRECT_REMOTE_USER"], 6));
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $d);
        }

        $username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] :  '';
        $password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] :  '';

        if (!$this->validate($request, $username, $password)) {
            throw new UnauthorizedHttpException('Basic', 'Unauthorized. Please check your username and password.');
        }

        return $next($request);
    }

    /**
     * Validates the user, password combination against the request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $user
     * @param string                   $password
     *
     * @return bool
     */
    protected function validate($request, $user, $password)
    {
        // Get current route name
        // Note: we do not have access to the current route in middleware, because
        // it has not been fully dispatched, therefore we must use the backwards
        // method of finding the route which matches the current request.
        $routeName = null;
        foreach(Route::getRoutes() as $route) {
            if ($route->matches($request)) {
                $routeName = $route->getName();
            }
        }

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
