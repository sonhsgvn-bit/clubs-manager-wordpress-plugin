=== Clubs Manager - Quản lý câu lạc bộ bi-a ===
Contributors: clubsmanager
Donate link: https://example.com/donate
Tags: billiards, clubs, management, maps, vietnamese
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin WordPress hoàn chỉnh cho quản lý câu lạc bộ bi-a với tìm kiếm nâng cao, Google Maps và giao diện responsive.

== Description ==

Clubs Manager là plugin WordPress hoàn chỉnh được thiết kế đặc biệt cho việc quản lý và hiển thị thông tin các câu lạc bộ bi-a tại Việt Nam. Plugin cung cấp đầy đủ các tính năng cần thiết để tạo ra một website directory chuyên nghiệp.

### Tính năng chính

**🎯 Quản lý thông tin chi tiết:**
* Custom Post Type "billiard-club" với đầy đủ meta fields
* Địa chỉ, giá cả (VNĐ/giờ), số bàn bi-a
* Thông tin liên hệ (điện thoại, email, website, Facebook)
* Tiện ích (chỗ để xe, WiFi, điều hòa, cho thuê cơ, dịch vụ ăn uống)
* Giờ mở cửa theo từng ngày trong tuần
* Gallery hình ảnh với lightbox

**📍 Taxonomy và phân loại:**
* Taxonomy "club-area" cho khu vực
* Hiển thị câu lạc bộ theo khu vực
* Thống kê và quick stats

**🔍 Tìm kiếm và lọc nâng cao:**
* Form tìm kiếm với AJAX
* Lọc theo khu vực, giá cả, số bàn
* Lọc theo tiện ích có sẵn
* Sắp xếp theo nhiều tiêu chí
* View toggle (grid/list)

**🗺️ Tích hợp Google Maps:**
* Hiển thị vị trí chính xác trên bản đồ
* Custom map styles
* Info windows với thông tin chi tiết
* Bản đồ tổng quan tất cả câu lạc bộ

**📱 Giao diện responsive:**
* Design mobile-first
* Tối ưu cho tất cả thiết bị
* Fast loading với lazy load images
* Modern, clean design

**⚙️ Admin interface:**
* Meta boxes dễ sử dụng
* Media upload cho gallery
* Settings page với Google Maps API config
* Dashboard widget thống kê
* Validation và tooltips

### Shortcodes

Plugin cung cấp 3 shortcodes chính:

* `[clubs_list]` - Hiển thị danh sách câu lạc bộ
* `[clubs_search]` - Form tìm kiếm với bộ lọc
* `[clubs_map]` - Bản đồ tất cả câu lạc bộ

### Ví dụ sử dụng

```
[clubs_list limit="6" layout="grid" area="quan-1"]
[clubs_search show_filters="true" ajax="true"]
[clubs_map height="400px" zoom="12"]
```

### Tính năng kỹ thuật

* WordPress 5.0+
* PHP 7.4+
* Responsive design (Bootstrap-like grid)
* AJAX search without page reload
* Google Maps API integration
* Image optimization
* SEO friendly URLs
* Security: nonces, sanitization, validation
* Internationalization ready (Vietnamese)
* Structured data for search engines

== Installation ==

1. Upload plugin files to `/wp-content/plugins/clubs-manager/` directory
2. Activate plugin through 'Plugins' menu in WordPress
3. Go to 'Câu lạc bộ bi-a' > 'Cài đặt' to configure Google Maps API key
4. Start adding clubs and areas
5. Use shortcodes in posts/pages or customize templates

### Cấu hình Google Maps

1. Get Google Maps API key from [Google Cloud Console](https://console.cloud.google.com/)
2. Enable Maps JavaScript API and Geocoding API
3. Add API key in plugin settings
4. Set default zoom level and other preferences

== Frequently Asked Questions ==

= Plugin có miễn phí không? =

Có, plugin hoàn toàn miễn phí và mã nguồn mở.

= Có cần Google Maps API key không? =

Google Maps API key là tùy chọn nhưng được khuyến nghị để sử dụng tính năng bản đồ.

= Plugin có hỗ trợ đa ngôn ngữ không? =

Plugin được thiết kế với ngôn ngữ tiếng Việt và hỗ trợ internationalization.

= Có thể tùy chỉnh giao diện không? =

Có, bạn có thể override templates bằng cách copy chúng vào theme folder.

= Plugin có tương thích với theme nào? =

Plugin được thiết kế để tương thích với hầu hết các WordPress themes.

== Screenshots ==

1. Giao diện danh sách câu lạc bộ với search form
2. Trang chi tiết câu lạc bộ với maps và gallery
3. Admin interface - thêm/sửa câu lạc bộ
4. Meta boxes với đầy đủ thông tin
5. Settings page với Google Maps config
6. Dashboard widget thống kê
7. Mobile responsive design

== Changelog ==

= 1.0.0 =
* Initial release
* Complete club management system
* Google Maps integration
* AJAX search and filtering
* Responsive templates
* Admin interface with meta boxes
* Shortcodes support
* Vietnamese language support

== Upgrade Notice ==

= 1.0.0 =
Initial release of Clubs Manager plugin.

== Developer Documentation ==

### Template Override

Copy template files from `/plugins/clubs-manager/templates/` to your theme:

```
your-theme/
  clubs-manager/
    single-club.php
    archive-clubs.php
    club-card.php
```

### Hooks and Filters

Plugin provides various hooks for customization:

```php
// Filter club data before display
add_filter( 'clubs_manager_club_data', 'custom_club_data_filter' );

// Action after club is saved
add_action( 'clubs_manager_club_saved', 'custom_club_saved_action' );
```

### Custom Fields

Add custom fields to clubs:

```php
add_action( 'clubs_manager_meta_boxes', 'add_custom_club_fields' );
```

For more documentation, visit plugin website.

== Support ==

For support, feature requests, or bug reports, please visit:
* GitHub: https://github.com/sonhsgvn-bit/clubs-manager-wordpress-plugin
* Email: support@clubsmanager.example.com

== Credits ==

Developed with ❤️ for the Vietnamese billiards community.