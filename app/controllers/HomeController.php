<?php
class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'title'      => config('name'),
            'hero'       => Banner::active('hero'),
            'promo'      => Banner::active('promo'),
            'middle'     => Banner::active('middle'),
            'categories' => Category::withCount(),
            'featured'   => Product::home('featured'),
            'newest'     => Product::home('new'),
            'best'       => Product::home('best'),
            'sale'       => Product::home('sale'),
            'promotions' => Promotion::active(),
        ]);
    }

    public function newsletter(): void
    {
        $email = $this->input('email');
        if (!valid_email($email)) { flash('error', t('msg.invalid_email')); $this->back('/'); }
        Model::exec("INSERT IGNORE INTO newsletter_subscribers (email) VALUES (?)", [$email]);
        flash('success', t('msg.subscribed'));
        $this->back('/');
    }

    public function lang(string $code): void
    {
        if (in_array($code, ['vi', 'en'], true)) { $_SESSION['lang'] = $code; }
        $this->back('/');
    }
}
