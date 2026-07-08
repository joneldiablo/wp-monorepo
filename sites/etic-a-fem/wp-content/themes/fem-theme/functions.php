<?php

/**
 * Fem-theme Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package fem-theme
 */

add_action('wp_enqueue_scripts', 'wp_bootstrap_starter_parent_theme_enqueue_styles');

/**
 * Enqueue scripts and styles.
 */
function wp_bootstrap_starter_parent_theme_enqueue_styles()
{
	//wp_enqueue_style('wp-bootstrap-starter-style', get_template_directory_uri() . '/style.css');
	wp_enqueue_style(
		'owl',
		'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css',
		array()
	);
	wp_enqueue_style(
		'fem-theme-style',
		get_stylesheet_directory_uri() . '/style.css',
		array('owl')
	);

	wp_enqueue_script(
		'owl',
		'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
		array('jquery', 'wp-bootstrap-starter-bootstrapjs'),
		'',
		true
	);

	wp_enqueue_script(
		'fem-script',
		get_stylesheet_directory_uri() . '/script.js',
		array('jquery', 'wp-bootstrap-starter-bootstrapjs', 'wp-bootstrap-starter-themejs'),
		'1.0.0',
		true
	);
	wp_localize_script('fem-script', 'WPURLS', array('siteurl' => get_option('siteurl')));
}

function fem_theme_menus()
{
	register_nav_menu('footer-menu', __('Menú de pie de página'));
	add_filter('nav_menu_link_attributes', function ($atts) {
		$atts['class'] = "nav-link";
		return $atts;
	}, 100, 1);
	add_filter('nav_menu_css_class', function ($classes) {
		$classes[] = 'nav-item';
		return $classes;
	}, 10, 1);
}
add_action('init', 'fem_theme_menus');

/**
 * post-types
 */
$glob = glob(get_theme_file_path() . '/post-types/*.php');
foreach ($glob as $filename) {
	include $filename;
}


/**
 * debug
 */
add_action('admin_bar_menu', 'show_template');
function show_template()
{
	global $template;
	//error_log($template);
}

function mef_link_counters($atts, $item, $args)
{
	if ($item->object_id && $item->object === 'page' && $args->theme_location === 'primary') {
		$counter = get_post_meta($item->object_id, 'counter', true);
		$counter = $counter ? $counter : 0;
		$item->title = $item->title .
			' <span class="badge badge-light" style="font-family:myriad; vertical-align: text-bottom;">' . $counter . '</span>';
	}
	return $atts;
}
add_filter('nav_menu_link_attributes', 'mef_link_counters', 10, 3);
