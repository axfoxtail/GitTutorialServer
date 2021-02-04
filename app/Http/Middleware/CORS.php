<?php

namespace App\Http\Middleware;

use Closure;

class CORS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        header("Access-Control-Allow-Origin: *");

        $headers = [
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE, PATCH',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
        ];

        if ($request->getMethod() == 'OPTIONS') {
            return \Response::make('OK', 200, $headers);
        }

        $res = $next($request);

        try {
            foreach ($headers as $key => $value) {
                $res->header($key, $value);
            }
        }
        catch (\Exception $e) {
            foreach ($headers as $key => $value) {
                $res->headers->set($key, $value);
            }
        }

        return $res;
    }
}
