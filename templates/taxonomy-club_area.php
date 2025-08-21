<?php
/**
 * Taxonomy Club Area Template
 *
 * @package ClubsManager
 */

get_header(); ?>

<div class="clubs-manager-taxonomy">
    <header class="taxonomy-header">
        <h1 class="taxonomy-title"><?php single_term_title(); ?></h1>
        <div class="taxonomy-description">
            <?php
            $term_description = term_description();
            if (!empty($term_description)) :
            ?>
                <div class="term-description"><?php echo $term_description; ?></div>
            <?php endif; ?>
            <p><?php echo sprintf(__('Có %d câu lạc bộ bi-a trong khu vực này', 'clubs-manager'), $wp_query->found_posts); ?></p>
        </div>
    </header>

    <div class="clubs-search-form">
        <form id="clubs-search-form" class="search-form">
            <input type="hidden" name="area_id" value="<?php echo get_queried_object_id(); ?>">
            
            <div class="search-fields">
                <div class="search-field">
                    <label for="search_name"><?php _e('Tên câu lạc bộ:', 'clubs-manager'); ?></label>
                    <input type="text" id="search_name" name="search_name" placeholder="<?php _e('Nhập tên câu lạc bộ...', 'clubs-manager'); ?>">
                </div>

                <div class="search-field">
                    <label for="search_price_min"><?php _e('Giá từ:', 'clubs-manager'); ?></label>
                    <input type="number" id="search_price_min" name="search_price_min" min="0" step="10000" placeholder="0">
                </div>

                <div class="search-field">
                    <label for="search_price_max"><?php _e('Giá đến:', 'clubs-manager'); ?></label>
                    <input type="number" id="search_price_max" name="search_price_max" min="0" step="10000" placeholder="500000">
                </div>

                <div class="search-field">
                    <label for="search_tables"><?php _e('Số bàn tối thiểu:', 'clubs-manager'); ?></label>
                    <input type="number" id="search_tables" name="search_tables" min="1" placeholder="1">
                </div>

                <div class="search-field">
                    <label for="search_parking"><?php _e('Có chỗ đậu xe:', 'clubs-manager'); ?></label>
                    <select id="search_parking" name="search_parking">
                        <option value=""><?php _e('Không quan trọng', 'clubs-manager'); ?></option>
                        <option value="yes"><?php _e('Có', 'clubs-manager'); ?></option>
                        <option value="no"><?php _e('Không', 'clubs-manager'); ?></option>
                    </select>
                </div>
            </div>

            <div class="search-actions">
                <button type="submit"><?php _e('Tìm kiếm', 'clubs-manager'); ?></button>
                <button type="button" id="reset-search"><?php _e('Xóa bộ lọc', 'clubs-manager'); ?></button>
            </div>
        </form>
    </div>

    <div id="clubs-results" class="clubs-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php include CLUBS_MANAGER_PLUGIN_DIR . 'templates/parts/club-card.php'; ?>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="no-clubs-found">
                <p><?php _e('Không tìm thấy câu lạc bộ nào trong khu vực này.', 'clubs-manager'); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <?php if (have_posts()) : ?>
        <div class="clubs-pagination">
            <?php
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('&laquo; Trước', 'clubs-manager'),
                'next_text' => __('Sau &raquo;', 'clubs-manager'),
            ));
            ?>
        </div>
    <?php endif; ?>

    <div id="loading-indicator" class="loading-indicator" style="display: none;">
        <p><?php _e('Đang tải...', 'clubs-manager'); ?></p>
    </div>

    <div class="related-areas">
        <h3><?php _e('Khu vực khác', 'clubs-manager'); ?></h3>
        <div class="areas-list">
            <?php
            $current_term_id = get_queried_object_id();
            $areas = get_terms(array(
                'taxonomy' => 'club_area',
                'hide_empty' => true,
                'exclude' => array($current_term_id),
            ));
            
            if (!empty($areas)) :
                foreach ($areas as $area) :
            ?>
                <a href="<?php echo esc_url(get_term_link($area)); ?>" class="area-link">
                    <?php echo esc_html($area->name); ?>
                    <span class="area-count">(<?php echo $area->count; ?>)</span>
                </a>
            <?php 
                endforeach;
            endif;
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>