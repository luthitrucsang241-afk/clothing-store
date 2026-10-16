<?php
// ---------- Cấu hình & URL ----------
function config($k = null, $d = null) { global $config; return $k === null ? $config : ($config[$k] ?? $d); }
function e($s): string { return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function base_url(): string { return rtrim((string) config('base_url'), '/'); }
function url(string $p = ''): string {
    if (preg_match('#^(https?:)?//#', $p)) { return $p; }
    return base_url() . '/' . ltrim($p, '/');
}
function asset(string $p): string { return base_url() . '/assets/' . ltrim($p, '/'); }
function media(?string $p): string { return $p ? url($p) : asset('images/placeholder.svg'); }
function img(?string $p, string $alt = '', string $cls = ''): string {
    $ph = e(asset('images/placeholder.svg'));
    return '<img src="' . e(media($p)) . '" alt="' . e($alt) . '" class="' . e($cls) . '" loading="lazy" onerror="this.onerror=null;this.src=\'' . $ph . '\'">';
}
function money($n): string { return number_format((float) $n, 0, ',', '.') . ' ₫'; }

// ---------- Ngôn ngữ ----------
function lang(): string { return $_SESSION['lang'] ?? config('default_lang', 'vi'); }
function t(string $k, ...$args): string {
    static $d = [];
    $l = lang();
    if (!isset($d[$l])) { $d[$l] = require APP . "/lang/$l.php"; }
    $s = $d[$l][$k] ?? $k;
    return $args ? vsprintf($s, $args) : $s;
}
function loc(array $row, string $f): string {
    if (lang() === 'en' && !empty($row[$f . '_en'])) { return (string) $row[$f . '_en']; }
    return (string) ($row[$f] ?? '');
}

// ---------- Bảo mật: CSRF, flash, old input ----------
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) { $_SESSION['_csrf'] = bin2hex(random_bytes(32)); }
    return $_SESSION['_csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">'; }
function csrf_check(): bool { return hash_equals($_SESSION['_csrf'] ?? '', (string) ($_POST['_token'] ?? '')); }

function flash(string $type, string $msg): void { $_SESSION['flash'][] = ['t' => $type, 'm' => $msg]; }
function flashes(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }
function old(string $k, $d = '') { $v = $_SESSION['old'][$k] ?? $d; return is_array($v) ? $d : $v; }
function field_error(string $k): string {
    $m = $_SESSION['errors'][$k] ?? null;
    return $m ? '<small class="field-error">' . e($m) . '</small>' : '';
}

// ---------- Người dùng ----------
function user(): ?array { return $_SESSION['user'] ?? null; }
function is_admin(): bool { return (user()['role'] ?? '') === 'admin'; }

// ---------- Trạng thái đơn ----------
function status_label(string $s): string { return t('status.' . $s); }
function status_class(string $s): string {
    return ['pending' => 'warn', 'confirmed' => 'info', 'processing' => 'info', 'shipping' => 'info', 'delivered' => 'ok', 'cancelled' => 'bad'][$s] ?? '';
}

// ---------- Tiện ích chuỗi ----------
function slugify(string $s): string {
    $s = mb_strtolower(trim($s), 'UTF-8');
    $map = ['a' => 'àáạảãâầấậẩẫăằắặẳẵ', 'e' => 'èéẹẻẽêềếệểễ', 'i' => 'ìíịỉĩ', 'o' => 'òóọỏõôồốộổỗơờớợởỡ', 'u' => 'ùúụủũưừứựửữ', 'y' => 'ỳýỵỷỹ', 'd' => 'đ'];
    foreach ($map as $r => $chars) { $s = preg_replace('/[' . $chars . ']/u', $r, $s); }
    $s = trim(preg_replace('/[^a-z0-9]+/', '-', $s), '-');
    return $s !== '' ? $s : 'n-a';
}
function unique_slug(string $table, string $slug, int $ignoreId = 0): string {
    $try = $slug; $i = 2;
    while (Model::val("SELECT COUNT(*) FROM `$table` WHERE slug=? AND id<>?", [$try, $ignoreId])) { $try = $slug . '-' . $i++; }
    return $try;
}

