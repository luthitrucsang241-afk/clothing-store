<?php
class Category extends Model
{
    public static function active(): array
    {
        static $cache = null;
        if ($cache === null) { $cache = self::all("SELECT * FROM categories WHERE status=1 ORDER BY id"); }
        return $cache;
    }
    public static function withCount(): array
    {
        return self::all("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id=c.id AND p.status=1) AS total FROM categories c WHERE c.status=1 ORDER BY c.id");
    }
}
