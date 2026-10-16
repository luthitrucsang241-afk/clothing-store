<?php
function valid_email(string $v): bool { return (bool) filter_var($v, FILTER_VALIDATE_EMAIL) && strlen($v) <= 150; }
function valid_phone(string $v): bool { return (bool) preg_match('/^(0|\+84)\d{9,10}$/', $v); }
function valid_password(string $v): bool { return strlen($v) >= 8 && preg_match('/[A-Za-z]/', $v) && preg_match('/\d/', $v); }
function str_len(string $v): int { return mb_strlen($v, 'UTF-8'); }
