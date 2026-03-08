<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session("locale", config("app.locale", "en"));
        App::setLocale($locale);

        $tz = session("user_timezone", "Asia/Makassar");
        config(["app.timezone" => $tz]);
        date_default_timezone_set($tz);

        return $next($request);
    }
}