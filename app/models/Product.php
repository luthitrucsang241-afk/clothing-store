<?php
class Product extends Model
{
    // Giá hiệu lực: giá khuyến mãi nếu có và thấp hơn giá gốc
    const FP = "IF(p.sale_price IS NOT NULL AND p.sale_price<p.price,p.sale_price,p.price)";
    const SEL = "SELECT p.*, c.name AS category_name, c.name_en AS category_name_en, c.slug AS category_slug, "
        . self::FP . " AS final_price, (SELECT COALESCE(SUM(v.stock_quantity),0) FROM product_variants v WHERE v.product_id=p.id AND v.status=1) AS stock "
        . "FROM products p JOIN categories c ON c.id=p.category_id";

    public static function unitPrice(array $r, $adj = 0): float
    {
        $base = ($r['sale_price'] !== null && (float) $r['sale_price'] < (float) $r['price']) ? (float) $r['sale_price'] : (float) $r['price'];
        return max(0, $base + (float) $adj);
    }

    public static function home(string $type, int $limit = 8): array
    {
        $where = "p.status=1 AND c.status=1";
        $order = ['featured' => 'p.is_featured DESC, p.id DESC', 'new' => 'p.is_new DESC, p.created_at DESC', 'best' => 'p.sold_count DESC', 'sale' => '(p.price-p.sale_price)/p.price DESC'][$type] ?? 'p.id DESC';
        if ($type === 'featured') { $where .= " AND p.is_featured=1"; }
        if ($type === 'new')      { $where .= " AND p.is_new=1"; }
        if ($type === 'sale')     { $where .= " AND p.sale_price IS NOT NULL AND p.sale_price<p.price"; }
        return self::all(self::SEL . " WHERE $where ORDER BY $order LIMIT " . (int) $limit);
    }

    public static function listing(array $f, int $page, int $per): array
    {
        $w = ['p.status=1', 'c.status=1']; $p = [];
        if ($f['q'] !== '') { $like = '%' . addcslashes($f['q'], '%_\\') . '%'; $w[] = '(p.name LIKE ? OR p.sku LIKE ?)'; array_push($p, $like, $like); }
        if ($f['category'] !== '') { $w[] = 'c.slug=?'; $p[] = $f['category']; }
        if ($f['min'] !== '' && is_numeric($f['min'])) { $w[] = self::FP . ' >= ?'; $p[] = $f['min']; }
        if ($f['max'] !== '' && is_numeric($f['max'])) { $w[] = self::FP . ' <= ?'; $p[] = $f['max']; }
        $vc = []; $vp = [];
        if ($f['size'] !== '')  { $vc[] = 'v.size=?';  $vp[] = $f['size']; }
        if ($f['color'] !== '') { $vc[] = 'v.color=?'; $vp[] = $f['color']; }
        if ($vc) {
            $w[] = 'EXISTS (SELECT 1 FROM product_variants v WHERE v.product_id=p.id AND v.status=1 AND ' . implode(' AND ', $vc) . ')';
            $p = array_merge($p, $vp);
        }
        $order = [
            'price_asc'  => self::FP . ' ASC, p.id DESC',
            'price_desc' => self::FP . ' DESC, p.id DESC',
            'best'       => 'p.sold_count DESC',
            'newest'     => 'p.created_at DESC, p.id DESC',
        ][$f['sort']] ?? 'p.created_at DESC, p.id DESC';
        $where = implode(' AND ', $w);
        $total = (int) self::val("SELECT COUNT(*) FROM products p JOIN categories c ON c.id=p.category_id WHERE $where", $p);
        $rows = self::all(self::SEL . " WHERE $where ORDER BY $order LIMIT " . (int) $per . " OFFSET " . (int) (($page - 1) * $per), $p);
        return [$rows, $total];
    }

    public static function filterOptions(): array
    {
        return [
            'sizes'  => array_column(self::all("SELECT DISTINCT size FROM product_variants WHERE status=1 ORDER BY FIELD(size,'S','M','L','XL','XXL','FREE'), size"), 'size'),
            'colors' => array_column(self::all("SELECT DISTINCT color FROM product_variants WHERE status=1 ORDER BY color"), 'color'),
        ];
    }

    public static function find(int $id): ?array
    {
        return self::one(self::SEL . " WHERE p.id=? AND p.status=1 AND c.status=1", [$id]);
    }

    public static function variants(int $id): array
    {
        return self::all("SELECT * FROM product_variants WHERE product_id=? AND status=1 ORDER BY color, FIELD(size,'S','M','L','XL','XXL'), size", [$id]);
    }

    public static function images(int $id): array
    {
        return self::all("SELECT * FROM product_images WHERE product_id=? ORDER BY sort_order, id", [$id]);
    }

    public static function related(array $p, int $limit = 4): array
    {
        return self::all(self::SEL . " WHERE p.category_id=? AND p.id<>? AND p.status=1 AND c.status=1 ORDER BY p.sold_count DESC LIMIT " . (int) $limit, [$p['category_id'], $p['id']]);
    }

    public static function rating(int $id): array
    {
        $r = self::one("SELECT COALESCE(AVG(rating),0) AS avg, COUNT(*) AS cnt FROM reviews WHERE product_id=? AND status=1", [$id]);
        return ['avg' => round((float) $r['avg'], 1), 'cnt' => (int) $r['cnt']];
    }
}
