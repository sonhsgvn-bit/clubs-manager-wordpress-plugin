<?php
/**
 * Frontend functionality for Clubs Manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Clubs_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_shortcodes'));
        add_action('wp_ajax_clubs_search', array($this, 'ajax_clubs_search'));
        add_action('wp_ajax_nopriv_clubs_search', array($this, 'ajax_clubs_search'));
        add_action('pre_get_posts', array($this, 'modify_main_query'));
    }
    
    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('clubs_list', array($this, 'clubs_list_shortcode'));
        add_shortcode('clubs_search', array($this, 'clubs_search_shortcode'));
        add_shortcode('clubs_map', array($this, 'clubs_map_shortcode'));
    }
    
    /**
     * Clubs list shortcode
     */
    public function clubs_list_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 12,
            'area' => '',
            'orderby' => 'date',
            'order' => 'DESC',
        ), $atts);
        
        $args = array(
            'post_type' => 'billiard-club',
            'posts_per_page' => intval($atts['limit']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'post_status' => 'publish',
        );
        
        if (!empty($atts['area'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'club-area',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($atts['area']),
                ),
            );
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        if ($query->have_posts()) {
            echo '<div class="clubs-grid">';
            while ($query->have_posts()) {
                $query->the_post();
                $this->render_club_card(get_the_ID());
            }
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p class="no-clubs-found">' . __('Không tìm thấy câu lạc bộ nào.', 'clubs-manager') . '</p>';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Clubs search shortcode
     */
    public function clubs_search_shortcode($atts) {
        $atts = shortcode_atts(array(
            'ajax' => 'true',
        ), $atts);
        
        ob_start();
        ?>
        <div class="clubs-search-form">
            <form id="clubs-search-form" method="get" <?php echo $atts['ajax'] === 'true' ? 'data-ajax="true"' : ''; ?>>
                <div class="search-row">
                    <div class="search-field">
                        <label for="club_name"><?php _e('Tên câu lạc bộ', 'clubs-manager'); ?></label>
                        <input type="text" id="club_name" name="club_name" value="<?php echo esc_attr(get_query_var('club_name')); ?>" placeholder="<?php _e('Nhập tên câu lạc bộ...', 'clubs-manager'); ?>" />
                    </div>
                    
                    <div class="search-field">
                        <label for="club_area"><?php _e('Khu vực', 'clubs-manager'); ?></label>
                        <select id="club_area" name="club_area">
                            <option value=""><?php _e('Tất cả khu vực', 'clubs-manager'); ?></option>
                            <?php
                            $areas = get_terms(array(
                                'taxonomy' => 'club-area',
                                'hide_empty' => false,
                            ));
                            if ($areas && !is_wp_error($areas)) {
                                foreach ($areas as $area) {
                                    $selected = selected(get_query_var('club_area'), $area->slug, false);
                                    echo '<option value="' . esc_attr($area->slug) . '"' . $selected . '>' . esc_html($area->name) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="search-row">
                    <div class="search-field">
                        <label for="min_price"><?php _e('Giá từ (VNĐ)', 'clubs-manager'); ?></label>
                        <input type="number" id="min_price" name="min_price" value="<?php echo esc_attr(get_query_var('min_price')); ?>" min="0" step="10000" placeholder="0" />
                    </div>
                    
                    <div class="search-field">
                        <label for="max_price"><?php _e('Giá đến (VNĐ)', 'clubs-manager'); ?></label>
                        <input type="number" id="max_price" name="max_price" value="<?php echo esc_attr(get_query_var('max_price')); ?>" min="0" step="10000" placeholder="<?php _e('Không giới hạn', 'clubs-manager'); ?>" />
                    </div>
                </div>
                
                <div class="search-row">
                    <div class="search-field">
                        <label for="min_tables"><?php _e('Số bàn tối thiểu', 'clubs-manager'); ?></label>
                        <select id="min_tables" name="min_tables">
                            <option value=""><?php _e('Không yêu cầu', 'clubs-manager'); ?></option>
                            <?php for ($i = 1; $i <= 20; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php selected(get_query_var('min_tables'), $i); ?>><?php echo $i; ?> <?php _e('bàn', 'clubs-manager'); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="search-field">
                        <label for="has_parking"><?php _e('Chỗ đậu xe', 'clubs-manager'); ?></label>
                        <select id="has_parking" name="has_parking">
                            <option value=""><?php _e('Không quan trọng', 'clubs-manager'); ?></option>
                            <option value="yes" <?php selected(get_query_var('has_parking'), 'yes'); ?>><?php _e('Có chỗ đậu xe', 'clubs-manager'); ?></option>
                        </select>
                    </div>
                </div>
                
                <div class="search-actions">
                    <button type="submit" class="button primary"><?php _e('Tìm kiếm', 'clubs-manager'); ?></button>
                    <button type="button" id="reset-search" class="button secondary"><?php _e('Đặt lại', 'clubs-manager'); ?></button>
                </div>
            </form>
            
            <?php if ($atts['ajax'] === 'true'): ?>
                <div id="clubs-search-results" class="clubs-search-results">
                    <div class="loading" style="display: none;"><?php _e('Đang tìm kiếm...', 'clubs-manager'); ?></div>
                    <div class="results-container"></div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Clubs map shortcode
     */
    public function clubs_map_shortcode($atts) {
        $atts = shortcode_atts(array(
            'height' => '400px',
            'zoom' => '12',
        ), $atts);
        
        $options = get_option('clubs_manager_options');
        $api_key = isset($options['google_maps_api_key']) ? $options['google_maps_api_key'] : '';
        
        if (empty($api_key)) {
            return '<p class="error">' . __('Google Maps API Key chưa được cấu hình.', 'clubs-manager') . '</p>';
        }
        
        // Get all clubs with coordinates
        $clubs = get_posts(array(
            'post_type' => 'billiard-club',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_club_lat',
                    'value' => '',
                    'compare' => '!=',
                ),
                array(
                    'key' => '_club_lng',
                    'value' => '',
                    'compare' => '!=',
                ),
            ),
        ));
        
        $markers = array();
        foreach ($clubs as $club) {
            $lat = get_post_meta($club->ID, '_club_lat', true);
            $lng = get_post_meta($club->ID, '_club_lng', true);
            $price = get_post_meta($club->ID, '_club_price', true);
            
            if ($lat && $lng) {
                $markers[] = array(
                    'lat' => floatval($lat),
                    'lng' => floatval($lng),
                    'title' => get_the_title($club->ID),
                    'url' => get_permalink($club->ID),
                    'price' => $price ? number_format($price, 0, ',', '.') . ' VNĐ/giờ' : '',
                );
            }
        }
        
        $center_lat = isset($options['default_map_center_lat']) ? $options['default_map_center_lat'] : '10.7769';
        $center_lng = isset($options['default_map_center_lng']) ? $options['default_map_center_lng'] : '106.7009';
        
        ob_start();
        ?>
        <div class="clubs-map-container">
            <div id="clubs-map" style="height: <?php echo esc_attr($atts['height']); ?>; width: 100%;"></div>
        </div>
        
        <script>
        function initClubsMap() {
            var map = new google.maps.Map(document.getElementById('clubs-map'), {
                zoom: <?php echo intval($atts['zoom']); ?>,
                center: {lat: <?php echo floatval($center_lat); ?>, lng: <?php echo floatval($center_lng); ?>}
            });
            
            var markers = <?php echo json_encode($markers); ?>;
            
            markers.forEach(function(markerData) {
                var marker = new google.maps.Marker({
                    position: {lat: markerData.lat, lng: markerData.lng},
                    map: map,
                    title: markerData.title
                });
                
                var infoWindow = new google.maps.InfoWindow({
                    content: '<div class="club-info">' +
                             '<h4><a href="' + markerData.url + '">' + markerData.title + '</a></h4>' +
                             (markerData.price ? '<p>Giá: ' + markerData.price + '</p>' : '') +
                             '</div>'
                });
                
                marker.addListener('click', function() {
                    infoWindow.open(map, marker);
                });
            });
        }
        </script>
        
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr($api_key); ?>&callback=initClubsMap"></script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * AJAX clubs search
     */
    public function ajax_clubs_search() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'clubs_search_nonce')) {
            wp_die(__('Lỗi bảo mật', 'clubs-manager'));
        }
        
        $args = array(
            'post_type' => 'billiard-club',
            'posts_per_page' => 12,
            'post_status' => 'publish',
        );
        
        // Search by name
        if (!empty($_POST['club_name'])) {
            $args['s'] = sanitize_text_field($_POST['club_name']);
        }
        
        // Filter by area
        if (!empty($_POST['club_area'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'club-area',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($_POST['club_area']),
                ),
            );
        }
        
        // Filter by price range
        $meta_query = array('relation' => 'AND');
        
        if (!empty($_POST['min_price'])) {
            $meta_query[] = array(
                'key' => '_club_price',
                'value' => intval($_POST['min_price']),
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }
        
        if (!empty($_POST['max_price'])) {
            $meta_query[] = array(
                'key' => '_club_price',
                'value' => intval($_POST['max_price']),
                'compare' => '<=',
                'type' => 'NUMERIC',
            );
        }
        
        // Filter by minimum tables
        if (!empty($_POST['min_tables'])) {
            $meta_query[] = array(
                'key' => '_club_tables',
                'value' => intval($_POST['min_tables']),
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }
        
        // Filter by parking
        if (!empty($_POST['has_parking']) && $_POST['has_parking'] === 'yes') {
            $meta_query[] = array(
                'key' => '_club_parking',
                'value' => 'yes',
                'compare' => '=',
            );
        }
        
        if (count($meta_query) > 1) {
            $args['meta_query'] = $meta_query;
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        if ($query->have_posts()) {
            echo '<div class="clubs-grid">';
            while ($query->have_posts()) {
                $query->the_post();
                $this->render_club_card(get_the_ID());
            }
            echo '</div>';
        } else {
            echo '<p class="no-clubs-found">' . __('Không tìm thấy câu lạc bộ nào phù hợp.', 'clubs-manager') . '</p>';
        }
        
        $html = ob_get_clean();
        wp_reset_postdata();
        
        wp_send_json_success(array('html' => $html));
    }
    
    /**
     * Render club card
     */
    private function render_club_card($club_id) {
        $club = get_post($club_id);
        $price = get_post_meta($club_id, '_club_price', true);
        $tables = get_post_meta($club_id, '_club_tables', true);
        $parking = get_post_meta($club_id, '_club_parking', true);
        $address = get_post_meta($club_id, '_club_address', true);
        $phone = get_post_meta($club_id, '_club_phone', true);
        $areas = get_the_terms($club_id, 'club-area');
        
        include CLUBS_MANAGER_PLUGIN_DIR . 'templates/club-card.php';
    }
    
    /**
     * Modify main query for custom search parameters
     */
    public function modify_main_query($query) {
        if (!is_admin() && $query->is_main_query()) {
            if (is_post_type_archive('billiard-club') || is_tax('club-area')) {
                $options = get_option('clubs_manager_options');
                $per_page = isset($options['clubs_per_page']) ? intval($options['clubs_per_page']) : 12;
                $query->set('posts_per_page', $per_page);
                
                // Handle custom search parameters
                $meta_query = array('relation' => 'AND');
                
                if (!empty($_GET['min_price'])) {
                    $meta_query[] = array(
                        'key' => '_club_price',
                        'value' => intval($_GET['min_price']),
                        'compare' => '>=',
                        'type' => 'NUMERIC',
                    );
                }
                
                if (!empty($_GET['max_price'])) {
                    $meta_query[] = array(
                        'key' => '_club_price',
                        'value' => intval($_GET['max_price']),
                        'compare' => '<=',
                        'type' => 'NUMERIC',
                    );
                }
                
                if (!empty($_GET['min_tables'])) {
                    $meta_query[] = array(
                        'key' => '_club_tables',
                        'value' => intval($_GET['min_tables']),
                        'compare' => '>=',
                        'type' => 'NUMERIC',
                    );
                }
                
                if (!empty($_GET['has_parking']) && $_GET['has_parking'] === 'yes') {
                    $meta_query[] = array(
                        'key' => '_club_parking',
                        'value' => 'yes',
                        'compare' => '=',
                    );
                }
                
                if (count($meta_query) > 1) {
                    $query->set('meta_query', $meta_query);
                }
                
                if (!empty($_GET['club_name'])) {
                    $query->set('s', sanitize_text_field($_GET['club_name']));
                }
            }
        }
    }
}