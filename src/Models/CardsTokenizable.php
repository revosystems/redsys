<?php


namespace Revosystems\Redsys\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CardsTokenizable
{
    const CACHE_KEY = 'redsys.cards.';

    public static function get(string $customerToken) : Collection
    {
        try {
            if (! $cachedCards = Cache::get(self::CACHE_KEY . "{$customerToken}")) {
                return collect();
            }
            return collect(unserialize($cachedCards));
        } catch (\Exception $e) {
            Log::error("[REDSYS] Unserialize cards exception: {$e->getMessage()}");
            return collect();
        }
    }

    public static function tokenize(GatewayCard $card, $customerToken)
    {
        try {
            $tokenizedCards = unserialize(Cache::get(self::CACHE_KEY . "{$customerToken}", []));
        } catch (\Exception $e) {
            Log::error("[REDSYS] Unserialize old cards exception: {$e->getMessage()}");
            $tokenizedCards = [];
        }
        $tokenizedCards[$card->id] = $card;
        Cache::put(self::CACHE_KEY . "{$customerToken}", serialize($tokenizedCards), Carbon::now()->addMonths(4));
    }
}
