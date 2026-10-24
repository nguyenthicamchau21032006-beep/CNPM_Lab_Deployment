# Lab PHP + MySQL + Docker Compose (không tự build image)

## Mục tiêu
- Đưa source PHP lên GitHub cá nhân.
- Clone source về máy.
- Pull image PHP/Apache và MySQL có sẵn.
- Chạy website bằng Docker Compose, không cần cài PHP/MySQL trực tiếp trên máy.

## Yêu cầu
- Git
- Docker Desktop (Windows/macOS) hoặc Docker Engine + Docker Compose (Linux)
- Tài khoản GitHub

## 1. Đưa source lên GitHub cá nhân
Tạo repository mới trên GitHub, ví dụ `cnpm-php-mysql-lab`.
Mở terminal tại thư mục dự án:

```bash
git init
git add .
git commit -m "Initial PHP MySQL lab"
git branch -M main
git remote add origin https://github.com/<YOUR_USERNAME>/cnpm-php-mysql-lab.git
git push -u origin main
```

Thay `<YOUR_USERNAME>` bằng username GitHub của bạn. Nếu GitHub đã tạo README sẵn trong repository, hãy clone repository đó trước rồi chép các tệp lab vào để tránh xung đột lịch sử.

## 2. Clone source về máy
Trong thư mục khác, chạy:

```bash
git clone https://github.com/<YOUR_USERNAME>/cnpm-php-mysql-lab.git
cd cnpm-php-mysql-lab
```

## 3. Cấu hình môi trường
Sao chép `.env.example` thành `.env`.

Windows PowerShell:
```powershell
Copy-Item .env.example .env
```

macOS/Linux:
```bash
cp .env.example .env
```

Có thể giữ mật khẩu mẫu cho bài lab chỉ chạy local; không dùng mật khẩu mẫu trong môi trường thật.

## 4. Khởi động
```bash
docker compose pull
docker compose up -d
docker compose ps
```

Lần đầu, Docker tải các image có sẵn từ registry. Compose không build image vì file `compose.yaml` không có `build:` và dự án không cần Dockerfile.

Mở: http://localhost:8080

## 5. Kiểm tra và quản lý
```bash
docker compose logs -f app
docker compose logs -f db
docker compose ps
docker compose down
```

Dữ liệu MySQL được giữ trong named volume khi chạy `docker compose down`.
Để xóa cả dữ liệu database và làm lại từ đầu (cẩn thận):
```bash
docker compose down -v
```

Nếu cổng 8080 đang được sử dụng, đổi `APP_PORT=8081` trong `.env`, sau đó chạy `docker compose up -d` và mở `http://localhost:8081`.

## Bài tập thực hành
1. Đổi tiêu đề website và màu giao diện.
2. Thêm trường `priority` (Low/Medium/High) cho công việc.
3. Thêm chức năng sửa tên công việc.
4. Tạo branch `feature/<ten-chuc-nang>`, commit và push.
5. Chụp minh chứng: repository GitHub, `docker compose ps`, website trên trình duyệt.
6. Giải thích vai trò của app container, database container, port mapping, environment variables và volume.

