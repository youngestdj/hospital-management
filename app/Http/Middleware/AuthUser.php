<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Closure;
use Exception;

class AuthUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (!$token = $request->header('Authorization')) {
            throw new CustomException(
                'Please log in first.',
                'Authentication error.'
            );
        }

        try {
            $decoded = JWT::decode($token, \config('auth.jwt_secret'), ['HS256']);
        } catch (ExpiredException $e) {
            throw new CustomException(
                'Session expired. Please log in again.',
                'Authentication error.'
            );
        } catch (Exception $e) {
            throw new CustomException(
                'Something went wrong.',
                'Authentication error.'
            );
        }

        if ($decoded->data->user !== $role) {
            throw new CustomException(
              'You do not have permission to perform this action.',
              'Authentication error.'
          );
        }
        
        return $next($request);
    }
}
