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
    public function handle($request, Closure $next, ...$roles)
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
                $e->getMessage(),
                'Authentication error.'
            );
        }

        foreach ($roles as $role) {
            if ($decoded->data->user === $role) {
              $request->attributes->add(['role' => $decoded->data->user]);
              $request->attributes->add(['userId' => $decoded->data->userId]);
              return $next($request);
            }
        }
        throw new CustomException(
            'You do not have permission to perform this action.',
            'Authentication error.'
        );
    }
}
