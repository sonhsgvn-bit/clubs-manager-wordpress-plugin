# Clubs Manager - WordPress Plugin

> Plugin WordPress hoàn chỉnh cho quản lý câu lạc bộ bi-a với tìm kiếm nâng cao, Google Maps và giao diện responsive.

[![WordPress](https://img.shields.io/badge/WordPress-5.0+-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

## 🎯 Tính năng chính

### 📋 Quản lý thông tin chi tiết
- **Custom Post Type "billiard-club"** với đầy đủ meta fields
- Địa chỉ, giá cả (VNĐ/giờ), số bàn bi-a
- Thông tin liên hệ (điện thoại, email, website, Facebook)
- Tiện ích (chỗ để xe, WiFi, điều hòa, cho thuê cơ, dịch vụ ăn uống)
- Giờ mở cửa theo từng ngày trong tuần
- Gallery hình ảnh với lightbox

### 📍 Taxonomy và phân loại
- **Taxonomy "club-area"** cho khu vực
- Hiển thị câu lạc bộ theo khu vực
- Thống kê và quick stats

### 🔍 Tìm kiếm và lọc nâng cao
- Form tìm kiếm với AJAX
- Lọc theo khu vực, giá cả, số bàn
- Lọc theo tiện ích có sẵn
- Sắp xếp theo nhiều tiêu chí
- View toggle (grid/list)

### 🗺️ Tích hợp Google Maps
- Hiển thị vị trí chính xác trên bản đồ
- Custom map styles
- Info windows với thông tin chi tiết
- Bản đồ tổng quan tất cả câu lạc bộ

### 📱 Giao diện responsive
- Design mobile-first
- Tối ưu cho tất cả thiết bị
- Fast loading với lazy load images
- Modern, clean design

### ⚙️ Admin interface
- Meta boxes dễ sử dụng
- Media upload cho gallery
- Settings page với Google Maps API config
- Dashboard widget thống kê
- Validation và tooltips

## 🚀 Cài đặt

### 1. Tải và cài đặt plugin

```bash
# Clone repository
git clone https://github.com/sonhsgvn-bit/clubs-manager-wordpress-plugin.git

# Hoặc download ZIP và extract vào thư mục plugins
```

### 2. Kích hoạt plugin
1. Upload thư mục `clubs-manager` vào `/wp-content/plugins/`
2. Kích hoạt plugin trong WordPress Admin
3. Vào 'Câu lạc bộ bi-a' > 'Cài đặt' để cấu hình

### 3. Cấu hình Google Maps (tùy chọn)
1. Lấy API key từ [Google Cloud Console](https://console.cloud.google.com/)
2. Enable Maps JavaScript API và Geocoding API
3. Thêm API key trong plugin settings

## 📖 Sử dụng

### Shortcodes

Plugin cung cấp 3 shortcodes chính:

#### `[clubs_list]` - Danh sách câu lạc bộ
```php
[clubs_list limit="6" layout="grid" area="quan-1"]
[clubs_list limit="12" layout="list" orderby="price" order="asc"]
```

**Tham số:**
- `area`: Slug của khu vực
- `limit`: Số lượng hiển thị (mặc định: 12)
- `layout`: grid hoặc list (mặc định: grid)
- `orderby`: title, price, date (mặc định: title)
- `order`: ASC hoặc DESC (mặc định: ASC)

#### `[clubs_search]` - Form tìm kiếm
```php
[clubs_search show_filters="true" ajax="true"]
[clubs_search show_filters="false" ajax="false"]
```

**Tham số:**
- `show_filters`: true/false - hiển thị bộ lọc nâng cao
- `ajax`: true/false - sử dụng AJAX search
- `results_per_page`: số kết quả mỗi trang

#### `[clubs_map]` - Bản đồ
```php
[clubs_map height="400px" zoom="12"]
[clubs_map height="500px" zoom="15" area="quan-1"]
```

**Tham số:**
- `height`: chiều cao bản đồ (mặc định: 400px)
- `zoom`: mức zoom (mặc định: 15)
- `center`: tọa độ trung tâm (lat,lng)
- `area`: hiển thị chỉ câu lạc bộ trong khu vực

## 🛠️ Customization

### Template Override

Copy template files vào theme của bạn:

```
your-theme/
├── clubs-manager/
│   ├── single-club.php
│   ├── archive-clubs.php
│   └── club-card.php
```

### Hooks và Filters

```php
// Filter dữ liệu club trước khi hiển thị
add_filter( 'clubs_manager_club_data', 'custom_club_data_filter' );

function custom_club_data_filter( $data ) {
    // Customize club data
    return $data;
}

// Action sau khi club được lưu
add_action( 'clubs_manager_club_saved', 'custom_club_saved_action' );

function custom_club_saved_action( $club_id ) {
    // Do something after club is saved
}
```

### Custom CSS

```css
/* Override plugin styles */
.club-card {
    border: 2px solid #your-color;
}

.clubs-search-form {
    background: #your-background;
}
```

## 📁 Cấu trúc files

```
clubs-manager/
├── clubs-manager.php          # Main plugin file
├── uninstall.php             # Uninstall cleanup
├── readme.txt                # WordPress plugin info
├── README.md                 # GitHub readme
├── includes/                 # PHP classes
│   ├── class-club-post-type.php
│   ├── class-club-meta-boxes.php
│   ├── class-club-admin.php
│   ├── class-club-frontend.php
│   ├── class-club-shortcode.php
│   ├── class-club-ajax.php
│   └── class-club-maps.php
├── templates/                # Template files
│   ├── single-club.php
│   ├── archive-clubs.php
│   └── club-card.php
├── assets/                   # CSS/JS assets
│   ├── css/
│   │   ├── frontend.css
│   │   └── admin.css
│   └── js/
│       ├── frontend.js
│       └── admin.js
└── languages/               # Translation files
    └── clubs-manager.pot
```

## 🔧 Yêu cầu kỹ thuật

- **WordPress:** 5.0+
- **PHP:** 7.4+
- **MySQL:** 5.6+
- **Google Maps API Key:** Tùy chọn (cho tính năng maps)

## 🌟 Features Roadmap

- [ ] Reviews và rating system
- [ ] Booking system integration
- [ ] Multi-language support
- [ ] Advanced search filters
- [ ] Club comparison feature
- [ ] Social media integration
- [ ] Email notifications
- [ ] CSV import/export
- [ ] REST API endpoints
- [ ] Mobile app integration

## 🤝 Đóng góp

Chúng tôi hoan nghênh mọi đóng góp! Vui lòng:

1. Fork repository
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Mở Pull Request

### Development Setup

```bash
# Clone repository
git clone https://github.com/sonhsgvn-bit/clubs-manager-wordpress-plugin.git

# Tạo symbolic link trong WordPress
ln -s /path/to/clubs-manager-wordpress-plugin /path/to/wordpress/wp-content/plugins/clubs-manager

# Hoặc copy files
cp -r clubs-manager-wordpress-plugin /path/to/wordpress/wp-content/plugins/clubs-manager
```

## 📝 License

Plugin này được phát hành dưới [GPL v2 License](https://www.gnu.org/licenses/gpl-2.0.html).

## 📞 Hỗ trợ

- **GitHub Issues:** [Báo cáo lỗi hoặc yêu cầu tính năng](https://github.com/sonhsgvn-bit/clubs-manager-wordpress-plugin/issues)
- **Email:** support@clubsmanager.example.com
- **Documentation:** [Wiki](https://github.com/sonhsgvn-bit/clubs-manager-wordpress-plugin/wiki)

## 🙏 Credits

Developed with ❤️ for the Vietnamese billiards community.

### Third-party Libraries
- Google Maps JavaScript API
- WordPress Media Library
- jQuery and related plugins

---

**⭐ Nếu plugin hữu ích, hãy cho chúng tôi một star trên GitHub!**