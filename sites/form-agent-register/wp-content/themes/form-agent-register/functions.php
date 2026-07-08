<?php

/**
 * Form-agent-register Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package form-agent-register
 */

function far_theme_enqueue_styles()
{
  wp_enqueue_style(
    'font-awesome',
    'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'
  );
  wp_enqueue_style(
    'form-agent-register-style',
    get_stylesheet_directory_uri() . '/style.css'
  );

  wp_enqueue_script(
    'bootstrap-autocomplete',
    get_stylesheet_directory_uri() . '/assets/plugins/autocomplete-typeahead-bootstrap/dist/latest/bootstrap-autocomplete.min.js',
    array('jquery'),
    '',
    true
  );

  wp_enqueue_script(
    'far-script',
    get_stylesheet_directory_uri() . '/script.js',
    array('jquery'),
    '1.0.0',
    true
  );
}

/**
 * Enqueue scripts and styles.
 */
add_action('wp_enqueue_scripts', 'far_theme_enqueue_styles');


function email_set_content_type()
{
  return "text/html";
}
add_filter('wp_mail_content_type', 'email_set_content_type');

/**
 * get all post-types functions
 */
$glob = glob(dirname(__DIR__) . '/form-agent-register/post-types/*.php');
foreach ($glob as $filename) {
  include $filename;
}
