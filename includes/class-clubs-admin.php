<?php
/**
 * Admin functionality for Clubs Manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Clubs_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        add_filter('manage_billiard-club_posts_columns', array($this, 'add_custom_columns'));
        add_action('manage_billiard-club_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
        add_filter('manage_edit-billiard-club_sortable_columns', array($this, 'sortable_columns'));
        add_action('pre_get_posts', array($this, 'custom_orderby'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=billiard-club',
            __('Cài đặt Clubs Manager', 'clubs-manager'),
            __('Cài đặt', 'clubs-manager'),
            'manage_options',
            'clubs-manager-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Initialize admin settings
     */
    public function admin_init() {
        register_setting('clubs_manager_settings', 'clubs_manager_options');
        
        add_settings_section(
            'clubs_manager_main',
            __('Cài đặt chính', 'clubs-manager'),
            array($this, 'main_section_callback'),
            'clubs_manager_settings'
        );
        
        add_settings_field(
            'google_maps_api_key',
            __('Google Maps API Key', 'clubs-manager'),
            array($this, 'google_maps_api_key_callback'),
            'clubs_manager_settings',
            'clubs_manager_main'
        );
        
        add_settings_field(
            'default_map_center_lat',
            __('Latitude trung tâm bản đồ mặc định', 'clubs-manager'),
            array($this, 'default_map_center_lat_callback'),
            'clubs_manager_settings',
            'clubs_manager_main'
        );
        
        add_settings_field(
            'default_map_center_lng',
            __('Longitude trung tâm bản đồ mặc định', 'clubs-manager'),
            array($this, 'default_map_center_lng_callback'),
            'clubs_manager_settings',
            'clubs_manager_main'
        );
        
        add_settings_field(
            'clubs_per_page',
            __('Số câu lạc bộ hiển thị trên một trang', 'clubs-manager'),
            array($this, 'clubs_per_page_callback'),
            'clubs_manager_settings',
            'clubs_manager_main'
        );
    }
    
    /**
     * Main section callback
     */
    public function main_section_callback() {
        echo '<p>' . __('Cấu hình các tùy chọn chính cho plugin Clubs Manager.', 'clubs-manager') . '</p>';
    }
    
    /**
     * Google Maps API Key callback
     */
    public function google_maps_api_key_callback() {
        $options = get_option('clubs_manager_options');
        $value = isset($options['google_maps_api_key']) ? $options['google_maps_api_key'] : '';
        echo '<input type="text" id="google_maps_api_key" name="clubs_manager_options[google_maps_api_key]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . __('API Key cho Google Maps. Cần thiết để hiển thị bản đồ.', 'clubs-manager') . '</p>';
    }
    
    /**
     * Default map center lat callback
     */
    public function default_map_center_lat_callback() {
        $options = get_option('clubs_manager_options');
        $value = isset($options['default_map_center_lat']) ? $options['default_map_center_lat'] : '10.7769';
        echo '<input type="text" id="default_map_center_lat" name="clubs_manager_options[default_map_center_lat]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . __('Latitude trung tâm bản đồ mặc định (Hồ Chí Minh: 10.7769)', 'clubs-manager') . '</p>';
    }
    
    /**
     * Default map center lng callback
     */
    public function default_map_center_lng_callback() {
        $options = get_option('clubs_manager_options');
        $value = isset($options['default_map_center_lng']) ? $options['default_map_center_lng'] : '106.7009';
        echo '<input type="text" id="default_map_center_lng" name="clubs_manager_options[default_map_center_lng]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . __('Longitude trung tâm bản đồ mặc định (Hồ Chí Minh: 106.7009)', 'clubs-manager') . '</p>';
    }
    
    /**
     * Clubs per page callback
     */
    public function clubs_per_page_callback() {
        $options = get_option('clubs_manager_options');
        $value = isset($options['clubs_per_page']) ? $options['clubs_per_page'] : '12';
        echo '<input type="number" id="clubs_per_page" name="clubs_manager_options[clubs_per_page]" value="' . esc_attr($value) . '" min="1" max="50" />';
        echo '<p class="description">' . __('Số lượng câu lạc bộ hiển thị trên mỗi trang.', 'clubs-manager') . '</p>';
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('clubs_manager_settings');
                do_settings_sections('clubs_manager_settings');
                submit_button(__('Lưu cài đặt', 'clubs-manager'));
                ?>
            </form>
        </div>
        <?php
    }
    
    /**
     * Add custom columns to admin list
     */
    public function add_custom_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['club_area'] = __('Khu vực', 'clubs-manager');
        $new_columns['club_price'] = __('Giá (VNĐ/giờ)', 'clubs-manager');
        $new_columns['club_tables'] = __('Số bàn', 'clubs-manager');
        $new_columns['club_parking'] = __('Đậu xe', 'clubs-manager');
        $new_columns['club_phone'] = __('Điện thoại', 'clubs-manager');
        $new_columns['thumbnail'] = __('Ảnh', 'clubs-manager');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    /**
     * Custom column content
     */
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'club_area':
                $terms = get_the_terms($post_id, 'club-area');
                if ($terms && !is_wp_error($terms)) {
                    $area_names = wp_list_pluck($terms, 'name');
                    echo esc_html(implode(', ', $area_names));
                } else {
                    echo '—';
                }
                break;
                
            case 'club_price':
                $price = get_post_meta($post_id, '_club_price', true);
                if ($price) {
                    echo number_format($price, 0, ',', '.') . ' VNĐ';
                } else {
                    echo '—';
                }
                break;
                
            case 'club_tables':
                $tables = get_post_meta($post_id, '_club_tables', true);
                echo $tables ? esc_html($tables) : '—';
                break;
                
            case 'club_parking':
                $parking = get_post_meta($post_id, '_club_parking', true);
                if ($parking === 'yes') {
                    echo '<span class="dashicons dashicons-yes-alt" style="color: green;"></span>';
                } elseif ($parking === 'no') {
                    echo '<span class="dashicons dashicons-dismiss" style="color: red;"></span>';
                } else {
                    echo '—';
                }
                break;
                
            case 'club_phone':
                $phone = get_post_meta($post_id, '_club_phone', true);
                echo $phone ? esc_html($phone) : '—';
                break;
                
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                } else {
                    echo '—';
                }
                break;
        }
    }
    
    /**
     * Make columns sortable
     */
    public function sortable_columns($columns) {
        $columns['club_price'] = 'club_price';
        $columns['club_tables'] = 'club_tables';
        return $columns;
    }
    
    /**
     * Custom orderby for sortable columns
     */
    public function custom_orderby($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }
        
        $orderby = $query->get('orderby');
        
        if ($orderby === 'club_price') {
            $query->set('meta_key', '_club_price');
            $query->set('orderby', 'meta_value_num');
        } elseif ($orderby === 'club_tables') {
            $query->set('meta_key', '_club_tables');
            $query->set('orderby', 'meta_value_num');
        }
    }
}