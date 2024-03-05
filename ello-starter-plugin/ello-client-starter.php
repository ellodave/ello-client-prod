<?php
/**
 * Plugin Name: Ellodave Client Starter
 * Description: Default configuration for client website builds.
 * Version: 3.0.0
 * Author: Richard Henney
 */

// Display admin notice when uploading content to the Media Library
function images_admin_notice() {
    if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'upload.php') {
        echo '<div class="notice notice-warning is-dismissible">
             <h3><strong>Uploading content to Media Library</strong></h3>
             <p>Please check if content is already present in the library before uploading to avoid duplication. Remember to include ALT text to any images uploaded. <a href="https://ellodave.sharepoint.com/:w:/g/EW2sl-oWhYRAsV9o1H89714BHC3wyT_LemyxUb1cF_hEiw?e=7jurMM" target="_blank">Read guide to uploading media.</a></p>
         </div>';
    }
}
add_action('admin_notices', 'images_admin_notice');

// Disable password changed email to admin	
remove_action( 'after_password_reset', 'wp_password_change_notification' );

// Remove welcome panel from dashboard
remove_action('welcome_panel', 'wp_welcome_panel');

// Change admin footer to client brand
function change_admin_footer() {
    echo '<p>Built by <a href="//ellodave.co.uk" target="_blank">ellodave</a></p>';
}
add_filter('admin_footer_text', 'change_admin_footer');

// Disable WP automatic compression
add_filter('jpeg_quality', function () {
    return 100;
});

// Disable comments site-wide
function disable_comments() {
    // Redirect any user trying to access comments page
    if (is_admin() && current_user_can('activate_plugins')) {
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
    }
}
add_action('admin_init', 'disable_comments');

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Remove comments page in menu
function remove_comments_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'remove_comments_menu');

// Remove comments links from admin bar
function remove_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'remove_comments_admin_bar');

add_action('admin_bar_menu', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}, 999);

// Disable post tags
function disable_post_tags() {
    register_taxonomy('post_tag', []);
}
add_action('init', 'disable_post_tags');

// Remove WP logo from admin bar
function remove_wp_logo_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
}
add_action('admin_bar_menu', 'remove_wp_logo_admin_bar', 0);

// Detect user agent and add as body class
function browser_body_class($classes) {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    if (strpos($user_agent, 'Lynx') !== false) {
        $classes[] = 'browser-is-lynx';
    } elseif (strpos($user_agent, 'Gecko') !== false) {
        $classes[] = 'browser-is-gecko';
    } elseif (strpos($user_agent, 'Opera') !== false) {
        $classes[] = 'browser-is-opera';
    } elseif (strpos($user_agent, 'MSIE') !== false) {
        $classes[] = 'browser-is-ie';
    } elseif (strpos($user_agent, 'Safari') !== false) {
        $classes[] = 'browser-is-safari';
    } elseif (strpos($user_agent, 'Chrome') !== false) {
        $classes[] = 'browser-is-chrome';
    } else {
        $classes[] = 'browser-is-unknown';
    }

    if (strpos($user_agent, 'iPhone') !== false) {
        $classes[] = 'browser-is-iphone';
    }

    return $classes;
}
add_filter('body_class', 'browser_body_class');
