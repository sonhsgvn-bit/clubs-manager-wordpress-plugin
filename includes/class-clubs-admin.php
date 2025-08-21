<?php
/**
 * Admin functionality
 *
 * @package ClubsManager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Clubs_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        add_filter('manage_billiard_club_posts_columns', array($this, 'add_custom_columns'));
        add_action('manage_billiard_club_posts_custom_column', array($this, 'display_custom_columns'), 10, 2);
        add_filter('manage_edit-billiard_club_sortable_columns', array($this, 'sortable_columns'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=billiard_club',
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
            'clubs_manager_maps_section',
            __('Cài đặt Google Maps', 'clubs-manager'),
            array($this, 'maps_section_callback'),
            'clubs_manager_settings'
        );
        
        add_settings_field(
            'google_maps_api_key',
            __('Google Maps API Key', 'clubs-manager'),
            array($this, 'api_key_callback'),
            'clubs_manager_settings',
            'clubs_manager_maps_section'
        );
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Cài đặt Clubs Manager', 'clubs-manager'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('clubs_manager_settings');
                do_settings_sections('clubs_manager_settings');
                submit_button();
                ?>
            </form>
            
            <div class="clubs-manager-help">
                <h2><?php _e('Hướng dẫn sử dụng', 'clubs-manager'); ?></h2>
                <p><?php _e('Plugin Clubs Manager giúp bạn quản lý câu lạc bộ bi-a với các tính năng:', 'clubs-manager'); ?></p>
                <ul>
                    <li><?php _e('Thêm, sửa, xóa thông tin câu lạc bộ', 'clubs-manager'); ?></li>
                    <li><?php _e('Hiển thị danh sách câu lạc bộ với bộ lọc tìm kiếm', 'clubs-manager'); ?></li>
                    <li><?php _e('Hiển thị chi tiết câu lạc bộ với Google Maps', 'clubs-manager'); ?></li>
                    <li><?php _e('Phân loại câu lạc bộ theo khu vực', 'clubs-manager'); ?></li>
                </ul>
                
                <h3><?php _e('Shortcodes có sẵn', 'clubs-manager'); ?></h3>
                <p><code>[clubs_list]</code> - <?php _e('Hiển thị danh sách câu lạc bộ', 'clubs-manager'); ?></p>
                <p><code>[clubs_search]</code> - <?php _e('Hiển thị form tìm kiếm câu lạc bộ', 'clubs-manager'); ?></p>
                <p><code>[clubs_map]</code> - <?php _e('Hiển thị bản đồ tất cả câu lạc bộ', 'clubs-manager'); ?></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Maps section callback
     */
    public function maps_section_callback() {
        echo '<p>' . __('Nhập Google Maps API Key để sử dụng tính năng bản đồ.', 'clubs-manager') . '</p>';
    }
    
    /**
     * API key callback
     */
    public function api_key_callback() {
        $options = get_option('clubs_manager_options');
        $api_key = isset($options['google_maps_api_key']) ? $options['google_maps_api_key'] : '';
        ?>
        <input type="text" name="clubs_manager_options[google_maps_api_key]" value="<?php echo esc_attr($api_key); ?>" class="large-text" />
        <p class="description">
            <?php _e('Lấy API key tại:', 'clubs-manager'); ?> 
            <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Google Cloud Console</a>
        </p>
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
        $new_columns['club_address'] = __('Địa chỉ', 'clubs-manager');
        $new_columns['club_price'] = __('Giá', 'clubs-manager');
        $new_columns['club_tables'] = __('Số bàn', 'clubs-manager');
        $new_columns['club_parking'] = __('Đậu xe', 'clubs-manager');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    /**
     * Display custom columns content
     */
    public function display_custom_columns($column, $post_id) {
        switch ($column) {
            case 'club_area':
                $terms = get_the_terms($post_id, 'club_area');
                if ($terms && !is_wp_error($terms)) {
                    $term_names = array();
                    foreach ($terms as $term) {
                        $term_names[] = '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
                    }
                    echo implode(', ', $term_names);
                } else {
                    echo '—';
                }
                break;
                
            case 'club_address':
                $address = get_post_meta($post_id, 'club_address', true);
                echo $address ? esc_html($address) : '—';
                break;
                
            case 'club_price':
                $price = get_post_meta($post_id, 'club_price', true);
                echo $price ? number_format($price) . ' VNĐ/giờ' : '—';
                break;
                
            case 'club_tables':
                $tables = get_post_meta($post_id, 'club_tables', true);
                echo $tables ? esc_html($tables) . ' bàn' : '—';
                break;
                
            case 'club_parking':
                $parking = get_post_meta($post_id, 'club_parking', true);
                if ($parking === 'yes') {
                    echo '<span style="color: green;">✓ ' . __('Có', 'clubs-manager') . '</span>';
                } elseif ($parking === 'no') {
                    echo '<span style="color: red;">✗ ' . __('Không', 'clubs-manager') . '</span>';
                } else {
                    echo '—';
                }
                break;
        }
    }
    
    /**
     * Make custom columns sortable
     */
    public function sortable_columns($columns) {
        $columns['club_price'] = 'club_price';
        $columns['club_tables'] = 'club_tables';
        return $columns;
    }
}