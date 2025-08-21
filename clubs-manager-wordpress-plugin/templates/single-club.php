<?php
/**
 * Single Club Template
 * 
 * Template for displaying individual club details
 */

get_header(); ?>

<div class="clubs-single-container">
    <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'club-single' ); ?>>
            
            <header class="club-header">
                <div class="club-header-content">
                    <h1 class="club-title"><?php the_title(); ?></h1>
                    
                    <?php
                    $areas = get_the_terms( get_the_ID(), 'club-area' );
                    if ( $areas && ! is_wp_error( $areas ) ) :
                        $area_names = wp_list_pluck( $areas, 'name' );
                    ?>
                        <div class="club-areas">
                            <span class="areas-icon">📍</span>
                            <?php echo implode( ', ', $area_names ); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="club-featured-image">
                            <?php the_post_thumbnail( 'large' ); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </header>
            
            <div class="club-content">
                <?php if ( get_the_content() ) : ?>
                    <div class="club-description">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>
                
                <?php
                // Display club details using frontend class
                if ( class_exists( 'Club_Frontend' ) ) {
                    echo Club_Frontend::render_basic_info( get_the_ID() );
                    echo Club_Frontend::render_contact_info( get_the_ID() );
                    echo Club_Frontend::render_facilities( get_the_ID() );
                    echo Club_Frontend::render_opening_hours( get_the_ID() );
                    echo Club_Frontend::render_gallery( get_the_ID() );
                    
                    // Add map container
                    $address = get_post_meta( get_the_ID(), '_club_address', true );
                    if ( $address ) {
                        echo '<div class="club-map-section">';
                        echo '<h3>' . __( 'Bản đồ', 'clubs-manager' ) . '</h3>';
                        echo '<div id="club-map-container" class="club-map-container" data-address="' . esc_attr( $address ) . '" data-title="' . esc_attr( get_the_title() ) . '" style="height: 400px;"></div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
            
            <footer class="club-footer">
                <div class="club-navigation">
                    <?php
                    $prev_post = get_previous_post( true, '', 'club-area' );
                    $next_post = get_next_post( true, '', 'club-area' );
                    ?>
                    
                    <?php if ( $prev_post ) : ?>
                        <div class="nav-previous">
                            <a href="<?php echo get_permalink( $prev_post->ID ); ?>" rel="prev">
                                <span class="nav-subtitle"><?php _e( 'Câu lạc bộ trước', 'clubs-manager' ); ?></span>
                                <span class="nav-title"><?php echo get_the_title( $prev_post->ID ); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( $next_post ) : ?>
                        <div class="nav-next">
                            <a href="<?php echo get_permalink( $next_post->ID ); ?>" rel="next">
                                <span class="nav-subtitle"><?php _e( 'Câu lạc bộ tiếp theo', 'clubs-manager' ); ?></span>
                                <span class="nav-title"><?php echo get_the_title( $next_post->ID ); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="club-actions">
                    <a href="<?php echo get_post_type_archive_link( 'billiard-club' ); ?>" class="back-to-clubs">
                        <?php _e( '← Quay lại danh sách câu lạc bộ', 'clubs-manager' ); ?>
                    </a>
                    
                    <?php
                    $phone = get_post_meta( get_the_ID(), '_club_phone', true );
                    if ( $phone ) :
                    ?>
                        <a href="tel:<?php echo esc_attr( $phone ); ?>" class="club-call-action">
                            <?php _e( '📞 Gọi ngay', 'clubs-manager' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </footer>
            
        </article>
        
        <?php
        // Related clubs
        $related_clubs = get_posts( array(
            'post_type' => 'billiard-club',
            'posts_per_page' => 3,
            'post__not_in' => array( get_the_ID() ),
            'tax_query' => array(
                array(
                    'taxonomy' => 'club-area',
                    'field' => 'term_id',
                    'terms' => wp_get_post_terms( get_the_ID(), 'club-area', array( 'fields' => 'ids' ) ),
                ),
            ),
        ) );
        
        if ( $related_clubs ) :
        ?>
            <section class="related-clubs">
                <h3><?php _e( 'Câu lạc bộ liên quan', 'clubs-manager' ); ?></h3>
                <div class="related-clubs-grid">
                    <?php foreach ( $related_clubs as $related_club ) : ?>
                        <div class="related-club-item">
                            <?php if ( has_post_thumbnail( $related_club->ID ) ) : ?>
                                <div class="related-club-image">
                                    <a href="<?php echo get_permalink( $related_club->ID ); ?>">
                                        <?php echo get_the_post_thumbnail( $related_club->ID, 'medium' ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="related-club-content">
                                <h4><a href="<?php echo get_permalink( $related_club->ID ); ?>"><?php echo get_the_title( $related_club->ID ); ?></a></h4>
                                
                                <?php
                                $related_address = get_post_meta( $related_club->ID, '_club_address', true );
                                $related_price = get_post_meta( $related_club->ID, '_club_price', true );
                                ?>
                                
                                <?php if ( $related_address ) : ?>
                                    <p class="related-club-address">📍 <?php echo esc_html( wp_trim_words( $related_address, 6 ) ); ?></p>
                                <?php endif; ?>
                                
                                <?php if ( $related_price ) : ?>
                                    <p class="related-club-price">💰 <?php echo number_format( $related_price, 0, ',', '.' ); ?> VNĐ/giờ</p>
                                <?php endif; ?>
                                
                                <a href="<?php echo get_permalink( $related_club->ID ); ?>" class="related-club-link"><?php _e( 'Xem chi tiết', 'clubs-manager' ); ?></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>