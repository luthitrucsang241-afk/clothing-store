# Hướng dẫn mua domain, hosting và đưa NOIRVA lên mạng

## 1. Chọn và mua tên miền (domain)
1. Kiểm tra tên còn trống (vd `noirva.vn`, `noirva.com`, `noirva.store`) tại nhà đăng ký như: PA Vietnam, Mắt Bão, Nhân Hòa, Tenten (tên miền .vn), hoặc Namecheap, GoDaddy, Cloudflare Registrar (.com).
2. Tên miền `.vn` cần giấy tờ cá nhân/doanh nghiệp theo quy định; `.com` mua nhanh bằng thẻ quốc tế.
3. Mua 1 năm, bật **khoá domain** và **WHOIS privacy** (nếu có). Giá thay đổi theo từng nhà cung cấp, hãy xem bảng giá hiện tại trước khi mua.

## 2. Chọn hosting
Website cần: **PHP 8.0+**, **MySQL/MariaDB**, **Apache** (hỗ trợ `.htaccess`), SSL miễn phí (Let's Encrypt).
- Giai đoạn đầu: **Shared hosting cPanel** là đủ (Mắt Bão, PA Vietnam, Nhân Hòa, Hostinger…).
- Khi nhiều khách: nâng lên **VPS** (cần tự cài Apache/Nginx, PHP, MySQL).
Chọn gói có: cPanel, phpMyAdmin, SSH/FTP, ít nhất 1GB SSD, hỗ trợ tiếng Việt nếu bạn cần.

## 3. Trỏ domain về hosting
Cách A (dễ nhất): ở trang quản lý domain, đổi **Nameserver** thành 2 nameserver nhà hosting gửi trong email.
Cách B: giữ nameserver, tạo **bản ghi A** (`@` và `www`) trỏ về **IP hosting**.
Chờ 15 phút đến 24 giờ để DNS cập nhật (kiểm tra bằng whatsmydns.net).

## 4. Tạo cơ sở dữ liệu trên hosting
1. cPanel -> **MySQL Databases**: tạo database (vd `tenhosting_noirva`), tạo user + mật khẩu mạnh, gán user vào database với **All Privileges**.
2. cPanel -> **phpMyAdmin** -> chọn database vừa tạo -> **Import** `noirva_store.sql`.
   - **Quan trọng:** file SQL có dòng `DROP DATABASE`/`CREATE DATABASE`/`USE noirva_store`. Trên hosting bạn không được tạo DB tên đó, nên hãy mở file bằng VS Code và **xoá 3 dòng** `DROP DATABASE ...`, `CREATE DATABASE ...`, `USE ...` trước khi import (hoặc dùng file trong thư mục database sau khi chỉnh).

## 5. Upload mã nguồn
1. Nén thư mục `clothing-store` thành zip -> cPanel **File Manager** -> upload vào thư mục chứa site -> **Extract**. (Hoặc dùng FileZilla qua FTP.)
2. **Cách tốt nhất:** cPanel -> **Domains** -> sửa **Document Root** của domain thành `.../clothing-store/public`.
   - Nếu không đổi được document root: đặt toàn bộ project trong `public_html/` (file `.htaccess` ở thư mục gốc sẽ tự chuyển vào `public/`).
3. Sửa `config/app.php`:
   - `'base_url' => ''`  (khi domain trỏ thẳng vào site)
   - `'debug' => false`
4. Sửa `config/database.php` bằng tên DB, user, mật khẩu trên hosting (không để mật khẩu trong thư mục public, không đưa lên GitHub).
5. Phân quyền: thư mục `storage/logs` và `public/assets/uploads` cho phép ghi (755, nếu lỗi thì 775).
6. cPanel -> **Select PHP Version**: chọn PHP 8.1+, bật `pdo_mysql`, `mbstring`, `fileinfo`, `gd`.

## 6. Bật HTTPS (SSL)
cPanel -> **SSL/TLS Status** -> chạy **AutoSSL / Let's Encrypt** cho domain. Sau khi có ổ khoá, mở `public/.htaccess` và bỏ dấu `#` ở 3 dòng cuối để ép dùng https.

## 7. Kiểm tra sau khi lên mạng
- Đăng nhập admin, **đổi mật khẩu mặc định** (hoặc tạo admin mới rồi khoá tài khoản mẫu).
- Thử luồng: xem sản phẩm -> thêm giỏ -> đặt hàng COD -> duyệt đơn trong Admin.
- Vào `/developer` xem trạng thái hệ thống, đảm bảo "Chế độ debug" đã tắt.
- Sao lưu định kỳ: Export database trong phpMyAdmin + tải thư mục `public/assets/uploads`.

## 8. Email thật (tuỳ chọn, làm sau)
Hiện hệ thống ghi email vào `storage/logs/mail.log`. Để gửi email thật, tích hợp PHPMailer + SMTP của hosting hoặc dịch vụ như Brevo/Mailgun trong hàm `send_mail()` (`app/helpers/functions.php`).
