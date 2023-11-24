<?php
/**
 * Plugin Name: Ellodave Client Starter
 * Description: Default config for client website build.
 * Version: 2.0.0
 * Author: Richard Henney
 */

/* *** NEW PROJECT TO DO's ***
 - Disable No-index for search engines
 - If needed, add script and upload block-controls-editor.js for gutenberg editor controls
*/


function images_admin_notice(){
    global $pagenow;
    if ( $pagenow == 'upload.php' ) {
         echo '<div class="notice notice-warning is-dismissible">
             <h3><strong>Uploading content to Media Library</strong></h3>
			 <p>Please check if content is already present in the library before uploading to avoid duplication. Remember to include ALT text to any images uploaded. <a href="https://ellodave.sharepoint.com/:w:/g/EW2sl-oWhYRAsV9o1H89714BHC3wyT_LemyxUb1cF_hEiw?e=7jurMM" target="_blank">Read guide to uploading media.</a></p>
         </div>';
    }
}
add_action('admin_notices', 'images_admin_notice');

// Remove wp version number
 function wpb_remove_version() {
	return '';
	}
	add_filter('the_generator', 'wpb_remove_version');

// Remove welcome panel from dash
remove_action('welcome_panel', 'wp_welcome_panel');

// Remove Default Image Links in WordPress
//function wpb_imagelink_setup() {
//    $image_set = get_option( 'image_default_link_type' );
//     
//    if ($image_set !== 'none') {
//        update_option('image_default_link_type', 'none');
//    }
//}
//add_action('admin_init', 'wpb_imagelink_setup', 10);

// Change admin footer to client brand
	function remove_footer_admin () {
 
		echo '<p>Built by <a href="//ellodave.co.uk" target="_blank">ellodave</a></p>';
		 
		}
		 
		add_filter('admin_footer_text', 'remove_footer_admin');

// Disable RSS feeds

    function fb_disable_feed() {
			wp_die( __('No feed available,please visit our <a href="'. get_bloginfo('url') .'">homepage</a>!') );
			}
			 
			add_action('do_feed', 'fb_disable_feed', 1);
			add_action('do_feed_rdf', 'fb_disable_feed', 1);
			add_action('do_feed_rss', 'fb_disable_feed', 1);
			add_action('do_feed_rss2', 'fb_disable_feed', 1);
			add_action('do_feed_atom', 'fb_disable_feed', 1);

remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );

/* Disable jquery_migrate */
function dequeue_jquery_migrate( $scripts ) {
    if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) ) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            [ 'jquery-migrate' ]
        );
    }
}

// Disable WP automatic compression
add_filter( 'jpeg_quality', create_function( '', 'return 100;' ) );

// Disable the emoji's
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );	
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );	
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	
	// Remove from TinyMCE
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
}

// Filter out the tinymce emoji plugin.
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

// Disable WP-EMBED
function my_deregister_scripts(){
  wp_deregister_script( 'wp-embed' );
}

add_action( 'wp_footer', 'my_deregister_scripts' ); // Disable WP-EMBED
add_action( 'init', 'disable_emojis' ); // Disable the emoji's
add_action( 'wp_enqueue_scripts', 'remove_block_css', 100 ); // Disable Gutenberg Block Library
add_action( 'wp_default_scripts', 'dequeue_jquery_migrate' ); // Disable jquery_migrate
add_action( 'wp_enqueue_scripts', 'dequeue_for_logged_users', 100 ); // Disable scripts in frontend to non-admin 

// Disable comments site-wide
add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;
    
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }

    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});

add_action('wp_before_admin_bar_render', function() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
});


// Remove all WP Gutenberg Patterns
function removeGutenbergPatterns() {

    remove_theme_support('core-block-patterns');

}

add_action('after_setup_theme', 'removeGutenbergPatterns');

// Disable post tags
add_action('init', function(){
    register_taxonomy('post_tag', []);
});

// Remove wp logo from admin bar
function example_admin_bar_remove_logo() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );
}
add_action( 'wp_before_admin_bar_render', 'example_admin_bar_remove_logo', 0 );

// Detect user agent and add as body class
add_filter('body_class','browser_body_class'); 
function browser_body_class($classes) { 
 
global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone; 
 
if($is_lynx) $classes[] = 'browser-is-lynx'; 
 
elseif($is_gecko) $classes[] = 'browser-is-gecko'; 
 
elseif($is_opera) $classes[] = 'browser-is-opera'; 
 
elseif($is_NS4) $classes[] = 'browser-is-ns4'; 
 
elseif($is_safari) $classes[] = 'browser-is-safari'; 
 
elseif($is_chrome) $classes[] = 'browser-is-chrome'; 
 
elseif($is_IE) $classes[] = 'browser-is-ie'; 
 
else $classes[] = 'browser-is-unknown'; 
 
if($is_iphone) $classes[] = 'browser-is-iphone'; 
 
return $classes; 
}


/**
 * Disable Native Gutenberg Features
 */
function gutenberg_removals()
{
  add_theme_support('disable-custom-font-sizes');
  add_theme_support('editor-font-sizes', []);
  add_theme_support( 'disable-custom-colors' );
  add_theme_support( 'editor-color-palette' );
  add_theme_support( 'custom-background' );
  //add_theme_support( 'editor-styles' );
}
add_action('after_setup_theme', 'gutenberg_removals');
