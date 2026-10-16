<?php
// Khu vực kỹ thuật: KHÔNG hiển thị mật khẩu/cấu hình bí mật, KHÔNG cho chạy SQL tuỳ ý.
class DeveloperController extends Controller
{
    public function status(): void
    {
        $checks = [];
        try {
            $tables = (int) Model::val("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE()");
            $checks['Kết nối MySQL'] = ['ok' => true, 'info' => "Thành công, $tables bảng"];
        } catch (Throwable $e) { $checks['Kết nối MySQL'] = ['ok' => false, 'info' => 'Thất bại']; }
        $checks['Phiên bản PHP'] = ['ok' => version_compare(PHP_VERSION, '8.0', '>='), 'info' => PHP_VERSION];
        foreach (['pdo_mysql', 'mbstring', 'fileinfo', 'gd'] as $ext) { $checks["Extension $ext"] = ['ok' => extension_loaded($ext), 'info' => extension_loaded($ext) ? 'Đã bật' : 'Chưa bật']; }
        $checks['Ghi được storage/logs'] = ['ok' => is_writable(ROOT . '/storage/logs'), 'info' => ''];
        $checks['Ghi được assets/uploads'] = ['ok' => is_writable(ROOT . '/public/assets/uploads'), 'info' => ''];
        $checks['Chế độ debug'] = ['ok' => !config('debug'), 'info' => config('debug') ? 'ĐANG BẬT – hãy tắt khi chạy thật' : 'Đã tắt'];
        $this->view('developer/status', ['title' => 'Trạng thái hệ thống', 'checks' => $checks, 'app' => ['Tên' => config('name'), 'Múi giờ' => date_default_timezone_get(), 'Máy chủ' => $_SERVER['SERVER_SOFTWARE'] ?? '']], 'admin');
    }

    public function logs(): void
    {
        $file = ROOT . '/storage/logs/error.log';
        $lines = is_file($file) ? array_slice(file($file, FILE_IGNORE_NEW_LINES) ?: [], -200) : [];
        $this->view('developer/logs', ['title' => 'Nhật ký lỗi', 'lines' => array_reverse($lines)], 'admin');
    }
}
