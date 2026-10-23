<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShortLink extends Model
{
    protected $fillable = ['code', 'url', 'title', 'hits', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Create a short public URL (e.g. https://www.thmmn.net/l/k3x9ab2)
     * that redirects to the given (usually long, signed) URL.
     * Falls back to the original URL if anything goes wrong.
     */
    public static function shorten(string $url, ?string $title = null): string
    {
        try {
            // Inherit the expiry of signed temporary URLs (?expires=timestamp)
            $expiresAt = null;
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            if (!empty($query['expires']) && is_numeric($query['expires'])) {
                $expiresAt = \Carbon\Carbon::createFromTimestamp((int) $query['expires']);
            }

            do {
                $code = Str::lower(Str::random(7));
            } while (self::where('code', $code)->exists());

            self::create([
                'code'       => $code,
                'url'        => $url,
                'title'      => $title,
                'expires_at' => $expiresAt,
            ]);

            return route('short.link', ['code' => $code]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ShortLink creation failed: ' . $e->getMessage());
            return $url;
        }
    }
}
