<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('locale');
        
        if (!$locale) {
            $locale = $this->detectBrowserLanguage($request);
            Session::put('locale', $locale);
        }
        
        App::setLocale($locale);
        
        return $next($request);
    }
    
    private function detectBrowserLanguage(Request $request)
    {
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return 'en';
        }
        
        $languages = explode(',', $acceptLanguage);
        $primaryLanguage = explode(';', $languages[0])[0];
        $languageCode = explode('-', $primaryLanguage)[0];
        
        $supportedLanguages = ['en', 'ar', 'fr'];
        
        return in_array($languageCode, $supportedLanguages) ? $languageCode : 'en';
    }
}
