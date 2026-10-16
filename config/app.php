<?php
return [
    'name'         => 'NOIRVA',
    // Chạy local XAMPP: '/clothing-store/public'
    // Khi lên hosting (document root trỏ vào thư mục public, hoặc dùng .htaccess gốc): ''
    'base_url'     => '/clothing-store/public',
    'debug'        => true,          // Đặt false khi chạy thật
    'default_lang' => 'vi',
    'per_page'     => 9,             // số sản phẩm mỗi trang (trang khách hàng)
    'upload_max'   => 2 * 1024 * 1024, // 2MB
    'timezone'     => 'Asia/Ho_Chi_Minh',

    // ----- Gửi email đặt lại mật khẩu -----
    // driver 'log'  : không gửi thật, chỉ ghi storage/logs/mail.log (khi debug=true link hiện ngay trên web)
    // driver 'smtp' : gửi thật qua Gmail. Cần bật xác minh 2 bước cho Gmail rồi tạo "Mật khẩu ứng dụng"
    //                 (https://myaccount.google.com/apppasswords), dán 16 ký tự vào 'password'.
    'mail' => [
        'driver'     => 'log',
        'host'       => 'smtp.gmail.com',
        'port'       => 587,
        'username'   => '',                // vd: noirva.shop@gmail.com
        'password'   => '',                // mật khẩu ứng dụng 16 ký tự (KHÔNG phải mật khẩu Gmail)
        'from_email' => '',                // để trống = dùng username
        'from_name'  => 'NOIRVA',
    ],
    // ----- Gửi mã OTP qua số điện thoại -----
    // 'log' : chỉ ghi storage/logs/sms.log (khi debug=true mã hiện ngay trên web). Gửi SMS thật cần nhà cung cấp SMS.
    'sms' => ['driver' => 'log'],
];
