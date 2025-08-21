=== Clubs Manager - Quản lý câu lạc bộ bi-a ===
Contributors: yourname
Tags: billiards, clubs, management, vietnamese, directory
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Plugin WordPress hoàn chỉnh để quản lý và hiển thị thông tin các câu lạc bộ bi-a với tính năng tìm kiếm nâng cao.

== Description ==

Clubs Manager là plugin WordPress chuyên dụng để quản lý thông tin các câu lạc bộ bi-a. Plugin cung cấp đầy đủ các tính năng cần thiết để tạo một website directory hoàn chỉnh cho các câu lạc bộ bi-a tại Việt Nam.

= Tính năng chính =

* **Custom Post Type "billiard-club"** với đầy đủ thông tin:
  * Tên câu lạc bộ
  * Mô tả chi tiết
  * Địa chỉ
  * Giá thuê (VNĐ/giờ)
  * Số bàn bi-a
  * Thông tin chỗ đậu xe
  * Thông tin liên hệ (phone, email, website)
  * Thư viện hình ảnh
  * Giờ mở cửa (7 ngày/tuần)
  * Tọa độ Google Maps

* **Taxonomy "club-area"** để phân loại theo khu vực (Quận 1, Quận 3, Bình Thạnh, v.v.)

* **Giao diện Frontend:**
  * Trang archive hiển thị danh sách câu lạc bộ
  * Trang single hiển thị chi tiết câu lạc bộ
  * Form tìm kiếm nâng cao với nhiều bộ lọc
  * AJAX search không reload trang
  * Google Maps tích hợp
  * Responsive design

* **Quản trị Admin:**
  * Meta boxes đầy đủ cho việc nhập thông tin
  * Media uploader cho gallery
  * Settings page cấu hình Google Maps API
  * Custom columns trong danh sách admin

* **Shortcodes:**
  * `[clubs_list]` - Hiển thị danh sách câu lạc bộ
  * `[clubs_search]` - Form tìm kiếm
  * `[clubs_map]` - Bản đồ tất cả câu lạc bộ

= Tìm kiếm nâng cao =

Plugin hỗ trợ tìm kiếm theo nhiều tiêu chí:
* Tìm kiếm theo tên câu lạc bộ
* Lọc theo khu vực
* Lọc theo khoảng giá
* Lọc theo số bàn bi-a tối thiểu
* Lọc theo việc có chỗ đậu xe hay không

= Hỗ trợ đa ngôn ngữ =

Plugin được thiết kế với hỗ trợ tiếng Việt đầy đủ và có thể dễ dàng dịch sang các ngôn ngữ khác.

= Yêu cầu kỹ thuật =

* WordPress 5.0 trở lên
* PHP 7.4 trở lên
* Google Maps API Key (để hiển thị bản đồ)

== Installation ==

1. Upload thư mục plugin vào `/wp-content/plugins/`
2. Kích hoạt plugin thông qua menu 'Plugins' trong WordPress
3. Vào menu "Câu lạc bộ bi-a" > "Cài đặt" để cấu hình Google Maps API Key
4. Bắt đầu thêm các câu lạc bộ bi-a

= Cấu hình Google Maps =

1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Tạo project mới hoặc chọn project có sẵn
3. Bật Google Maps JavaScript API
4. Tạo API Key
5. Nhập API Key vào trang cài đặt plugin

== Frequently Asked Questions ==

= Plugin có hoạt động với theme bất kỳ không? =

Có, plugin sử dụng template riêng và sẽ hoạt động với hầu hết các theme WordPress.

= Tôi có thể tùy chỉnh giao diện không? =

Có, bạn có thể copy các file template từ thư mục plugin sang theme và tùy chỉnh theo ý muốn.

= Plugin có hỗ trợ SEO không? =

Có, plugin được thiết kế SEO-friendly với URL thân thiện và meta data đầy đủ.

= Tôi có thể import dữ liệu từ Excel không? =

Hiện tại plugin chưa hỗ trợ import trực tiếp, nhưng bạn có thể sử dụng các plugin import WordPress khác.

== Screenshots ==

1. Trang danh sách câu lạc bộ với form tìm kiếm
2. Trang chi tiết câu lạc bộ với bản đồ
3. Form thêm/sửa câu lạc bộ trong admin
4. Cài đặt plugin và Google Maps API
5. Danh sách câu lạc bộ trong admin

== Changelog ==

= 1.0.0 =
* Phiên bản đầu tiên
* Custom post type cho câu lạc bộ bi-a
* Taxonomy cho khu vực
* Form tìm kiếm nâng cao với AJAX
* Google Maps tích hợp
* Shortcodes đầy đủ
* Giao diện responsive
* Hỗ trợ tiếng Việt

== Upgrade Notice ==

= 1.0.0 =
Phiên bản đầu tiên của plugin. Vui lòng backup website trước khi cài đặt.

== Development ==

Plugin được phát triển theo các tiêu chuẩn WordPress và tuân thủ các nguyên tắc bảo mật. 

Mã nguồn: https://github.com/sonhsgvn-bit/clubs-manager-wordpress-plugin

== Support ==

Nếu bạn gặp vấn đề hoặc cần hỗ trợ, vui lòng tạo issue trên GitHub repository.

== Donations ==

Nếu plugin hữu ích, bạn có thể ủng hộ phát triển bằng cách đánh giá 5 sao và chia sẻ cho cộng đồng.