# Nông Sản Sạch

Website thương mại điện tử giới thiệu và kinh doanh nông sản sạch, giúp khách hàng tìm hiểu và lựa chọn các sản phẩm nông sản.

## 1. Công nghệ sử dụng

- **Backend:** PHP 8.3, Laravel 12
- **Frontend:** HTML, CSS, JavaScript, Vite
- **Database:** SQLite
- **Web Server:** Apache
- **Containerization:** Docker
- **Deployment:** Render

## 2. Cài đặt dự án trên máy cá nhân

### Yêu cầu

- PHP >= 8.2
- Composer
- Node.js và npm
- SQLite

### Các bước cài đặt

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
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

Mở PowerShell hoặc Terminal tại thư mục chứa `Dockerfile`, sau đó chạy:

```bash
docker build -t nong-san-sach:week7 .
```

### Khởi chạy container

```bash
docker run -d --name nong-san-sach-test -p 8080:80 -e APP_ENV=local -e APP_DEBUG=true -e APP_KEY="YOUR_GENERATED_KEY" nong-san-sach:week7```
php artisan key:generate --show

Thay `<your-app-key>` bằng khóa ứng dụng Laravel hợp lệ. Có thể tạo khóa bằng lệnh:

```bash
php artisan key:generate --show
```

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
| `DB_CONNECTION` | `sqlite` |
| `SESSION_DRIVER` | `file` |
| `CACHE_STORE` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `PORT` | `80` |

Không bật `APP_DEBUG=true` trên môi trường Production.

## 5. Triển khai trên Render

1. Đẩy mã nguồn dự án lên GitHub.
2. Đăng nhập Render và chọn **New → Web Service**.
3. Kết nối repository GitHub chứa dự án.
4. Chọn môi trường triển khai **Docker**.
5. Khai báo các biến môi trường trong phần **Environment**.
6. Đặt `PORT=80` để khớp với cổng Apache đang lắng nghe trong Docker image hiện tại.
7. Chọn **Deploy Web Service**.
8. Sau khi triển khai hoàn tất, truy cập URL Render cung cấp.

## 6. Lưu ý về SQLite và dữ liệu

Dự án hiện sử dụng SQLite với database mẫu tại `database/database.sqlite`. File database này được đưa vào Docker image để phục vụ kiểm thử.

Dữ liệu được ghi bên trong container hoặc image không được đảm bảo tồn tại sau khi triển khai lại hay khởi động lại dịch vụ trên môi trường có filesystem tạm thời. Khi sử dụng thực tế, cần cấu hình lưu trữ bền vững phù hợp, chẳng hạn PostgreSQL hoặc Persistent Disk, trước khi lưu dữ liệu quan trọng.

## 7. Cấu trúc Docker

Dockerfile sử dụng **multi-stage build** gồm:

- **Stage 1 – Frontend:** Cài đặt dependencies bằng Node.js và build tài nguyên frontend.
- **Stage 2 – Composer:** Cài đặt PHP dependencies bằng Composer.
- **Stage 3 – Application:** Chạy ứng dụng Laravel bằng PHP 8.3 và Apache.

Các file cấu hình Docker:

- `Dockerfile`
- `.dockerignore`

---

© 2026 Nông Sản Sạch
