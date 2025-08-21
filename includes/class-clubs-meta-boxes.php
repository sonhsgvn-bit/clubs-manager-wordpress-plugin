<?php
/**
 * Meta Boxes for Billiard Clubs
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Clubs_Meta_Boxes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'club_details',
            __('Thông tin câu lạc bộ', 'clubs-manager'),
            array($this, 'club_details_callback'),
            'billiard-club',
            'normal',
            'high'
        );
        
        add_meta_box(
            'club_contact',
            __('Thông tin liên hệ', 'clubs-manager'),
            array($this, 'club_contact_callback'),
            'billiard-club',
            'normal',
            'high'
        );
        
        add_meta_box(
            'club_gallery',
            __('Thư viện hình ảnh', 'clubs-manager'),
            array($this, 'club_gallery_callback'),
            'billiard-club',
            'normal',
            'high'
        );
        
        add_meta_box(
            'club_hours',
            __('Giờ mở cửa', 'clubs-manager'),
            array($this, 'club_hours_callback'),
            'billiard-club',
            'normal',
            'high'
        );
        
        add_meta_box(
            'club_location',
            __('Vị trí', 'clubs-manager'),
            array($this, 'club_location_callback'),
            'billiard-club',
            'side',
            'high'
        );
    }
    
    /**
     * Club details meta box callback
     */
    public function club_details_callback($post) {
        wp_nonce_field('clubs_meta_nonce', 'clubs_meta_nonce_field');
        
        $address = get_post_meta($post->ID, '_club_address', true);
        $price = get_post_meta($post->ID, '_club_price', true);
        $tables = get_post_meta($post->ID, '_club_tables', true);
        $parking = get_post_meta($post->ID, '_club_parking', true);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="club_address"><?php _e('Địa chỉ', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="text" id="club_address" name="club_address" value="<?php echo esc_attr($address); ?>" class="regular-text" />
                    <p class="description"><?php _e('Địa chỉ đầy đủ của câu lạc bộ', 'clubs-manager'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_price"><?php _e('Giá (VNĐ/giờ)', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="number" id="club_price" name="club_price" value="<?php echo esc_attr($price); ?>" min="0" step="1000" />
                    <p class="description"><?php _e('Giá thuê bàn bi-a theo giờ (VNĐ)', 'clubs-manager'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_tables"><?php _e('Số bàn bi-a', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="number" id="club_tables" name="club_tables" value="<?php echo esc_attr($tables); ?>" min="1" max="100" />
                    <p class="description"><?php _e('Tổng số bàn bi-a có trong câu lạc bộ', 'clubs-manager'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_parking"><?php _e('Chỗ đậu xe', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <select id="club_parking" name="club_parking">
                        <option value="yes" <?php selected($parking, 'yes'); ?>><?php _e('Có', 'clubs-manager'); ?></option>
                        <option value="no" <?php selected($parking, 'no'); ?>><?php _e('Không', 'clubs-manager'); ?></option>
                    </select>
                    <p class="description"><?php _e('Câu lạc bộ có chỗ đậu xe không?', 'clubs-manager'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Club contact meta box callback
     */
    public function club_contact_callback($post) {
        $phone = get_post_meta($post->ID, '_club_phone', true);
        $email = get_post_meta($post->ID, '_club_email', true);
        $website = get_post_meta($post->ID, '_club_website', true);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="club_phone"><?php _e('Số điện thoại', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="tel" id="club_phone" name="club_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" />
                    <p class="description"><?php _e('Số điện thoại liên hệ', 'clubs-manager'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_email"><?php _e('Email', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="email" id="club_email" name="club_email" value="<?php echo esc_attr($email); ?>" class="regular-text" />
                    <p class="description"><?php _e('Địa chỉ email liên hệ', 'clubs-manager'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_website"><?php _e('Website', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="url" id="club_website" name="club_website" value="<?php echo esc_attr($website); ?>" class="regular-text" />
                    <p class="description"><?php _e('Trang web của câu lạc bộ', 'clubs-manager'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Club gallery meta box callback
     */
    public function club_gallery_callback($post) {
        $gallery = get_post_meta($post->ID, '_club_gallery', true);
        if (!is_array($gallery)) {
            $gallery = array();
        }
        ?>
        <div id="club-gallery-container">
            <div id="club-gallery-images">
                <?php if (!empty($gallery)): ?>
                    <?php foreach ($gallery as $image_id): ?>
                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                        <?php if ($image_url): ?>
                            <div class="gallery-image" data-id="<?php echo esc_attr($image_id); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" alt="" />
                                <button type="button" class="remove-gallery-image">&times;</button>
                                <input type="hidden" name="club_gallery[]" value="<?php echo esc_attr($image_id); ?>" />
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" id="add-gallery-images" class="button"><?php _e('Thêm hình ảnh', 'clubs-manager'); ?></button>
            <p class="description"><?php _e('Thêm nhiều hình ảnh để hiển thị trong thư viện', 'clubs-manager'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Club hours meta box callback
     */
    public function club_hours_callback($post) {
        $hours = get_post_meta($post->ID, '_club_hours', true);
        if (!is_array($hours)) {
            $hours = array();
        }
        
        $days = array(
            'monday' => __('Thứ Hai', 'clubs-manager'),
            'tuesday' => __('Thứ Ba', 'clubs-manager'),
            'wednesday' => __('Thứ Tư', 'clubs-manager'),
            'thursday' => __('Thứ Năm', 'clubs-manager'),
            'friday' => __('Thứ Sáu', 'clubs-manager'),
            'saturday' => __('Thứ Bảy', 'clubs-manager'),
            'sunday' => __('Chủ Nhật', 'clubs-manager'),
        );
        ?>
        <table class="form-table">
            <?php foreach ($days as $day => $label): ?>
                <?php
                $open = isset($hours[$day]['open']) ? $hours[$day]['open'] : '';
                $close = isset($hours[$day]['close']) ? $hours[$day]['close'] : '';
                $closed = isset($hours[$day]['closed']) ? $hours[$day]['closed'] : false;
                ?>
                <tr>
                    <th scope="row"><?php echo esc_html($label); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="club_hours[<?php echo esc_attr($day); ?>][closed]" value="1" <?php checked($closed, true); ?> />
                            <?php _e('Đóng cửa', 'clubs-manager'); ?>
                        </label>
                        <br />
                        <label><?php _e('Mở:', 'clubs-manager'); ?>
                            <input type="time" name="club_hours[<?php echo esc_attr($day); ?>][open]" value="<?php echo esc_attr($open); ?>" />
                        </label>
                        <label><?php _e('Đóng:', 'clubs-manager'); ?>
                            <input type="time" name="club_hours[<?php echo esc_attr($day); ?>][close]" value="<?php echo esc_attr($close); ?>" />
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }
    
    /**
     * Club location meta box callback
     */
    public function club_location_callback($post) {
        $lat = get_post_meta($post->ID, '_club_lat', true);
        $lng = get_post_meta($post->ID, '_club_lng', true);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="club_lat"><?php _e('Latitude', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="text" id="club_lat" name="club_lat" value="<?php echo esc_attr($lat); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_lng"><?php _e('Longitude', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="text" id="club_lng" name="club_lng" value="<?php echo esc_attr($lng); ?>" class="regular-text" />
                </td>
            </tr>
        </table>
        <p class="description"><?php _e('Tọa độ Google Maps cho vị trí câu lạc bộ', 'clubs-manager'); ?></p>
        <?php
    }
    
    /**
     * Save meta box data
     */
    public function save_meta_boxes($post_id) {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check if this is the correct post type
        if (get_post_type($post_id) !== 'billiard-club') {
            return;
        }
        
        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Verify nonce
        if (!isset($_POST['clubs_meta_nonce_field']) || !wp_verify_nonce($_POST['clubs_meta_nonce_field'], 'clubs_meta_nonce')) {
            return;
        }
        
        // Save club details
        $this->save_field($post_id, 'club_address', '_club_address', 'sanitize_text_field');
        $this->save_field($post_id, 'club_price', '_club_price', 'absint');
        $this->save_field($post_id, 'club_tables', '_club_tables', 'absint');
        $this->save_field($post_id, 'club_parking', '_club_parking', 'sanitize_text_field');
        
        // Save contact info
        $this->save_field($post_id, 'club_phone', '_club_phone', 'sanitize_text_field');
        $this->save_field($post_id, 'club_email', '_club_email', 'sanitize_email');
        $this->save_field($post_id, 'club_website', '_club_website', 'esc_url_raw');
        
        // Save gallery
        if (isset($_POST['club_gallery']) && is_array($_POST['club_gallery'])) {
            $gallery = array_map('absint', $_POST['club_gallery']);
            update_post_meta($post_id, '_club_gallery', $gallery);
        } else {
            delete_post_meta($post_id, '_club_gallery');
        }
        
        // Save hours
        if (isset($_POST['club_hours']) && is_array($_POST['club_hours'])) {
            $hours = array();
            foreach ($_POST['club_hours'] as $day => $data) {
                $hours[sanitize_key($day)] = array(
                    'open' => sanitize_text_field($data['open']),
                    'close' => sanitize_text_field($data['close']),
                    'closed' => isset($data['closed']) ? true : false,
                );
            }
            update_post_meta($post_id, '_club_hours', $hours);
        }
        
        // Save location
        $this->save_field($post_id, 'club_lat', '_club_lat', 'sanitize_text_field');
        $this->save_field($post_id, 'club_lng', '_club_lng', 'sanitize_text_field');
    }
    
    /**
     * Helper function to save individual fields
     */
    private function save_field($post_id, $field_name, $meta_key, $sanitize_callback) {
        if (isset($_POST[$field_name])) {
            $value = call_user_func($sanitize_callback, $_POST[$field_name]);
            update_post_meta($post_id, $meta_key, $value);
        } else {
            delete_post_meta($post_id, $meta_key);
        }
    }
}