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
        header('Access-Control-Allow-Origin: *');
        $headers = [
          "Access-Control-Allow-Methods" => "OPTIONS, PUT, POST, GET, DELETE, PATCH",
          "Access-Control-Allow-Headers" => "Content-Type, Authorization, Accept",
          "Access-Control-Max-Age" => "3600"
        ];

        if($request->getMethod() === 'OPTIONS') {
            return response()->make('', 200, $headers);
        }

        $response = $next($request);
        foreach ($headers as $key => $item) {
            $response->header($key, $item);
        }

        return $response;
    }
}
