<?php
class PageController extends Controller
{
    public function about(): void     { $this->view('pages/about',      ['title' => t('nav.about')]); }
    public function policies(): void  { $this->view('pages/policies',   ['title' => t('footer.policies')]); }
    public function faq(): void       { $this->view('pages/faq',        ['title' => t('footer.faq')]); }
    public function sizeGuide(): void { $this->view('pages/size-guide', ['title' => t('footer.size_guide')]); }
}
