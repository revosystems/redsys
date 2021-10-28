<?php


namespace Revosystems\Redsys\Models;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class RedsysUserUUid
{
    public static function get()
    {
        if (! $uuid = Cookie::get('redsys-uuid')){
            $uuid = Str::random(24);
            Cookie::queue('redsys-uuid', $uuid);
        }
        return $uuid;
    }
}