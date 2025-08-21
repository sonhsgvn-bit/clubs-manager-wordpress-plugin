<?php
/**
 * Archive Clubs Template
 *
 * @package ClubsManager
 */

get_header(); ?>

<div class="clubs-manager-archive">
    <header class="archive-header">
        <h1 class="archive-title"><?php _e('Câu lạc bộ bi-a', 'clubs-manager'); ?></h1>
        <div class="archive-description">
            <?php if (is_tax('club_area')) : ?>
                <p><?php echo sprintf(__('Danh sách câu lạc bộ trong khu vực: %s', 'clubs-manager'), single_term_title('', false)); ?></p>
            <?php else : ?>
                <p><?php _e('Danh sách tất cả câu lạc bộ bi-a', 'clubs-manager'); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <div class="clubs-search-form">
        <form id="clubs-search-form" class="search-form">
            <div class="search-fields">
                <div class="search-field">
                    <label for="search_name"><?php _e('Tên câu lạc bộ:', 'clubs-manager'); ?></label>
                    <input type="text" id="search_name" name="search_name" placeholder="<?php _e('Nhập tên câu lạc bộ...', 'clubs-manager'); ?>">
                </div>

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
                <p><?php _e('Không tìm thấy câu lạc bộ nào.', 'clubs-manager'); ?></p>
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
</div>

<?php get_footer(); ?>