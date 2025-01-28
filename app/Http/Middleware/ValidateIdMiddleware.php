<?php

namespace App\Http\Middleware;

use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cases = [
            'role',
            'user',
        ];

        foreach ($request->route()->parameters as $key => $param) {
            if (in_array($key, $cases)) {
                if ((string) $param != $param) {
                    return response()->json(['message' => 'Invalid ID'], 404);
                }

                switch ($key) {
                    case 'role':
                        $roleRepository = new RoleRepository;
                        if (! $roleRepository->getById($param, false)) {
                            return response()->json(['message' => 'Role tidak ditemukan.'], 404);
                        }
                        break;
                    case 'user':
                        $userRepository = new UserRepository;
                        if (! $userRepository->getById($param, false)) {
                            return response()->json(['message' => 'User tidak ditemukan.'], 404);
                        }
                        break;
                }
            }
        }

        return $next($request);
    }
}
