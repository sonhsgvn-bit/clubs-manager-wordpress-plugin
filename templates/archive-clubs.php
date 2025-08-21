<?php
/**
 * Template for displaying club archive pages
 */

get_header(); ?>

<div class="container clubs-archive-container">
    <header class="archive-header">
        <?php if (is_post_type_archive('billiard-club')) : ?>
            <h1 class="archive-title"><?php _e('Tất cả câu lạc bộ bi-a', 'clubs-manager'); ?></h1>
        <?php elseif (is_tax('club-area')) : ?>
            <h1 class="archive-title"><?php single_term_title(__('Câu lạc bộ bi-a tại ', 'clubs-manager')); ?></h1>
        <?php endif; ?>
        
        <?php if (term_description()) : ?>
            <div class="archive-description"><?php echo term_description(); ?></div>
        <?php endif; ?>
    </header>
    
    <div class="clubs-archive-content">
        
        <!-- Search Form -->
        <div class="clubs-search-section">
            <?php echo do_shortcode('[clubs_search ajax="true"]'); ?>
        </div>
        
        <!-- Results Count -->
        <div class="clubs-results-info">
            <?php
            global $wp_query;
            $total = $wp_query->found_posts;
            $current_page = max(1, get_query_var('paged'));
            $per_page = $wp_query->query_vars['posts_per_page'];
            $start = ($current_page - 1) * $per_page + 1;
            $end = min($start + $per_page - 1, $total);
            ?>
            
            <?php if ($total > 0) : ?>
                <p class="results-count">
                    <?php printf(
                        __('Hiển thị %1$d-%2$d trong tổng số %3$d câu lạc bộ', 'clubs-manager'),
                        number_format_i18n($start),
                        number_format_i18n($end),
                        number_format_i18n($total)
                    ); ?>
                </p>
            <?php endif; ?>
            
            <!-- Sorting Options -->
            <div class="clubs-sorting">
                <label for="clubs-sort"><?php _e('Sắp xếp theo:', 'clubs-manager'); ?></label>
                <select id="clubs-sort" onchange="clubsSortChange(this.value)">
                    <option value="date-desc" <?php selected(get_query_var('orderby'), 'date'); ?>><?php _e('Mới nhất', 'clubs-manager'); ?></option>
                    <option value="title-asc" <?php selected(get_query_var('orderby'), 'title'); ?>><?php _e('Tên A-Z', 'clubs-manager'); ?></option>
                    <option value="price-asc"><?php _e('Giá thấp - cao', 'clubs-manager'); ?></option>
                    <option value="price-desc"><?php _e('Giá cao - thấp', 'clubs-manager'); ?></option>
                </select>
            </div>
        </div>
        
        <!-- Clubs Grid -->
        <div class="clubs-grid-container">
            <?php if (have_posts()) : ?>
                <div class="clubs-grid" id="clubs-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php include CLUBS_MANAGER_PLUGIN_DIR . 'templates/club-card.php'; ?>
                    <?php endwhile; ?>
                </div>
                
                <!-- Pagination -->
                <div class="clubs-pagination">
                    <?php
                    $pagination_args = array(
                        'prev_text' => '&laquo; ' . __('Trước', 'clubs-manager'),
                        'next_text' => __('Sau', 'clubs-manager') . ' &raquo;',
                        'before_page_number' => '<span class="screen-reader-text">' . __('Trang', 'clubs-manager') . ' </span>',
                    );
                    echo paginate_links($pagination_args);
                    ?>
                </div>
                
            <?php else : ?>
                <div class="no-clubs-found">
                    <h2><?php _e('Không tìm thấy câu lạc bộ nào', 'clubs-manager'); ?></h2>
                    <p><?php _e('Hãy thử thay đổi tiêu chí tìm kiếm của bạn.', 'clubs-manager'); ?></p>
                    <a href="<?php echo get_post_type_archive_link('billiard-club'); ?>" class="button"><?php _e('Xem tất cả câu lạc bộ', 'clubs-manager'); ?></a>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
</div>

<script>
function clubsSortChange(sortValue) {
    var url = new URL(window.location);
    var parts = sortValue.split('-');
    var orderby = parts[0];
    var order = parts[1];
    
    url.searchParams.set('orderby', orderby);
    url.searchParams.set('order', order);
    
    window.location = url.toString();
}
</script>

<?php get_footer(); ?>