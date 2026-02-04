<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        $user = Auth::user();
        
        if(isset($user) && !$user->is_active){
            Auth::logout();
            
            $request->session()->invalidate();
 
            $request->session()->regenerateToken();

            return redirect()->route('login') 
                    ->with("errors", ["Ваша учетная запись не активна"]);
        }
        return $next($request);
    }
}
