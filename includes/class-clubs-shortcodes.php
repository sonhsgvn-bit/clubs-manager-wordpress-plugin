<?php
/**
 * Shortcodes functionality
 *
 * @package ClubsManager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Clubs_Shortcodes {
    
    /**
     * Constructor
     */
    public function __construct() {
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
            'show_search' => 'true',
        ), $atts);
        
        $args = array(
            'post_type' => 'billiard_club',
            'post_status' => 'publish',
            'posts_per_page' => intval($atts['limit']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
        );
        
        // Filter by area if specified
        if (!empty($atts['area'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'club_area',
                    'field' => 'slug',
                    'terms' => $atts['area'],
                ),
            );
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        echo '<div class="clubs-manager-shortcode">';
        
        // Show search form if enabled
        if ($atts['show_search'] === 'true') {
            echo $this->clubs_search_shortcode();
        }
        
        echo '<div id="clubs-results" class="clubs-grid">';
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                include CLUBS_MANAGER_PLUGIN_DIR . 'templates/parts/club-card.php';
            }
            wp_reset_postdata();
        } else {
            echo '<div class="no-clubs-found"><p>' . __('Không tìm thấy câu lạc bộ nào.', 'clubs-manager') . '</p></div>';
        }
        
        echo '</div>';
        echo '</div>';
        
        return ob_get_clean();
    }
    
    /**
     * Clubs search shortcode
     */
    public function clubs_search_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show_area' => 'true',
            'show_price' => 'true',
            'show_tables' => 'true',
            'show_parking' => 'true',
        ), $atts);
        
        ob_start();
        ?>
        <div class="clubs-search-form">
            <form id="clubs-search-form" class="search-form">
                <div class="search-fields">
                    <div class="search-field">
                        <label for="search_name"><?php _e('Tên câu lạc bộ:', 'clubs-manager'); ?></label>
                        <input type="text" id="search_name" name="search_name" placeholder="<?php _e('Nhập tên câu lạc bộ...', 'clubs-manager'); ?>">
                    </div>

                    <?php if ($atts['show_area'] === 'true') : ?>
                        <div class="search-field">
                            <label for="search_area"><?php _e('Khu vực:', 'clubs-manager'); ?></label>
                            <select id="search_area" name="search_area">
                                <option value=""><?php _e('Tất cả khu vực', 'clubs-manager'); ?></option>
                                <?php
                                $areas = get_terms(array(
                                    'taxonomy' => 'club_area',
                                    'hide_empty' => false,
                                ));
                                foreach ($areas as $area) :
                                ?>
                                    <option value="<?php echo esc_attr($area->term_id); ?>"><?php echo esc_html($area->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if ($atts['show_price'] === 'true') : ?>
                        <div class="search-field">
                            <label for="search_price_min"><?php _e('Giá từ:', 'clubs-manager'); ?></label>
                            <input type="number" id="search_price_min" name="search_price_min" min="0" step="10000" placeholder="0">
                        </div>

                        <div class="search-field">
                            <label for="search_price_max"><?php _e('Giá đến:', 'clubs-manager'); ?></label>
                            <input type="number" id="search_price_max" name="search_price_max" min="0" step="10000" placeholder="500000">
                        </div>
                    <?php endif; ?>

                    <?php if ($atts['show_tables'] === 'true') : ?>
                        <div class="search-field">
                            <label for="search_tables"><?php _e('Số bàn tối thiểu:', 'clubs-manager'); ?></label>
                            <input type="number" id="search_tables" name="search_tables" min="1" placeholder="1">
                        </div>
                    <?php endif; ?>

                    <?php if ($atts['show_parking'] === 'true') : ?>
                        <div class="search-field">
                            <label for="search_parking"><?php _e('Có chỗ đậu xe:', 'clubs-manager'); ?></label>
                            <select id="search_parking" name="search_parking">
                                <option value=""><?php _e('Không quan trọng', 'clubs-manager'); ?></option>
                                <option value="yes"><?php _e('Có', 'clubs-manager'); ?></option>
                                <option value="no"><?php _e('Không', 'clubs-manager'); ?></option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="search-actions">
                    <button type="submit"><?php _e('Tìm kiếm', 'clubs-manager'); ?></button>
                    <button type="button" id="reset-search"><?php _e('Xóa bộ lọc', 'clubs-manager'); ?></button>
                </div>
            </form>
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
            'width' => '100%',
            'zoom' => '10',
            'center_lat' => '10.8231',
            'center_lng' => '106.6297',
            'area' => '',
        ), $atts);
        
        // Get clubs data
        $frontend = new Clubs_Frontend();
        
        $args = array();
        if (!empty($atts['area'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'club_area',
                    'field' => 'slug',
                    'terms' => $atts['area'],
                ),
            );
        }
        
        $clubs = $frontend->get_clubs_for_map($args);
        
        ob_start();
        ?>
        <div class="clubs-map-container">
            <div id="clubs-map" 
                 style="width: <?php echo esc_attr($atts['width']); ?>; height: <?php echo esc_attr($atts['height']); ?>;"
                 data-zoom="<?php echo esc_attr($atts['zoom']); ?>"
                 data-center-lat="<?php echo esc_attr($atts['center_lat']); ?>"
                 data-center-lng="<?php echo esc_attr($atts['center_lng']); ?>"
                 data-clubs="<?php echo esc_attr(json_encode($clubs)); ?>">
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }
}