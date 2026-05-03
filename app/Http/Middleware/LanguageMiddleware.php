<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Get current locale
        $locale = session()->get('locale') ?? config('app.locale');
        
        // 2. Set the application locale
        App::setLocale($locale);
        app('translator')->setLocale($locale);

        // 3. Sync Database to JSON Files only if needed (e.g., using a cache flag)
        // Optimization: Use a cache key to avoid syncing on every single request
        $cacheKey = "lang_sync_{$locale}";
        
        if (!Cache::has($cacheKey)) {
            try {
                // Get all translations using raw DB query for speed and reliability
                $translations = \DB::table('translations')->get();
                $data = [];
                
                foreach ($translations as $t) {
                    $val = ($locale === 'kh') ? ($t->kh ?: $t->en) : ($t->en ?: $t->kh);
                    if ($val) {
                        $data[$t->key] = $val;
                    }
                }

                if (!empty($data)) {
                    $langFile = lang_path($locale . '.json');
                    
                    // Ensure directory exists
                    if (!file_exists(dirname($langFile))) {
                        mkdir(dirname($langFile), 0755, true);
                    }
                    
                    // Write the JSON file
                    file_put_contents($langFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                    
                    // Reset translator so it re-reads the JSON file we just wrote
                    app('translator')->setLoaded([]);
                    
                    // Cache the sync status for 60 minutes to avoid redundant work
                    Cache::put($cacheKey, true, now()->addMinutes(60));
                    
                    \Log::debug("Language Sync: Successful for " . $locale);
                }
            } catch (\Exception $e) {
                \Log::error("Translation Sync Failed: " . $e->getMessage());
            }
        }

        return $next($request);
    }
}
