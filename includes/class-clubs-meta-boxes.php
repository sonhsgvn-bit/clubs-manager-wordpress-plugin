<?php
/**
 * Meta Boxes for Clubs
 *
 * @package ClubsManager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Clubs_Meta_Boxes {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'club_details',
            __('Thông tin câu lạc bộ', 'clubs-manager'),
            array($this, 'club_details_callback'),
            'billiard_club',
            'normal',
            'high'
        );
        
        add_meta_box(
            'club_location',
            __('Vị trí câu lạc bộ', 'clubs-manager'),
            array($this, 'club_location_callback'),
            'billiard_club',
            'normal',
            'high'
        );
        
        add_meta_box(
            'club_gallery',
            __('Thư viện ảnh', 'clubs-manager'),
            array($this, 'club_gallery_callback'),
            'billiard_club',
            'normal',
            'high'
        );
        
        add_meta_box(
            'club_hours',
            __('Giờ mở cửa', 'clubs-manager'),
            array($this, 'club_hours_callback'),
            'billiard_club',
            'side',
            'default'
        );
    }
    
    /**
     * Club details meta box callback
     */
    public function club_details_callback($post) {
        wp_nonce_field(basename(__FILE__), 'club_details_nonce');
        
        $club_address = get_post_meta($post->ID, 'club_address', true);
        $club_price = get_post_meta($post->ID, 'club_price', true);
        $club_tables = get_post_meta($post->ID, 'club_tables', true);
        $club_parking = get_post_meta($post->ID, 'club_parking', true);
        $club_phone = get_post_meta($post->ID, 'club_phone', true);
        $club_email = get_post_meta($post->ID, 'club_email', true);
        $club_website = get_post_meta($post->ID, 'club_website', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="club_address"><?php _e('Địa chỉ', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="text" id="club_address" name="club_address" value="<?php echo esc_attr($club_address); ?>" class="large-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_price"><?php _e('Giá VNĐ/giờ', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="number" id="club_price" name="club_price" value="<?php echo esc_attr($club_price); ?>" min="0" step="1000" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_tables"><?php _e('Số bàn bi-a', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="number" id="club_tables" name="club_tables" value="<?php echo esc_attr($club_tables); ?>" min="1" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_parking"><?php _e('Có chỗ đậu xe', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <select id="club_parking" name="club_parking">
                        <option value="yes" <?php selected($club_parking, 'yes'); ?>><?php _e('Có', 'clubs-manager'); ?></option>
                        <option value="no" <?php selected($club_parking, 'no'); ?>><?php _e('Không', 'clubs-manager'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_phone"><?php _e('Số điện thoại', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="tel" id="club_phone" name="club_phone" value="<?php echo esc_attr($club_phone); ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_email"><?php _e('Email', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="email" id="club_email" name="club_email" value="<?php echo esc_attr($club_email); ?>" class="large-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_website"><?php _e('Website', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="url" id="club_website" name="club_website" value="<?php echo esc_attr($club_website); ?>" class="large-text" />
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Club location meta box callback
     */
    public function club_location_callback($post) {
        wp_nonce_field(basename(__FILE__), 'club_location_nonce');
        
        $club_lat = get_post_meta($post->ID, 'club_lat', true);
        $club_lng = get_post_meta($post->ID, 'club_lng', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="club_lat"><?php _e('Latitude', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="text" id="club_lat" name="club_lat" value="<?php echo esc_attr($club_lat); ?>" step="any" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="club_lng"><?php _e('Longitude', 'clubs-manager'); ?></label>
                </th>
                <td>
                    <input type="text" id="club_lng" name="club_lng" value="<?php echo esc_attr($club_lng); ?>" step="any" />
                </td>
            </tr>
        </table>
        <div id="club-map" style="width: 100%; height: 300px; margin-top: 10px;"></div>
        <p class="description"><?php _e('Nhấp vào bản đồ để chọn vị trí chính xác của câu lạc bộ.', 'clubs-manager'); ?></p>
        <?php
    }
    
    /**
     * Club gallery meta box callback
     */
    public function club_gallery_callback($post) {
        wp_nonce_field(basename(__FILE__), 'club_gallery_nonce');
        
        $club_gallery = get_post_meta($post->ID, 'club_gallery', true);
        $gallery_ids = !empty($club_gallery) ? explode(',', $club_gallery) : array();
        
        ?>
        <div class="club-gallery-container">
            <input type="hidden" id="club_gallery" name="club_gallery" value="<?php echo esc_attr($club_gallery); ?>" />
            <div class="club-gallery-thumbnails">
                <?php if (!empty($gallery_ids)) : ?>
                    <?php foreach ($gallery_ids as $image_id) : ?>
                        <?php $image_url = wp_get_attachment_image_src($image_id, 'thumbnail')[0]; ?>
                        <div class="gallery-thumb" data-id="<?php echo $image_id; ?>">
                            <img src="<?php echo esc_url($image_url); ?>" alt="" />
                            <button type="button" class="remove-gallery-image">&times;</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="button add-gallery-images"><?php _e('Thêm ảnh', 'clubs-manager'); ?></button>
        </div>
        <?php
    }
    
    /**
     * Club hours meta box callback
     */
    public function club_hours_callback($post) {
        wp_nonce_field(basename(__FILE__), 'club_hours_nonce');
        
        $club_hours = get_post_meta($post->ID, 'club_hours', true);
        $default_hours = array(
            'monday' => array('open' => '08:00', 'close' => '22:00'),
            'tuesday' => array('open' => '08:00', 'close' => '22:00'),
            'wednesday' => array('open' => '08:00', 'close' => '22:00'),
            'thursday' => array('open' => '08:00', 'close' => '22:00'),
            'friday' => array('open' => '08:00', 'close' => '22:00'),
            'saturday' => array('open' => '08:00', 'close' => '23:00'),
            'sunday' => array('open' => '08:00', 'close' => '23:00'),
        );
        
        if (empty($club_hours)) {
            $club_hours = $default_hours;
        }
        
        $days = array(
            'monday' => __('Thứ 2', 'clubs-manager'),
            'tuesday' => __('Thứ 3', 'clubs-manager'),
            'wednesday' => __('Thứ 4', 'clubs-manager'),
            'thursday' => __('Thứ 5', 'clubs-manager'),
            'friday' => __('Thứ 6', 'clubs-manager'),
            'saturday' => __('Thứ 7', 'clubs-manager'),
            'sunday' => __('Chủ nhật', 'clubs-manager'),
        );
        
        ?>
        <table class="form-table">
            <?php foreach ($days as $day_key => $day_label) : ?>
                <tr>
                    <th scope="row"><?php echo $day_label; ?></th>
                    <td>
                        <input type="time" name="club_hours[<?php echo $day_key; ?>][open]" value="<?php echo esc_attr($club_hours[$day_key]['open']); ?>" />
                        -
                        <input type="time" name="club_hours[<?php echo $day_key; ?>][close]" value="<?php echo esc_attr($club_hours[$day_key]['close']); ?>" />
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }
    
    /**
     * Save meta box data
     */
    public function save_meta_boxes($post_id) {
        // Check if nonce is valid
        if (!isset($_POST['club_details_nonce']) || !wp_verify_nonce($_POST['club_details_nonce'], basename(__FILE__))) {
            return;
        }
        
        // Check if user has permission
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Check if not an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Save club details
        $fields = array('club_address', 'club_price', 'club_tables', 'club_parking', 'club_phone', 'club_email', 'club_website', 'club_lat', 'club_lng', 'club_gallery');
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        // Save club hours
        if (isset($_POST['club_hours'])) {
            $club_hours = array();
            foreach ($_POST['club_hours'] as $day => $hours) {
                $club_hours[$day] = array(
                    'open' => sanitize_text_field($hours['open']),
                    'close' => sanitize_text_field($hours['close'])
                );
            }
            update_post_meta($post_id, 'club_hours', $club_hours);
        }
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        global $post_type;
        
        if (($hook == 'post-new.php' || $hook == 'post.php') && $post_type == 'billiard_club') {
            wp_enqueue_media();
            wp_enqueue_script('clubs-admin', CLUBS_MANAGER_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), CLUBS_MANAGER_VERSION, true);
            wp_enqueue_style('clubs-admin', CLUBS_MANAGER_PLUGIN_URL . 'assets/css/admin.css', array(), CLUBS_MANAGER_VERSION);
        }
    }
}