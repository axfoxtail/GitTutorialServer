<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $tokenType = "Bearer";
    public function handle($request, Closure $next)
    {
        try {
            $token = $request->header('Authorization');
            $token = ltrim($token, $this->tokenType . ' ');
            if (!$token) {
                return response()->json(["status" => 300, "message" => "Invalid Token."], 400);
            }
            $user = User::where('api_token', $token)->first();
            if (!$user) {
                return response()->json(["status" => 300, "message" => "Invalid Token."], 400);
            }
            if (!$user->is_admin) {
                return response()->json(["status" => 300, "message" => "No Admin Permission."], 400);
            }
        } 
        catch (\Exception $e) {
            return response()->json(["status" => 300, "message" => "Token Error."], 200);
        }

        return $next($request);
    }
}
