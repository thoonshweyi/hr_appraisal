<?php
namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Support\Facades\App as FacadesApp;

class Localization
{
    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('locale')) {
            FacadesApp::setLocale(session()->get('locale'));
        }
        return $next($request);
    }
}
?>
