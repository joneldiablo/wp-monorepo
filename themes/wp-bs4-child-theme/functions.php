<?php
/**
 * Bs-ved Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package bs-ved
 */

add_action( 'wp_enqueue_scripts', 'wp_bootstrap_4_parent_theme_enqueue_styles' );

/**
 * Enqueue scripts and styles.
 */
function wp_bootstrap_4_parent_theme_enqueue_styles() {
	wp_enqueue_style( 'wp-bootstrap-4-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'bs-ved-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'wp-bootstrap-4-style' )
	);

}
