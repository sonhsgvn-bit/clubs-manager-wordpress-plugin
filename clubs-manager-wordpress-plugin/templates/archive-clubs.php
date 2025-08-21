<?php
/**
 * Archive Clubs Template
 * 
 * Template for displaying club listings and archives
 */

get_header(); ?>

<div class="clubs-archive-container">
    <header class="clubs-archive-header">
        <?php if ( is_tax( 'club-area' ) ) : ?>
            <h1 class="archive-title">
                <?php printf( __( 'Câu lạc bộ khu vực: %s', 'clubs-manager' ), single_term_title( '', false ) ); ?>
            </h1>
            <?php
            $term_description = term_description();
            if ( $term_description ) :
            ?>
                <div class="archive-description"><?php echo $term_description; ?></div>
            <?php endif; ?>
        <?php else : ?>
            <h1 class="archive-title"><?php _e( 'Tất cả câu lạc bộ bi-a', 'clubs-manager' ); ?></h1>
            <p class="archive-description"><?php _e( 'Khám phá các câu lạc bộ bi-a tốt nhất trong khu vực của bạn', 'clubs-manager' ); ?></p>
        <?php endif; ?>
    </header>
    
    <div class="clubs-archive-content">
        <!-- Search Form -->
        <div class="clubs-search-section">
            <?php echo do_shortcode( '[clubs_search show_filters="true" ajax="true"]' ); ?>
        </div>
        
        <!-- Clubs Listing -->
        <div class="clubs-listing-section">
            <?php if ( have_posts() ) : ?>
                
                <div class="clubs-listing-header">
                    <div class="clubs-count">
                        <?php
                        global $wp_query;
                        printf( 
                            _n( 
                                'Tìm thấy %s câu lạc bộ', 
                                'Tìm thấy %s câu lạc bộ', 
                                $wp_query->found_posts, 
                                'clubs-manager' 
                            ), 
                            number_format_i18n( $wp_query->found_posts ) 
                        );
                        ?>
                    </div>
                    
                    <div class="clubs-sorting">
                        <label for="clubs-sort"><?php _e( 'Sắp xếp:', 'clubs-manager' ); ?></label>
                        <select id="clubs-sort" name="clubs_sort">
                            <option value="title_asc" <?php selected( get_query_var( 'orderby' ), 'title' ); ?>><?php _e( 'Tên A-Z', 'clubs-manager' ); ?></option>
                            <option value="title_desc"><?php _e( 'Tên Z-A', 'clubs-manager' ); ?></option>
                            <option value="price_asc"><?php _e( 'Giá thấp đến cao', 'clubs-manager' ); ?></option>
                            <option value="price_desc"><?php _e( 'Giá cao đến thấp', 'clubs-manager' ); ?></option>
                            <option value="date_desc"><?php _e( 'Mới nhất', 'clubs-manager' ); ?></option>
                        </select>
                    </div>
                    
                    <div class="clubs-view-toggle">
                        <button class="view-toggle active" data-view="grid" title="<?php _e( 'Xem dạng lưới', 'clubs-manager' ); ?>">
                            <span class="dashicons dashicons-grid-view"></span>
                        </button>
                        <button class="view-toggle" data-view="list" title="<?php _e( 'Xem dạng danh sách', 'clubs-manager' ); ?>">
                            <span class="dashicons dashicons-list-view"></span>
                        </button>
                    </div>
                </div>
                
                <div class="clubs-grid" id="clubs-grid">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <?php include CLUBS_MANAGER_DIR . 'templates/club-card.php'; ?>
                    <?php endwhile; ?>
                </div>
                
                <?php
                // Pagination
                $pagination = paginate_links( array(
                    'format' => '?paged=%#%',
                    'current' => max( 1, get_query_var( 'paged' ) ),
                    'total' => $wp_query->max_num_pages,
                    'prev_text' => __( '‹ Trước', 'clubs-manager' ),
                    'next_text' => __( 'Sau ›', 'clubs-manager' ),
                    'type' => 'list',
                ) );
                
                if ( $pagination ) :
                ?>
                    <nav class="clubs-pagination">
                        <?php echo $pagination; ?>
                    </nav>
                <?php endif; ?>
                
            <?php else : ?>
                
                <div class="clubs-no-results">
                    <h3><?php _e( 'Không tìm thấy câu lạc bộ nào', 'clubs-manager' ); ?></h3>
                    <p><?php _e( 'Không có câu lạc bộ nào phù hợp với tiêu chí tìm kiếm của bạn.', 'clubs-manager' ); ?></p>
                    
                    <div class="no-results-suggestions">
                        <h4><?php _e( 'Thử các gợi ý sau:', 'clubs-manager' ); ?></h4>
                        <ul>
                            <li><?php _e( 'Kiểm tra lại từ khóa tìm kiếm', 'clubs-manager' ); ?></li>
                            <li><?php _e( 'Thử tìm kiếm với từ khóa tổng quát hơn', 'clubs-manager' ); ?></li>
                            <li><?php _e( 'Xóa bớt bộ lọc', 'clubs-manager' ); ?></li>
                        </ul>
                    </div>
                    
                    <a href="<?php echo get_post_type_archive_link( 'billiard-club' ); ?>" class="reset-search">
                        <?php _e( 'Xem tất cả câu lạc bộ', 'clubs-manager' ); ?>
                    </a>
                </div>
                
            <?php endif; ?>
        </div>
        
        <!-- Sidebar with areas and quick stats -->
        <div class="clubs-archive-sidebar">
            
            <!-- Areas Widget -->
            <div class="sidebar-widget areas-widget">
                <h3><?php _e( 'Khu vực', 'clubs-manager' ); ?></h3>
                <?php
                $areas = get_terms( array(
                    'taxonomy' => 'club-area',
                    'hide_empty' => true,
                ) );
                
                if ( $areas && ! is_wp_error( $areas ) ) :
                ?>
                    <ul class="areas-list">
                        <li class="area-item">
                            <a href="<?php echo get_post_type_archive_link( 'billiard-club' ); ?>" 
                               class="<?php echo is_post_type_archive( 'billiard-club' ) ? 'current' : ''; ?>">
                                <?php _e( 'Tất cả khu vực', 'clubs-manager' ); ?>
                            </a>
                        </li>
                        <?php foreach ( $areas as $area ) : ?>
                            <li class="area-item">
                                <a href="<?php echo get_term_link( $area ); ?>" 
                                   class="<?php echo is_tax( 'club-area', $area->slug ) ? 'current' : ''; ?>">
                                    <?php echo esc_html( $area->name ); ?>
                                    <span class="area-count">(<?php echo $area->count; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <!-- Quick Stats Widget -->
            <div class="sidebar-widget stats-widget">
                <h3><?php _e( 'Thống kê', 'clubs-manager' ); ?></h3>
                <?php
                $total_clubs = wp_count_posts( 'billiard-club' );
                $published_clubs = $total_clubs->publish;
                $total_areas = wp_count_terms( 'club-area' );
                
                // Get average price
                global $wpdb;
                $avg_price = $wpdb->get_var( "
                    SELECT AVG(CAST(meta_value AS UNSIGNED)) 
                    FROM {$wpdb->postmeta} 
                    WHERE meta_key = '_club_price' 
                    AND meta_value != '' 
                    AND meta_value > 0
                " );
                ?>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo number_format( $published_clubs ); ?></span>
                        <span class="stat-label"><?php _e( 'Câu lạc bộ', 'clubs-manager' ); ?></span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo number_format( $total_areas ); ?></span>
                        <span class="stat-label"><?php _e( 'Khu vực', 'clubs-manager' ); ?></span>
                    </div>
                    
                    <?php if ( $avg_price ) : ?>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo number_format( $avg_price, 0, ',', '.' ); ?></span>
                            <span class="stat-label"><?php _e( 'Giá TB (VNĐ)', 'clubs-manager' ); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Featured Clubs Widget -->
            <?php
            $featured_clubs = get_posts( array(
                'post_type' => 'billiard-club',
                'posts_per_page' => 3,
                'meta_query' => array(
                    array(
                        'key' => '_club_featured',
                        'value' => '1',
                        'compare' => '=',
                    ),
                ),
                'orderby' => 'rand',
            ) );
            
            if ( $featured_clubs ) :
            ?>
                <div class="sidebar-widget featured-widget">
                    <h3><?php _e( 'Câu lạc bộ nổi bật', 'clubs-manager' ); ?></h3>
                    <div class="featured-clubs">
                        <?php foreach ( $featured_clubs as $featured_club ) : ?>
                            <div class="featured-club-item">
                                <?php if ( has_post_thumbnail( $featured_club->ID ) ) : ?>
                                    <div class="featured-club-thumb">
                                        <a href="<?php echo get_permalink( $featured_club->ID ); ?>">
                                            <?php echo get_the_post_thumbnail( $featured_club->ID, 'thumbnail' ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="featured-club-info">
                                    <h4><a href="<?php echo get_permalink( $featured_club->ID ); ?>"><?php echo get_the_title( $featured_club->ID ); ?></a></h4>
                                    <?php
                                    $featured_price = get_post_meta( $featured_club->ID, '_club_price', true );
                                    if ( $featured_price ) :
                                    ?>
                                        <span class="featured-price"><?php echo number_format( $featured_price, 0, ',', '.' ); ?> VNĐ/giờ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php get_footer(); ?>