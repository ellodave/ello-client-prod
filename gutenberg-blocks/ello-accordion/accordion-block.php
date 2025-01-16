<?php
/**
 * Plugin Name: Accordion Block
 * Description: Adds a Gutenberg block for creating toggle-style accordions
 * Version: 1.0.0
 * Author: Richard Henney
 * License: GPL v2 or later
 * Text Domain: ello-accordion-block
 */

if (!defined('ABSPATH')) {
    exit;
}

function accordion_block_register() {
    wp_register_script(
        'ello-accordion-block-editor',
        plugins_url('src/index.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components'),
        filemtime(plugin_dir_path(__FILE__) . 'src/index.js')
    );

    wp_register_style(
        'ello-accordion-block-style',
        plugins_url('src/style.css', __FILE__),
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'src/style.css')
    );

    register_block_type('ello-accordion-block/main', array(
        'editor_script' => 'ello-accordion-block-editor',
        'editor_style'  => 'ello-accordion-block-style',
        'style'         => 'ello-accordion-block-style',
        'attributes' => array(
            'items' => array(
                'type' => 'array',
                'default' => array(),
            ),
        ),
        'render_callback' => 'render_accordion_block'
    ));
}

function render_accordion_block($attributes) {
    if (empty($attributes['items'])) {
        return '';
    }

    $output = '<div class="wp-block-ello-accordion-block">';
    
    foreach ($attributes['items'] as $item) {
        $image_html = '';
        if (!empty($item['imageUrl'])) {
            $image_html = sprintf(
                '<img src="%s" alt="%s" class="accordion-image"/>',
                esc_url($item['imageUrl']),
                esc_attr($item['imageAlt'])
            );
        }
        
        $output .= sprintf(
            '<details class="accordion-item">
                <summary class="accordion-header">%s</summary>
                <div class="accordion-content">
                    %s
                    %s
                </div>
            </details>',
            wp_kses_post($item['title']),
            $image_html,
            wp_kses_post($item['content'])
        );
    }
    
    $output .= '</div>';
    
    return $output;
}

add_action('init', 'accordion_block_register');
