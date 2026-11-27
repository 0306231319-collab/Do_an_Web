
# Nông Sản Sạch

Website thương mại điện tử giới thiệu và kinh doanh nông sản sạch, giúp khách hàng tìm hiểu và lựa chọn các sản phẩm nông sản.

## 1. Công nghệ sử dụng

- **Backend:** PHP 8.3, Laravel 12
- **Frontend:** HTML, CSS, JavaScript, Vite
- **Database:** MySQL
- **Web Server:** Apache
- **Containerization:** Docker
- **Deployment:** Render

## 2. Cài đặt dự án trên máy cá nhân

### Yêu cầu

- PHP >= 8.2
- Composer
- Node.js và npm
- MySQL Server

### Các bước cài đặt

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Cấu hình thông tin MySQL trong file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nong_san_sach
DB_USERNAME=root
DB_PASSWORD=
```

Tạo database `nong_san_sach` trong MySQL trước khi chạy migration.

```bash
php artisan migrate
npm run build
```

### Khởi chạy ứng dụng

```bash
php artisan serve
```

Truy cập: http://127.0.0.1:8000

## 3. Chạy bằng Docker

### Build image

Mở PowerShell hoặc Terminal tại thư mục chứa Dockerfile:

```bash
docker build -t nong-san-sach:week7 .
```

### Khởi chạy container

Nếu MySQL đang chạy trực tiếp trên máy Windows, sử dụng:

```bash
docker run -d --name nong-san-sach-test -p 8080:80 --add-host=host.docker.internal:host-gateway -e APP_ENV=production -e APP_DEBUG=false -e APP_KEY="YOUR_GENERATED_KEY" -e DB_CONNECTION=mysql -e DB_HOST=host.docker.internal -e DB_PORT=3306 -e DB_DATABASE=nong_san_sach -e DB_USERNAME=root -e DB_PASSWORD="YOUR_DB_PASSWORD" nong-san-sach:week7
```

Thay `YOUR_GENERATED_KEY` bằng khóa được tạo từ:

```bash
php artisan key:generate --show
```

Thay `YOUR_DB_PASSWORD` bằng mật khẩu MySQL thực tế.

Đảm bảo MySQL cho phép kết nối từ Docker và database đã được tạo.

Truy cập website: http://localhost:8080

### Kiểm tra container

```bash
docker ps
docker logs nong-san-sach-test
```

### Dừng và xóa container kiểm thử

```bash
docker stop nong-san-sach-test
docker rm nong-san-sach-test
```

## 4. Cấu hình môi trường Production

Khai báo các biến môi trường trong phần Environment của dịch vụ triển khai:

| Biến | Giá trị |
|---|---|
| `APP_NAME` | `Nong San Sach` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Khóa ứng dụng Laravel hợp lệ |
| `APP_URL` | URL ứng dụng được Render cung cấp |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | Hostname của MySQL server |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | Tên database |
| `DB_USERNAME` | Tên tài khoản MySQL |
| `DB_PASSWORD` | Mật khẩu MySQL |
| `SESSION_DRIVER` | `file` |
| `CACHE_STORE` | `file` |
| `QUEUE_CONNECTION` | `sync` |

Không bật `APP_DEBUG=true` trên môi trường Production.

## 5. Triển khai trên Render

1. Đẩy mã nguồn dự án lên GitHub.
2. Đăng nhập Render và chọn New → Web Service.
3. Kết nối repository GitHub chứa dự án.
4. Chọn môi trường triển khai Docker.
5. Khai báo các biến môi trường trong phần Environment.
6. Cấu hình kết nối tới MySQL server có thể truy cập từ Render.
7. Chọn Deploy Web Service.
8. Sau khi triển khai hoàn tất, truy cập URL Render cung cấp.

## 6. Lưu ý về MySQL và dữ liệu

Dự án sử dụng MySQL làm hệ quản trị cơ sở dữ liệu.

Database cần được tạo trước khi chạy migration. Khi triển khai Production, cần sử dụng MySQL server có khả năng lưu trữ dữ liệu bền vững và cho phép ứng dụng kết nối qua mạng.

Không nên sử dụng database MySQL chỉ chạy trong container ứng dụng vì dữ liệu có thể mất khi container bị xóa hoặc tạo lại.

## 7. Cấu trúc Docker

Dockerfile sử dụng multi-stage build gồm:

- **Stage 1 – Frontend:** Cài đặt dependencies bằng Node.js và build tài nguyên frontend.
- **Stage 2 – Composer:** Cài đặt PHP dependencies bằng Composer.
- **Stage 3 – Application:** Chạy ứng dụng Laravel bằng PHP 8.3, Apache và extension PDO MySQL.

Các file cấu hình Docker:

- `Dockerfile`
- `.dockerignore`

---

© 2026 Nông Sản Sạch
