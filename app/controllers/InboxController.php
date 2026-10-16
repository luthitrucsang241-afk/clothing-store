<?php
class InboxController extends Controller
{
    public function index(): void
    {
        $uid = user()['id']; $page = max(1, (int) ($_GET['page'] ?? 1)); $per = 10;
        $total = (int) Model::val("SELECT COUNT(*) FROM user_emails WHERE user_id=?", [$uid]);
        $rows = Model::all("SELECT id, subject, is_read, sent_by, created_at FROM user_emails WHERE user_id=? ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per), [$uid]);
        $this->view('inbox/index', ['title' => t('nav.inbox'), 'rows' => $rows, 'total' => $total, 'page' => $page, 'per' => $per]);
    }

    public function show(): void
    {
        $m = Model::one("SELECT * FROM user_emails WHERE id=? AND user_id=?", [(int) ($_GET['id'] ?? 0), user()['id']]);
        if (!$m) { $this->notFound(); return; }
        if (!$m['is_read']) { Model::exec("UPDATE user_emails SET is_read=1 WHERE id=?", [$m['id']]); }
        $this->view('inbox/view', ['title' => $m['subject'], 'm' => $m]);
    }

    public function delete(): void
    {
        Model::exec("DELETE FROM user_emails WHERE id=? AND user_id=?", [(int) $this->input('id'), user()['id']]);
        flash('success', t('inbox.deleted'));
        $this->redirect('/inbox');
    }
}
