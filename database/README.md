# Database
`init.sql` được MySQL chạy tự động khi khởi tạo volume lần đầu.
Nếu bạn sửa `init.sql` sau khi database đã được tạo, nội dung mới không tự chạy lại.
Để khởi tạo lại dữ liệu từ đầu, dùng `docker compose down -v` (lệnh này xóa dữ liệu đang lưu).
