=== Clubs Manager ===
Contributors: sonhsgvn-bit
Tags: billiard, clubs, management, maps, search
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WordPress plugin để quản lý câu lạc bộ bi-a với tính năng tìm kiếm, bản đồ và quản trị hoàn chỉnh.

== Description ==

Clubs Manager là plugin WordPress mạnh mẽ giúp bạn quản lý và hiển thị thông tin câu lạc bộ bi-a một cách chuyên nghiệp. Plugin cung cấp tất cả các tính năng cần thiết để tạo ra một website quản lý câu lạc bộ hoàn chỉnh.

= Tính năng chính =

* **Custom Post Type**: Quản lý thông tin câu lạc bộ với custom post type "billiard_club"
* **Taxonomy**: Phân loại câu lạc bộ theo khu vực với taxonomy "club_area"
* **Meta Fields**: Lưu trữ thông tin chi tiết như địa chỉ, giá cả, số bàn, giờ mở cửa, v.v.
* **Google Maps**: Hiển thị vị trí câu lạc bộ trên bản đồ
* **AJAX Search**: Tìm kiếm câu lạc bộ theo nhiều tiêu chí với AJAX
* **Responsive Design**: Giao diện tương thích với mọi thiết bị
* **Template System**: Hệ thống template riêng cho plugin
* **Shortcodes**: Các shortcode để hiển thị danh sách và bản đồ

= Meta Fields =

* Địa chỉ câu lạc bộ
* Giá VNĐ/giờ
* Số bàn bi-a
* Có chỗ đậu xe (có/không)
* Số điện thoại
* Email
* Website
* Thư viện ảnh
* Giờ mở cửa (7 ngày trong tuần)
* Tọa độ GPS (Latitude/Longitude)

= Shortcodes =

* `[clubs_list]` - Hiển thị danh sách câu lạc bộ
* `[clubs_search]` - Hiển thị form tìm kiếm
* `[clubs_map]` - Hiển thị bản đồ tất cả câu lạc bộ

= Template Files =

Plugin sử dụng các template files riêng:
* `single-billiard_club.php` - Chi tiết câu lạc bộ
* `archive-billiard_club.php` - Danh sách câu lạc bộ
* `taxonomy-club_area.php` - Danh sách theo khu vực

== Installation ==

1. Upload plugin files to `/wp-content/plugins/clubs-manager/` directory
2. Activate plugin trong WordPress admin
3. Plugin sẽ tự động tạo custom post type và flush rewrite rules
4. Vào "Câu lạc bộ" > "Cài đặt" để nhập Google Maps API key
5. Bắt đầu thêm câu lạc bộ mới

= Cấu hình Google Maps =

1. Lấy API key từ Google Cloud Console
2. Vào "Câu lạc bộ" > "Cài đặt" 
3. Nhập API key vào field "Google Maps API Key"
4. Lưu cài đặt

== Frequently Asked Questions ==

= Plugin có tương thích với theme hiện tại không? =

Có, plugin sử dụng template system riêng và sẽ hoạt động với mọi theme WordPress.

= Có thể tùy chỉnh giao diện không? =

Có, bạn có thể copy các template files vào theme để tùy chỉnh hoặc sửa đổi CSS.

= Plugin có hỗ trợ đa ngôn ngữ không? =

Có, plugin được chuẩn bị sẵn để dịch và hỗ trợ tiếng Việt mặc định.

= URLs của câu lạc bộ như thế nào? =

URLs sẽ có dạng: `yoursite.com/club/ten-cau-lac-bo/`

== Screenshots ==

1. Danh sách câu lạc bộ với form tìm kiếm
2. Chi tiết câu lạc bộ với Google Maps
3. Giao diện admin để thêm câu lạc bộ
4. Meta boxes cho thông tin chi tiết
5. Cài đặt Google Maps API

== Changelog ==

= 1.0.0 =
* Phiên bản đầu tiên
* Custom post type "billiard_club" 
* Taxonomy "club_area"
* Meta fields đầy đủ
* Template system
* AJAX search
* Google Maps integration
* Shortcodes
* Admin interface
* Responsive design

== Upgrade Notice ==

= 1.0.0 =
Phiên bản đầu tiên của plugin. Hãy backup website trước khi cài đặt.

== Developer Notes ==

Plugin được phát triển theo WordPress coding standards và best practices:

* Proper sanitization và validation
* Nonce verification cho security
* Proper enqueue scripts/styles
* Template hierarchy
* AJAX với proper nonces
* Responsive design
* SEO friendly URLs
* Vietnamese language support

= File Structure =

```
clubs-manager/
├── clubs-manager.php
├── uninstall.php
├── readme.txt
├── includes/
│   ├── class-clubs-post-type.php
│   ├── class-clubs-meta-boxes.php
│   ├── class-clubs-admin.php
│   ├── class-clubs-frontend.php
│   └── class-clubs-shortcodes.php
├── templates/
│   ├── single-billiard_club.php
│   ├── archive-billiard_club.php
│   ├── taxonomy-club_area.php
│   └── parts/
│       └── club-card.php
├── assets/
│   ├── css/
│   │   ├── frontend.css
│   │   └── admin.css
│   └── js/
│       ├── frontend.js
│       ├── admin.js
│       └── ajax-search.js
└── languages/
    └── clubs-manager.pot
```