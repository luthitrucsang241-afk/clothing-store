<?php
class Setting extends Model
{
    private static ?array $cache = null;
    public static function get(string $key, $default = '')
    {
        if (self::$cache === null) {
            self::$cache = [];
            foreach (self::all("SELECT setting_key, setting_value FROM settings") as $r) { self::$cache[$r['setting_key']] = $r['setting_value']; }
        }
        return self::$cache[$key] ?? $default;
    }
}
