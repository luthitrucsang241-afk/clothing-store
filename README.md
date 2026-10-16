# NOIRVA – Website bán quần áo (PHP thuần + MySQL, mô hình MVC)

## Yêu cầu
- XAMPP (PHP 8.0 trở lên, bật `pdo_mysql`, `mbstring`, `fileinfo`), Apache có `mod_rewrite`.
- Trình duyệt hiện đại, VS Code.

## Cài đặt trên localhost
1. Chép thư mục `clothing-store` vào `C:\xampp\htdocs\`.
2. Mở XAMPP, bật **Apache** và **MySQL**.
3. Vào `http://localhost/phpmyadmin` -> tab **Import** -> chọn `database/noirva_store.sql` -> **Go**.
4. Kiểm tra `config/database.php` (mặc định user `root`, mật khẩu rỗng).
5. Kiểm tra `config/app.php`: `base_url` = `/clothing-store/public` (đúng với tên thư mục).
6. Mở: **http://localhost/clothing-store/public** (hoặc http://localhost/clothing-store).

## Tài khoản mẫu (mật khẩu: `Admin@123`, hãy đổi sau khi cài)
| Vai trò | Email | Trang |
|---|---|---|
| Admin | admin@noirva.local | /admin |
| Developer | dev@noirva.local | /developer |
| Khách | khach@noirva.local | / |

Voucher mẫu: `WELCOME10`, `NOIRVA50K`, `SUMMER20`.

## Ảnh
Ảnh trong dữ liệu mẫu chưa có thật nên web hiển thị ảnh giữ chỗ. Cách thêm ảnh:
- Vào Admin -> Sản phẩm -> Sửa -> tải ảnh chính/ảnh phụ.
- Admin -> Banner / Danh mục -> tải ảnh.
- Ảnh tải lên được lưu ở `public/assets/uploads/`.

## Cấu trúc & quy ước
- MVC: `app/controllers`, `app/models`, `app/views`; Router ở `app/core/Router.php`; URL khai báo ở `routes/web.php`.
- Lưới 12-8-4 cột: `public/assets/css/responsive.css` (`.c-N` desktop, `.t-N` tablet, `.m-N` mobile).
- Light/Dark: `theme.css` + `theme.js` (lưu localStorage). Việt/Anh: `app/lang/vi.php`, `en.php` (đổi bằng nút VI | EN).
- Giỏ hàng lưu theo Session; giá, tồn kho, voucher luôn tính lại ở máy chủ; đặt hàng chạy trong transaction.
- Khuyến mãi (bảng `promotions`) chỉ hiển thị thông tin; giảm giá thực tế dùng Voucher và giá khuyến mãi của sản phẩm.
- Trang Admin hiển thị tiếng Việt.

## Bảo mật đã áp dụng
`password_hash`, prepared statements (PDO), CSRF token cho mọi POST, escape XSS, kiểm tra quyền ở máy chủ (middleware), chống đoán mật khẩu cơ bản, kiểm tra ảnh tải lên, chặn chạy PHP trong thư mục uploads, `session_regenerate_id` khi đăng nhập.

## Lỗi thường gặp
- Trang trắng/lỗi 500: xem `storage/logs/error.log` hoặc trang `/developer/logs`.
- 404 mọi trang: bật `mod_rewrite` và `AllowOverride All` cho thư mục htdocs.
- Lỗi kết nối DB: kiểm tra MySQL đã bật và `config/database.php`.
- Link quên mật khẩu / mã OTP: khi `debug = true` và `mail.driver = 'log'`, link và mã OTP hiện trực tiếp trên thông báo, đồng thời ghi ở `storage/logs/mail.log` và `sms.log`.

## Cập nhật mới (quên mật khẩu, ghi nhớ đăng nhập, bản đồ)
- **Nếu đã import DB cũ**: chạy `database/migration_update_1.sql` trong phpMyAdmin (một lần). Import mới hoàn toàn thì không cần.
- **Quên mật khẩu**: `/forgot-password` cho chọn *qua email* (liên kết, hiệu lực 1 giờ) hoặc *qua số điện thoại* (OTP 6 số, hiệu lực 10 phút, sai tối đa 5 lần).
- **Gửi email thật bằng Gmail**: trong `config/app.php` đặt `mail.driver = 'smtp'`, điền `username` (Gmail) và `password` (Mật khẩu ứng dụng 16 ký tự tạo tại https://myaccount.google.com/apppasswords, cần bật xác minh 2 bước).
- **SMS thật**: cần đăng ký nhà cung cấp SMS (eSMS, SpeedSMS, Twilio...) và gắn API vào hàm `send_sms()` trong `app/helpers/mailer.php`. Mặc định chỉ ghi log.
- **Ghi nhớ đăng nhập**: ô "Ghi nhớ đăng nhập (30 ngày)" ở trang đăng nhập; token băm lưu ở bảng `remember_tokens`, đổi token mỗi lần dùng, xoá khi đăng xuất hoặc đổi mật khẩu bằng chức năng quên mật khẩu.
- **Liên hệ**: bản đồ Google Maps ghim Đại học Văn Hiến, 613 Âu Cơ. Đổi vị trí bằng khoá `map_query` trong bảng `settings`.


## Cập nhật lần 2 (theo yêu cầu giảng viên)
- **Phân trang sản phẩm**: 9 sản phẩm/trang (`per_page` trong `config/app.php`), có nút Trước/Sau, dùng thành phần `pagination` của Bootstrap.
- **Admin xoá sản phẩm**: nút *Xoá* ở Admin -> Sản phẩm (có hộp thoại xác nhận). Xoá cả biến thể, ảnh, đánh giá của sản phẩm; đơn hàng cũ vẫn giữ tên/giá/ảnh. Chỉ muốn ngừng bán thì dùng nút *Ẩn*.
- **Báo cáo doanh thu**: thêm theo **tuần** (12 tuần) và theo **năm**, bên cạnh ngày/tháng. Thêm bảng **sản phẩm bán ế nhất** (đang bán, số lượng đã giao thấp nhất, kể cả 0).
- **Hộp thư của khách** (`/inbox`): khách xem email/thông báo do admin hoặc hệ thống gửi (xác nhận đơn, mỗi lần admin đổi trạng thái đơn). Admin gửi thư riêng cho khách ở Admin -> Khách hàng -> Xem -> *Gửi thư cho khách hàng*. Có đếm thư chưa đọc ở biểu tượng tài khoản.
- **Bootstrap 5.3** (nạp từ CDN jsDelivr, cần internet): dùng cho thông báo (alert), phân trang, hộp thoại xác nhận (modal), form (`form-control`, `form-select`), bảng (`table-hover`, `table-responsive`), danh sách thư (`list-group`), nút đóng, chế độ sáng/tối (`data-bs-theme`). Giữ lưới 12-8-4 cột và giao diện NOIRVA tự viết; `public/assets/css/bootstrap-bridge.css` nối màu/font của NOIRVA vào Bootstrap.
- **DB**: chưa chạy migration nào -> chạy `database/migration_update_1.sql` (đã gồm tất cả). Đã chạy bản 1 cũ từ trước -> chỉ chạy `database/migration_update_2.sql` (thêm bảng hộp thư). Import mới hoàn toàn bằng `noirva_store.sql` thì không cần chạy gì.
