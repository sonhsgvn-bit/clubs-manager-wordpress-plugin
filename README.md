# Clubs Manager - Quản lý câu lạc bộ bi-a

Plugin WordPress hoàn chỉnh để quản lý và hiển thị thông tin các câu lạc bộ bi-a với tính năng tìm kiếm nâng cao.

## 🎯 Tính năng chính

### 1. Custom Post Type "billiard-club"
- Tên câu lạc bộ (title)
- Mô tả chi tiết (content)
- Meta fields:
  - Địa chỉ
  - Giá (VNĐ/giờ)
  - Số bàn bi-a
  - Chỗ đậu xe (có/không)
  - Số điện thoại
  - Email
  - Website
  - Gallery hình ảnh (nhiều ảnh)
  - Giờ mở cửa (7 ngày trong tuần)
  - Tọa độ Google Maps (lat/lng)

### 2. Taxonomy "club-area" 
- Phân loại theo khu vực (Quận 1, Quận 3, Bình Thạnh, etc.)

### 3. Frontend Features
- Trang archive hiển thị danh sách câu lạc bộ
- Trang single hiển thị chi tiết câu lạc bộ
- Form tìm kiếm với bộ lọc:
  - Tìm kiếm theo tên
  - Lọc theo khu vực
  - Lọc theo khoảng giá
  - Lọc theo số bàn
  - Lọc có chỗ đậu xe
- AJAX search không reload trang
- Google Maps hiển thị vị trí
- Responsive design

### 4. Admin Features
- Meta boxes nhập thông tin câu lạc bộ
- Media uploader cho gallery
- Settings page cấu hình Google Maps API
- Custom columns trong admin list

### 5. Shortcodes
- `[clubs_list]` - Hiển thị danh sách câu lạc bộ
- `[clubs_search]` - Form tìm kiếm
- `[clubs_map]` - Bản đồ tất cả câu lạc bộ

## 📁 Cấu trúc file

```
clubs-manager/
├── clubs-manager.php (Main plugin file)
├── uninstall.php
├── readme.txt
├── README.md
├── includes/
│   ├── class-clubs-post-type.php
│   ├── class-clubs-meta-boxes.php
│   ├── class-clubs-admin.php
│   └── class-clubs-frontend.php
├── templates/
│   ├── single-club.php
│   ├── archive-clubs.php
│   └── club-card.php
├── assets/
│   ├── css/
│   │   ├── frontend.css
│   │   └── admin.css
│   └── js/
│       ├── frontend.js
│       └── admin.js
└── languages/
    └── clubs-manager.pot
```

## 🚀 Cài đặt

1. Upload thư mục plugin vào `/wp-content/plugins/`
2. Kích hoạt plugin thông qua menu 'Plugins' trong WordPress
3. Vào menu "Câu lạc bộ bi-a" > "Cài đặt" để cấu hình Google Maps API Key
4. Bắt đầu thêm các câu lạc bộ bi-a

### Cấu hình Google Maps

1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Tạo project mới hoặc chọn project có sẵn
3. Bật Google Maps JavaScript API
4. Tạo API Key
5. Nhập API Key vào trang cài đặt plugin

## 💻 Yêu cầu kỹ thuật

- WordPress 5.0+
- PHP 7.4+
- Google Maps API Key (cho chức năng bản đồ)

## 🎨 Sử dụng Shortcodes

### Hiển thị danh sách câu lạc bộ
```
[clubs_list limit="12" area="quan-1" orderby="date" order="DESC"]
```

### Form tìm kiếm
```
[clubs_search ajax="true"]
```

### Bản đồ tất cả câu lạc bộ
```
[clubs_map height="400px" zoom="12"]
```

## 🔧 Tùy chỉnh Template

Bạn có thể tùy chỉnh giao diện bằng cách copy các file template từ plugin sang theme:

1. Tạo thư mục `clubs-manager` trong theme của bạn
2. Copy các file từ `plugins/clubs-manager/templates/` vào thư mục vừa tạo
3. Tùy chỉnh theo ý muốn

## 🌐 Đa ngôn ngữ

Plugin hỗ trợ đầy đủ tiếng Việt và có thể dễ dàng dịch sang các ngôn ngữ khác thông qua file `.pot` trong thư mục `languages/`.

## 🔒 Bảo mật

Plugin tuân thủ các tiêu chuẩn bảo mật WordPress:
- Sử dụng nonces cho các form
- Sanitization và validation dữ liệu đầu vào
- Escape dữ liệu đầu ra
- Kiểm tra quyền người dùng

## 📱 Responsive Design

Giao diện được thiết kế responsive, tương thích với mọi thiết bị từ desktop đến mobile.

## 🆘 Hỗ trợ

Nếu bạn gặp vấn đề hoặc cần hỗ trợ:
1. Kiểm tra [Issues](https://github.com/sonhsgvn-bit/clubs-manager-wordpress-plugin/issues) có sẵn
2. Tạo issue mới nếu chưa có

## 📄 License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## 🤝 Đóng góp

Chúng tôi hoan nghênh mọi đóng góp! Vui lòng:
1. Fork repository
2. Tạo feature branch
3. Commit thay đổi
4. Tạo Pull Request

## 📊 Development

### Cài đặt môi trường development

```bash
git clone https://github.com/sonhsgvn-bit/clubs-manager-wordpress-plugin.git
cd clubs-manager-wordpress-plugin
```

### Standards

- Tuân thủ WordPress Coding Standards
- Sử dụng WordPress APIs
- Tương thích với WordPress Multisite
- Hỗ trợ các phiên bản PHP từ 7.4+

## 📈 Roadmap

- [ ] Import/Export dữ liệu Excel
- [ ] Tích hợp với các plugin booking
- [ ] API REST cho mobile apps
- [ ] Tính năng đánh giá và review
- [ ] Tích hợp thanh toán online
- [ ] Báo cáo thống kê chi tiết

---

Made with ❤️ for Vietnamese billiard clubs community