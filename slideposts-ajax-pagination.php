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
 
add_action('admin_init', 'slide_posts_init' );
add_action('admin_menu', 'slideposts_options_add_page');

// Init plugin options to white list our options
function slide_posts_init(){
	register_setting( 'slide_posts_options', 'sp_sample', 'slideposts_options_validate' );
}

// Add menu page
function slideposts_options_add_page() {
	add_options_page('SlidePosts Options', 'SlidePosts Settings', 'manage_options', 'sp_sampleoptions', 'slideposts_options_do_page');
}

// Draw the menu page itself
function slideposts_options_do_page() {
	?>
	<div class="wrap">
		<h2>SlidePosts Options</h2>
		<form method="post" action="options.php">
			<?php settings_fields('slide_posts_options'); ?>
			<?php $options = get_option('sp_sample'); ?>
			<table class="form-table">
				<tr valign="top"><th scope="row">Convert the posts into gallery</th>
					<td>
                        <input name="sp_sample[option1]" type="checkbox" value="1" <?php checked('1', $options['option1']); ?> /><br />
                        <p>This part is in progress yet ...</p>
                    </td>
				</tr>
				<tr valign="top"><th scope="row">Category name</th>
					<td><input type="text" name="sp_sample[catname]" value="<?php echo $options['catname']; ?>" /></td>
				</tr>
                <tr valign="top"><th scope="row">Posts per age</th>
					<td><input type="text" name="sp_sample[postsnumber]" value="<?php echo $options['postsnumber']; ?>" /></td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function slideposts_options_validate($input) {
	// Our first value is either 0 or 1
	$input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );
	
	// Say our second option must be safe text with no HTML tags
	$input['catname'] =  wp_filter_nohtml_kses($input['catname']);
    $input['postsnumber'] =  wp_filter_nohtml_kses($input['postsnumber']);
	
	return $input;
}

/************************************************************************************************/ 



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
    
    $options = get_option('sp_sample');
    $cat_name = $options['catname'];
    $postsnum = $options['postsnumber'];
    
        $paged = $_POST['page'];
        $args = array( 'category_name' => $cat_name, 'posts_per_page' => $postsnum, 'paged' => $paged ); 
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
                    <h3><?php the_title(); ?></h3>
                    <div class="postImg">
                        <?php 
                        if ( has_post_thumbnail() ) {
                        	the_post_thumbnail('large');
                        } ?>
                    </div>
                    <div class="postExcerpt"><?php the_content(); ?></div>
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
    /* will not use shortcode parameters as data is taken from wp_options
    extract( shortcode_atts( array(
        'category-name' => '',
        'post-per-page' => ''
    ), $atts ) );
    */
    $options = get_option('sp_sample');
    $cat_name = $options['catname'];
    $postsnum = $options['postsnumber'];
    
    $output = '<div class="wrapSlidePosts">'; 
            
            $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
            $args = array( 'category_name' => $cat_name, 'posts_per_page' => $postsnum, 'paged' => $paged ); 
            $wp_query = new WP_Query();
            $wp_query->query( $args );
            
            $output .= '<div class="paginateNumber">'. $paged . '</div>';
            $total_pages = $wp_query->max_num_pages; 
            $output .= '<div class="pagesNumber">' . $total_pages . '</div>';
            
            $output .= '<div class="slidePostsContainer"><div class="slidePostsTab">';
                while ($wp_query->have_posts()) : $wp_query->the_post(); 
                    
                    $output .= '<div class="postBlock">';
                    $output .= '<h3>' . get_the_title() . '</h3>';
                    $output .= '<div class="postImg">';
                        if ( has_post_thumbnail() ) {
                        	$output .= get_the_post_thumbnail($post->ID);
                        } 
                    $output .= '</div>';
                    $output .= '<div class="postExcerpt"><p>' . get_the_content() . '</p></div>';
                    $output .= '</div>';
    
                endwhile;
                $output .= '</div>';
                $output .= '<div class="slidePostsNav"><nav class="navigation pagination">';
                    if($paged == $total_pages) {
                        $output .= '<div class="nav-links"><a class="prev page-numbers" href="#">Previous page</a></div>';
                    } elseif ($paged == 1) {
                        $output .= '<div class="nav-links"><a class="next page-numbers" href="#">Next page</a></div>';
                    } else {
                        $output .= '<div class="nav-links"><a class="prev page-numbers" href="#">Previous page</a></div>';
                        $output .= '<div class="nav-links"><a class="next page-numbers" href="#">Next page</a></div>';
                    }
                    
                    //$output .= the_posts_pagination( array('prev_text' => __( 'Previous page', 'twentyseventeen' ), 'next_text' => __( 'Next page', 'twentyseventeen' ), 'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentyseventeen' ) . ' </span>', ) );
                    
                $output .= '</nav></div>';
            $output .= '</div>';
            
            wp_reset_query();
    
	$output .= '</div>';
    

    return $output;
}
add_shortcode( 'postslist', 'init_listitems' );
/**end shortcode function**/