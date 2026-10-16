<?php
/**
 * Gửi email qua SMTP (Gmail) và SMS (cổng SMS).
 * Cấu hình ở config/app.php -> 'mail' và 'sms'.
 * driver = 'log'  : chỉ ghi vào storage/logs (dùng khi phát triển, không gửi thật)
 * driver = 'smtp' : gửi thật qua SMTP (Gmail cần "Mật khẩu ứng dụng" 16 ký tự)
 */
function smtp_send(string $to, string $subject, string $body): bool
{
    $c = config('mail', []);
    $host = $c['host'] ?? 'smtp.gmail.com'; $port = (int) ($c['port'] ?? 587);
    $user = $c['username'] ?? ''; $pass = str_replace(' ', '', (string) ($c['password'] ?? ''));
    $from = $c['from_email'] ?: $user; $fromName = $c['from_name'] ?? 'NOIRVA';
    if ($user === '' || $pass === '' || $from === '') { return false; }

    $fp = @stream_socket_client(($port === 465 ? 'ssl://' : 'tcp://') . "$host:$port", $en, $es, 15);
    if (!$fp) { return false; }
    stream_set_timeout($fp, 15);
    $read = function () use ($fp) {
        $out = '';
        while (($l = fgets($fp, 515)) !== false) { $out .= $l; if (isset($l[3]) && $l[3] === ' ') { break; } }
        return $out;
    };
    $cmd = function (string $s, string $ok) use ($fp, $read) {
        fwrite($fp, $s . "\r\n");
        $r = $read();
        return strpos($r, $ok) === 0;
    };
    try {
        if (strpos($read(), '220') !== 0) { throw new RuntimeException('greeting'); }
        if (!$cmd('EHLO ' . ($_SERVER['HTTP_HOST'] ?? 'localhost'), '250')) { throw new RuntimeException('ehlo'); }
        if ($port !== 465) {
            if (!$cmd('STARTTLS', '220')) { throw new RuntimeException('starttls'); }
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) { throw new RuntimeException('tls'); }
            if (!$cmd('EHLO ' . ($_SERVER['HTTP_HOST'] ?? 'localhost'), '250')) { throw new RuntimeException('ehlo2'); }
        }
        if (!$cmd('AUTH LOGIN', '334') || !$cmd(base64_encode($user), '334') || !$cmd(base64_encode($pass), '235')) { throw new RuntimeException('auth'); }
        if (!$cmd("MAIL FROM:<$from>", '250') || !$cmd("RCPT TO:<$to>", '250') || !$cmd('DATA', '354')) { throw new RuntimeException('rcpt'); }
        $headers = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <$from>\r\nTo: <$to>\r\n"
            . "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\nDate: " . date('r') . "\r\n"
            . "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n";
        $msg = $headers . "\r\n" . chunk_split(base64_encode($body));
        if (!$cmd($msg . "\r\n.", '250')) { throw new RuntimeException('data'); }
        $cmd('QUIT', '221');
        fclose($fp);
        return true;
    } catch (Throwable $e) {
        error_log('SMTP: ' . $e->getMessage());
        @fclose($fp);
        return false;
    }
}

/** Gửi SMS. driver 'log' chỉ ghi file; muốn gửi thật cần gắn nhà cung cấp SMS (eSMS, SpeedSMS, Twilio...) vào đây. */
function send_sms(string $to, string $message): bool
{
    @file_put_contents(ROOT . '/storage/logs/sms.log', '[' . date('Y-m-d H:i:s') . "] TO: $to | $message\n", FILE_APPEND);
    $driver = config('sms', [])['driver'] ?? 'log';
    if ($driver === 'log') { return true; }
    // TODO: gọi API của nhà cung cấp SMS ở đây khi đã có tài khoản/API key.
    return false;
}
