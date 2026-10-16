<?php
class ProductController extends Controller
{
    public function index(): void
    {
        $f = [
            'q' => $this->input('q'), 'category' => $this->input('category'),
            'min' => $this->input('min'), 'max' => $this->input('max'),
            'size' => $this->input('size'), 'color' => $this->input('color'),
            'sort' => $this->input('sort', 'newest'),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $per = (int) config('per_page');
        [$products, $total] = Product::listing($f, $page, $per);
        $this->view('products/index', [
            'title' => t('nav.products'), 'products' => $products, 'total' => $total,
            'page' => $page, 'per' => $per, 'f' => $f,
            'categories' => Category::active(), 'opts' => Product::filterOptions(),
        ]);
    }

    public function detail(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $p = Product::find($id);
        if (!$p) { $this->notFound(); return; }
        $this->view('products/detail', [
            'title' => $p['name'], 'p' => $p,
            'variants' => Product::variants($id), 'images' => Product::images($id),
            'related' => Product::related($p), 'rating' => Product::rating($id),
            'reviews' => Review::forProduct($id),
            'reviewOrder' => user() ? Review::eligibleOrder(user()['id'], $id) : null,
        ]);
    }

    public function categories(): void
    {
        $this->view('products/categories', ['title' => t('nav.categories'), 'categories' => Category::withCount()]);
    }
}
