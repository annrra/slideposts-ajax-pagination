<?php
/**
 * Plugin Name: Slide Posts Ajax Pagination
 * Plugin URI: http://bettermonday.org
 * Description: This plugin adds posts from custom category with ajax pagination.
 * Version: 1.0.0
 * Author: Andrey Raychev
 * Author URI: http://bettermonday.org
 * License: GPL2
 */

function my_assets() {
	//wp_register_script( 'ajax-implementation', get_template_directory_uri().'/assets/js/ajax-implementation.js', array( 'jquery' ) );
    wp_register_script('ajax-implementation', plugins_url('assets/js/ajax-implementation.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'ajax-implementation' );
    
    wp_enqueue_style( 'slideposts', plugins_url( 'assets/css/slideposts.css', __FILE__ ) );
    
    global $wp_query;
    wp_localize_script( 'ajax-implementation', 'ajaximplementation', array(
    	'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'query_vars' => json_encode( $wp_query->query )
    ));
}
add_action( 'wp_enqueue_scripts', 'my_assets' );

/**slidepost_ajax_pagination function**/
add_action( 'wp_ajax_nopriv_slidepost_ajax_pagination', 'slidepost_ajax_pagination' );
add_action( 'wp_ajax_slidepost_ajax_pagination', 'slidepost_ajax_pagination' );
function slidepost_ajax_pagination() {
    $query_vars = json_decode( stripslashes( $_POST['query_vars'] ), true );
    $query_vars['paged'] = $_POST['page'];
    
        $paged = $_POST['page'];
        $args = array( 'category_name' => 'work', 'posts_per_page' => 3, 'paged' => $paged ); 
        $wp_query = new WP_Query( $query_vars );
        $GLOBALS['wp_query'] = $wp_query;
        $wp_query->query( $args );
        ?>
        <div class="slidePostsContainer">
            <div class="slidePostsTab">
            <?php
            while ($wp_query->have_posts()) : $wp_query->the_post(); 
            ?>
                <div class="postBlock">
                    <?php the_title(); ?>
                </div>
            <?php endwhile; ?>
            </div>
            <div class="slidePostsNav">
                <?php
                the_posts_pagination( array(
                    'prev_text' => __( 'Previous page', 'twentyseventeen' ),
                    'next_text' => __( 'Next page', 'twentyseventeen' ),
                ) );
                ?>
            </div>
        </div>
        <?php wp_reset_query(); ?>
    
    <?php
    die();    
}
/**end slidepost_ajax_pagination function**/

/**shortcode function**/
function init_listitems( $atts ) {
    extract( shortcode_atts( array(
        'category-name' => '',
        'post-per-page' => ''
    ), $atts ) );
    
    $output = '<div class="wrapSlidePosts">'; 
            
            $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
            $args = array( 'category_name' => $atts['category-name'], 'posts_per_page' => $atts['post-per-page'], 'paged' => $paged ); 
            $wp_query = new WP_Query();
            $wp_query->query( $args );
            
            $output .= '<div class="paginateNumber">'. $paged . '</div>';
            $total_pages = $wp_query->max_num_pages; 
            $output .= '<div class="pagesNumber">' . $total_pages . '</div>';
            $output .= '<div class="catName">' . $atts['category-name'] . '</div>';
            
            $output .= '<div class="slidePostsContainer"><div class="slidePostsTab">';
                while ($wp_query->have_posts()) : $wp_query->the_post(); 
                
                    $output .= '<div class="postBlock">' . get_the_title() . '</div>';
    
                endwhile;
                $output .= '</div>';
                $output .= '<div class="slidePostsNav">';
                    if($paged == $total_pages) {
                        $output .= '<div class="nav-links"><a class="prev page-numbers" href="#">Previous page</a></div>';
                    } elseif ($paged == 1) {
                        $output .= '<div class="nav-links"><a class="next page-numbers" href="#">Next page</a></div>';
                    } else {
                        $output .= '<div class="nav-links"><a class="prev page-numbers" href="#">Previous page</a></div>';
                        $output .= '<div class="nav-links"><a class="next page-numbers" href="#">Next page</a></div>';
                    }
                    
                    //$output .= the_posts_pagination( array('prev_text' => __( 'Previous page', 'twentyseventeen' ), 'next_text' => __( 'Next page', 'twentyseventeen' ), 'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentyseventeen' ) . ' </span>', ) );
                    
                $output .= '</div>';
            $output .= '</div>';
            
            wp_reset_query();
    
	$output .= '</div>';
    

    return $output;
}
add_shortcode( 'postslist', 'init_listitems' );
/**end shortcode function**/