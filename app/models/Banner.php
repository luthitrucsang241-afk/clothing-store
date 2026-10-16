<?php
class Banner extends Model
{
    public static function active(string $pos): array
    {
        return self::all("SELECT * FROM banners WHERE position=? AND status=1
            AND (start_at IS NULL OR start_at<=NOW()) AND (end_at IS NULL OR end_at>=NOW())
            ORDER BY sort_order, id", [$pos]);
    }
}
