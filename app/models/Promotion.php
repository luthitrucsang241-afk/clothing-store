<?php
class Promotion extends Model
{
    public static function active(): array
    {
        return self::all("SELECT * FROM promotions WHERE status=1
            AND (start_at IS NULL OR start_at<=NOW()) AND (end_at IS NULL OR end_at>=NOW()) ORDER BY id DESC");
    }
}
