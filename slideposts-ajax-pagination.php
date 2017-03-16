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
	add_options_page('SlidePosts Settings', 'SlidePosts', 'manage_options', 'sp_sampleoptions', 'slideposts_options_do_page');
}

// Draw the menu page itself
function slideposts_options_do_page() {
	?>
	<div class="wrap">
		<h2>SlidePosts Settings</h2>
		<form method="post" action="options.php">
			<?php settings_fields('slide_posts_options'); ?>
			<?php $options = get_option('sp_sample'); 
            if( ! isset($options['gallery']) ) { $options['gallery'] = false; }
            ?>
			<table class="form-table">
				<tr valign="top"><th scope="row">Gallery layout</th>
					<td>
                        <input name="sp_sample[gallery]" type="checkbox" value="1" <?php checked('1', $options['gallery']); ?> /><br />
                        <p>Convert the posts into gallery.<br />
                        As to look good and match the planned layout it is best to show such number of posts per page that their number is divisible by 3 - e.g. 3, 6, 9, 12 ...<br />
                        Set in "Posts per page" field</p>
                    </td>
				</tr>
				<tr valign="top"><th scope="row">Category name</th>
					<td><input type="text" name="sp_sample[catname]" value="<?php echo $options['catname']; ?>" /></td>
				</tr>
                <tr valign="top"><th scope="row">Posts per page</th>
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
	// Say our second option must be safe text with no HTML tags
	$input['catname'] =  wp_filter_nohtml_kses($input['catname']);
    $input['postsnumber'] =  wp_filter_nohtml_kses($input['postsnumber']);
	
	return $input;
}

/************************************************************************************************/ 



function my_assets() {
    wp_register_script('ajax-implementation', plugins_url('assets/js/ajax-implementation.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'ajax-implementation' );
    
    wp_enqueue_style( 'slideposts', plugins_url( 'assets/css/slideposts.css', __FILE__ ) );
    
    wp_localize_script( 'ajax-implementation', 'ajaximplementation', array(
    	'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ));
}
add_action( 'wp_enqueue_scripts', 'my_assets' );

/**slidepost_ajax_pagination function**/
add_action( 'wp_ajax_nopriv_slidepost_ajax_pagination', 'slidepost_ajax_pagination' );
add_action( 'wp_ajax_slidepost_ajax_pagination', 'slidepost_ajax_pagination' );
function slidepost_ajax_pagination() {
    
    $options = get_option('sp_sample');
    $cat_name = $options['catname'];
    $postsnum = $options['postsnumber'];
    if ( isset( $options['gallery'] ) ) {
        $ifgallery = 1;
    } else {
        $ifgallery = 0;
    }
    
        $paged = $_POST['page'];
        $args = array( 'category_name' => $cat_name, 'posts_per_page' => $postsnum, 'paged' => $paged, 'post_status' => array('publish') ); 
        $wp_query = new WP_Query( $args );
        $GLOBALS['wp_query'] = $wp_query;
        ?>
        <div class="slidePostsContainer">
            <div class="slidePostsTab">
            <?php
            $count=0;
            while ($wp_query->have_posts()) : $wp_query->the_post(); 
            ?>
                <div class="postBlock postBlock<?php echo $count; ?>">
                    <?php 
                    if ($ifgallery == 1) { ?>
                    <div class="postImg">
                        <a class="postEntryLink" href="<?php the_permalink(); ?>">
                            <div class="postOverContainer">
                                <h3><?php the_title(); ?></h3>
                                <?php if ( has_post_thumbnail() ) {the_post_thumbnail('thumbnail');} ?>
                                <span class="overLay"></span>
                            </div>
                        </a>
                    </div>
                    <?php
                    } else { ?>
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <div class="postImg">
                        <?php if ( has_post_thumbnail() ) {the_post_thumbnail('large');} ?>
                    </div>
                    <div class="postExcerpt"><?php the_content(); ?></div>
                    <?php
                    }
                    ?>
                </div>
            <?php 
            $count++;
            endwhile; ?>
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
        <?php wp_reset_postdata(); ?>
    
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
    if ( isset( $options['gallery'] ) ) {
        $ifgallery = 1;
        $wrapmode = "wrapSlideGallery";
    } else {
        $ifgallery = 0;
        $wrapmode = "wrapSlideList";
    }
    
    $output = '<div class="wrapSlidePosts '. $wrapmode .'">' . "\n"; 
            
            $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
            $args = array( 'category_name' => $cat_name, 'posts_per_page' => $postsnum, 'paged' => $paged, 'post_status' => array('publish') ); 
            $wp_query = new WP_Query($args);
            
            $output .= '<div class="paginateNumber">'. $paged . '</div>';
            $total_pages = $wp_query->max_num_pages; 
            $output .= '<div class="pagesNumber">' . $total_pages . '</div>' . "\n";
            
            $output .= '<div class="slidePostsContainer">' . "\n";
            $output .= '<div class="slidePostsTab">' . "\n";
                $count=0;
                while ($wp_query->have_posts()) : $wp_query->the_post();
                    
                    $output .= '<div class="postBlock postBlock'. $count .'">' . "\n";
                    if ($ifgallery == 1) {
                        $output .= '<div class="postImg">' . "\n";
                        $output .= '<a class="postEntryLink" href="'. get_the_permalink() .'">' . "\n";
                        $output .= '<div class="postOverContainer">' . "\n";
                        $output .= '<h3>' . get_the_title() . '</h3>' . "\n";
                        if ( has_post_thumbnail() ) { $output .= get_the_post_thumbnail(get_the_ID(), 'thumbnail'); }
                        $output .= '<span class="overLay"></span>' . "\n"; 
                        $output .= '</div>' . "\n";
                        $output .= '</a>' . "\n";
                        $output .= '</div>' . "\n";
                    } else {
                        $output .= '<h3><a href="'. get_the_permalink() .'">' . get_the_title() . '</a></h3>' . "\n";
                        $output .= '<div class="postImg">' . "\n";
                        if ( has_post_thumbnail() ) { $output .= get_the_post_thumbnail(get_the_ID(), 'large'); } 
                        $output .= '</div>' . "\n"; 
                        $output .= '<div class="postExcerpt"><p>' . get_the_content() . '</p></div>' . "\n";
                    }
                    $output .= '</div>' . "\n";
                $count++;
                endwhile;
                $output .= '</div>' . "\n";
                $output .= '<div class="slidePostsNav">' . "\n";
                $output .= '<nav class="navigation pagination">' . "\n";
                    if($total_pages == 1) {
                        $output .= '<div class="nav-links">&nbsp;</div>' . "\n";
                    }
                    elseif($paged == $total_pages) {
                        $output .= '<div class="nav-links"><a class="prev page-numbers" href="#">Previous page</a></div>' . "\n";
                    } elseif ($paged == 1) {
                        $output .= '<div class="nav-links"><a class="next page-numbers" href="#">Next page</a></div>' . "\n";
                    } else {
                        $output .= '<div class="nav-links"><a class="prev page-numbers" href="#">Previous page</a></div>' . "\n";
                        $output .= '<div class="nav-links"><a class="next page-numbers" href="#">Next page</a></div>' . "\n";
                    }
                    
                $output .= '</nav>' . "\n";
                $output .= '</div>' . "\n";
            $output .= '</div>' . "\n";
            
            wp_reset_postdata();
    
	$output .= '</div>' . "\n";
    

    return $output;
}
add_shortcode( 'postslist', 'init_listitems' );
/**end shortcode function**/