// ---------- Phân trang ----------
function pager(int $total, int $page, int $per): string {
    $pages = (int) ceil($total / max(1, $per));
    if ($pages <= 1) { return ''; }
    $page = min(max(1, $page), $pages);
    $q = $_GET;
    $item = function (int $i, string $label, bool $active = false, bool $disabled = false) use ($q) {
        $q['page'] = $i;
        $li = 'page-item' . ($active ? ' active' : '') . ($disabled ? ' disabled' : '');
        $inner = $disabled ? '<span class="page-link">' . $label . '</span>' : '<a class="page-link" href="?' . e(http_build_query($q)) . '"' . ($active ? ' aria-current="page"' : '') . '>' . $label . '</a>';
        return '<li class="' . $li . '">' . $inner . '</li>';
    };
    $h = '<nav aria-label="Phân trang"><ul class="pagination justify-content-center flex-wrap">';
    $h .= $item(max(1, $page - 1), '&lsaquo; ' . e(t('pager.prev')), false, $page <= 1);
    $dots = false;
    for ($i = 1; $i <= $pages; $i++) {
        if ($i === 1 || $i === $pages || abs($i - $page) <= 2) { $dots = false; $h .= $item($i, (string) $i, $i === $page); }
        elseif (!$dots) { $h .= '<li class="page-item disabled"><span class="page-link">…</span></li>'; $dots = true; }
    }
    $h .= $item(min($pages, $page + 1), e(t('pager.next')) . ' &rsaquo;', false, $page >= $pages);
    return $h . '</ul></nav><p class="pager-info muted center">' . e(t('pager.info', $page, $pages, $total)) . '</p>';
}

// ---------- Tải ảnh lên an toàn ----------
function upload_image(array $f): ?array {
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) { return null; }
    if ($f['error'] !== UPLOAD_ERR_OK) { return ['error' => 'Tải tệp thất bại.']; }
    if ($f['size'] > config('upload_max')) { return ['error' => 'Ảnh vượt quá dung lượng cho phép (2MB).']; }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'][$mime] ?? null;
    if (!$ext || !@getimagesize($f['tmp_name'])) { return ['error' => 'Chỉ cho phép ảnh JPG, PNG, WEBP, GIF.']; }
    $dir = ROOT . '/public/assets/uploads';
    if (!is_dir($dir)) { mkdir($dir, 0755, true); }
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], "$dir/$name")) { return ['error' => 'Không lưu được tệp.']; }
    return ['path' => 'assets/uploads/' . $name];
}
// Chuẩn hoá $_FILES['images'] (nhiều tệp) thành danh sách từng tệp
function files_list(string $key): array {
    $out = [];
    if (empty($_FILES[$key]['name']) || !is_array($_FILES[$key]['name'])) { return $out; }
    foreach ($_FILES[$key]['name'] as $i => $n) {
        $out[] = ['name' => $n, 'tmp_name' => $_FILES[$key]['tmp_name'][$i], 'error' => $_FILES[$key]['error'][$i], 'size' => $_FILES[$key]['size'][$i]];
    }
    return $out;
}

// ---------- Gửi email (driver 'log' = chỉ ghi nhật ký, 'smtp' = gửi thật, xem helpers/mailer.php) ----------
function send_mail(string $to, string $type, string $subject, string $body): bool {
    @file_put_contents(ROOT . '/storage/logs/mail.log', '[' . date('Y-m-d H:i:s') . "] TO: $to | $subject\n$body\n---\n", FILE_APPEND);
    $ok = true;
    if ((config('mail', [])['driver'] ?? 'log') === 'smtp') { $ok = smtp_send($to, $subject, $body); }
    try { Model::insert('email_logs', ['recipient' => $to, 'email_type' => $type, 'status' => $ok ? 'sent' : 'failed']); } catch (Throwable $e) {}
    return $ok;
}

// ---------- Hộp thư của khách: lưu thư admin/hệ thống gửi + gửi email ----------
function notify_user(int $uid, string $subject, string $body, ?int $by = null): void {
    try { Model::insert('user_emails', ['user_id' => $uid, 'subject' => mb_substr($subject, 0, 200), 'body' => $body, 'sent_by' => $by]); } catch (Throwable $e) { error_log('notify_user: ' . $e->getMessage()); }
    try {
        $em = Model::val("SELECT email FROM users WHERE id=?", [$uid]);
        if ($em) { send_mail((string) $em, 'notice', $subject, $body); }
    } catch (Throwable $e) {}
}
function inbox_unread(): int {
    static $n = null;
    if ($n === null) {
        $n = 0;
        if (user()) { try { $n = (int) Model::val("SELECT COUNT(*) FROM user_emails WHERE user_id=? AND is_read=0", [user()['id']]); } catch (Throwable $e) {} }
    }
    return $n;
}
