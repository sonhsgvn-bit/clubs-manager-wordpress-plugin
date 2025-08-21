# Clubs Manager WordPress Plugin

WordPress plugin để quản lý câu lạc bộ bi-a với tính năng tìm kiếm, bản đồ và quản trị hoàn chỉnh.

## 🚀 Tính năng chính

### Custom Post Type & Taxonomy
- **billiard_club**: Post type cho câu lạc bộ với slug `club`
- **club_area**: Taxonomy phân loại theo khu vực với slug `club-area`

### Meta Fields đầy đủ
- `club_address`: Địa chỉ câu lạc bộ
- `club_price`: Giá VNĐ/giờ
- `club_tables`: Số bàn bi-a
- `club_parking`: Có chỗ đậu xe (yes/no)
- `club_phone`: Số điện thoại
- `club_email`: Email
- `club_website`: Website
- `club_gallery`: Thư viện ảnh (array)
- `club_hours`: Giờ mở cửa (7 ngày)
- `club_lat`: Latitude
- `club_lng`: Longitude

### Template System
- `templates/single-billiard_club.php`: Chi tiết câu lạc bộ
- `templates/archive-billiard_club.php`: Danh sách câu lạc bộ
- `templates/taxonomy-club_area.php`: Danh sách theo khu vực
- `templates/parts/club-card.php`: Card component

### Frontend Features
- Hiển thị danh sách với pagination
- Chi tiết câu lạc bộ đầy đủ thông tin
- AJAX search với filters:
  - Theo tên câu lạc bộ
  - Theo khu vực
  - Theo khoảng giá
  - Theo số bàn tối thiểu
  - Có chỗ đậu xe
- Google Maps hiển thị vị trí
- Responsive design

### Admin Features
- Meta boxes nhập thông tin
- Media uploader cho gallery
- Settings page cho Google Maps API key
- Custom columns trong admin list
- Validation và sanitization

### Shortcodes
- `[clubs_list]`: Hiển thị danh sách câu lạc bộ
- `[clubs_search]`: Form tìm kiếm
- `[clubs_map]`: Bản đồ tất cả câu lạc bộ

## 📁 Cấu trúc file

```
clubs-manager/
├── clubs-manager.php              # Main plugin file
├── uninstall.php                  # Uninstall script
├── readme.txt                     # WordPress readme
├── README.md                      # GitHub readme
├── includes/                      # PHP classes
│   ├── class-clubs-post-type.php
│   ├── class-clubs-meta-boxes.php
│   ├── class-clubs-admin.php
│   ├── class-clubs-frontend.php
│   └── class-clubs-shortcodes.php
├── templates/                     # Template files
│   ├── single-billiard_club.php
│   ├── archive-billiard_club.php
│   ├── taxonomy-club_area.php
│   └── parts/
│       └── club-card.php
├── assets/                        # CSS & JS
│   ├── css/
│   │   ├── frontend.css
│   │   └── admin.css
│   └── js/
│       ├── frontend.js
│       ├── admin.js
│       └── ajax-search.js
└── languages/                     # Translation files
    └── clubs-manager.pot
```

## 🛠️ Cài đặt

1. Upload plugin vào `/wp-content/plugins/clubs-manager/`
2. Activate plugin trong WordPress admin
3. Plugin tự động tạo custom post type và flush rewrite rules
4. Vào "Câu lạc bộ" > "Cài đặt" để nhập Google Maps API key
5. Bắt đầu thêm câu lạc bộ mới

## 🗺️ Cấu hình Google Maps

1. Lấy API key từ [Google Cloud Console](https://console.cloud.google.com/)
2. Enable Maps JavaScript API và Places API
3. Vào "Câu lạc bộ" > "Cài đặt" trong WordPress admin
4. Nhập API key và lưu

## 🔧 Sử dụng

### Thêm câu lạc bộ mới
1. Vào "Câu lạc bộ" > "Thêm mới"
2. Nhập tên và mô tả câu lạc bộ
3. Chọn khu vực từ taxonomy
4. Điền thông tin chi tiết trong meta boxes
5. Thêm ảnh đại diện và gallery
6. Đặt vị trí trên bản đồ
7. Publish

### Hiển thị trên frontend
- Archive: `/club/`
- Single: `/club/ten-cau-lac-bo/`
- Taxonomy: `/club-area/ten-khu-vuc/`

### Sử dụng shortcodes
```php
// Hiển thị danh sách câu lạc bộ
[clubs_list limit="12" area="quan-1" show_search="true"]

// Form tìm kiếm
[clubs_search show_area="true" show_price="true"]

// Bản đồ
[clubs_map height="400px" zoom="12" center_lat="10.8231" center_lng="106.6297"]
```

## 🎨 Tùy chỉnh giao diện

### Override templates
Copy template files từ plugin vào theme:
```
your-theme/
└── clubs-manager/
    ├── single-billiard_club.php
    ├── archive-billiard_club.php
    └── taxonomy-club_area.php
```

### Custom CSS
```css
/* Tùy chỉnh club cards */
.club-card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Tùy chỉnh màu chủ đạo */
.club-card-button,
.search-actions button {
    background: #your-color;
}
```

## 🔌 Hooks & Filters

```php
// Filter club query arguments
add_filter('clubs_manager_query_args', function($args) {
    // Modify query args
    return $args;
});

// Action after club saved
add_action('clubs_manager_club_saved', function($post_id) {
    // Custom logic after saving
});
```

## 🌐 URLs Structure

- Archive: `yourdomain.com/club/`
- Single: `yourdomain.com/club/ten-cau-lac-bo/`
- Taxonomy: `yourdomain.com/club-area/quan-1/`
- Search: `yourdomain.com/club/?search_name=abc&search_area=1`

## 🛡️ Security Features

- Nonce verification cho tất cả forms
- Data sanitization và validation
- Capability checks
- SQL injection protection
- XSS protection

## 📱 Responsive Design

- Mobile-first approach
- Bootstrap-like grid system
- Touch-friendly interface
- Optimized for all screen sizes

## 🚨 Fix Page Not Found

Plugin tự động fix lỗi 404 bằng cách:

1. **Proper post type registration** với rewrite rules
2. **Activation hook** flush rewrite rules
3. **Template include filter** load plugin templates
4. **Correct permalink structure** với slug 'club'

## 🔄 Changelog

### v1.0.0
- Initial release
- Custom post type "billiard_club"
- Taxonomy "club_area" 
- Complete meta fields
- Template system
- AJAX search functionality
- Google Maps integration
- Admin interface
- Shortcodes support
- Responsive design

## 📄 License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## 👨‍💻 Author

**sonhsgvn-bit**

- GitHub: [sonhsgvn-bit](https://github.com/sonhsgvn-bit)

## 🤝 Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📞 Support

Nếu gặp vấn đề, vui lòng tạo issue trên GitHub repository.