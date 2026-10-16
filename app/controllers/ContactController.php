<?php
class ContactController extends Controller
{
    public function index(): void { $this->view('contact/index', ['title' => t('nav.contact')]); }

    public function send(): void
    {
        $n = $this->input('full_name'); $m = strtolower($this->input('email')); $s = $this->input('subject'); $c = $this->input('message');
        $e = [];
        if (str_len($n) < 2 || str_len($n) > 120) { $e['full_name'] = t('val.name'); }
        if (!valid_email($m)) { $e['email'] = t('msg.invalid_email'); }
        if ($s === '' || str_len($s) > 200) { $e['subject'] = t('val.required'); }
        if (str_len($c) < 10 || str_len($c) > 3000) { $e['message'] = t('contact.msg_len'); }
        if ($e) { $this->failWith($e, '/contact'); }
        Model::insert('contacts', ['full_name' => $n, 'email' => $m, 'subject' => $s, 'message' => $c]);
        flash('success', t('contact.sent'));
        $this->redirect('/contact');
    }
}
