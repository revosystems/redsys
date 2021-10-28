<?php


namespace Revosystems\RedsysPayment\Models;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class LiveUser
{
    public static function uuid()
    {
        if (! $uuid = Cookie::get('xpress-uuid')){
            $uuid = Str::random(24);
            Cookie::queue('xpress-uuid', $uuid);
        }
        return $uuid;
    }
}