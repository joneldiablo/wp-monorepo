<?php

/**
 * Registers the `health_system` post type.
 */
function health_system_init()
{
  register_post_type('health-system', array(
    'labels'                => array(
      'name'                  => __('Sistema Nacional de Salud', 'fem'),
      'singular_name'         => __('Health system', 'fem'),
      'all_items'             => __('Artículos sobre Sistema de Salud', 'fem'),
      'archives'              => __('Health system Archives', 'fem'),
      'attributes'            => __('Health system Attributes', 'fem'),
      'insert_into_item'      => __('Insert into health system', 'fem'),
      'uploaded_to_this_item' => __('Uploaded to this health system', 'fem'),
      'featured_image'        => _x('Featured Image', 'health-system', 'fem'),
      'set_featured_image'    => _x('Set featured image', 'health-system', 'fem'),
      'remove_featured_image' => _x('Remove featured image', 'health-system', 'fem'),
      'use_featured_image'    => _x('Use as featured image', 'health-system', 'fem'),
      'filter_items_list'     => __('Filter health systems list', 'fem'),
      'items_list_navigation' => __('Health systems list navigation', 'fem'),
      'items_list'            => __('Health systems list', 'fem'),
      'new_item'              => __('New Health system', 'fem'),
      'add_new'               => __('Añadir nueva', 'fem'),
      'add_new_item'          => __('Add New Health system', 'fem'),
      'edit_item'             => __('Edit Health system', 'fem'),
      'view_item'             => __('View Health system', 'fem'),
      'view_items'            => __('View Health systems', 'fem'),
      'search_items'          => __('Search health systems', 'fem'),
      'not_found'             => __('No health systems found', 'fem'),
      'not_found_in_trash'    => __('No health systems found in trash', 'fem'),
      'parent_item_colon'     => __('Parent Health system:', 'fem'),
      'menu_name'             => __('Sistema Nacional de Salud', 'fem'),
    ),
    'public'                => true,
    'hierarchical'          => false,
    'show_ui'               => true,
    'show_in_nav_menus'     => true,
    'supports'              => array(
      'title', 'thumbnail', 'editor', 'comments', 'revisions',
      'trackbacks', 'excerpt', 'page-attributes', 'custom-fields', 'post-formats'
    ),
    'has_archive'           => true,
    'rewrite'               => array('slug' => 'sistema-de-salud'),
    'query_var'             => true,
    'menu_position'         => 33,
    'menu_icon'             => 'dashicons-share-alt',
    'show_in_rest'          => true,
    'rest_base'             => 'health-system',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
    'description' => 'Conoce las historias de los demás usuarios'
  ));
}
add_action('init', 'health_system_init');

/**
 * Sets the post updated messages for the `health_system` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `health_system` post type.
 */
function health_system_updated_messages($messages)
{
  global $post;

  $permalink = get_permalink($post);

  $messages['health-system'] = array(
    0  => '', // Unused. Messages start at index 1.
    /* translators: %s: post permalink */
    1  => sprintf(__('Health system updated. <a target="_blank" href="%s">View health system</a>', 'fem'), esc_url($permalink)),
    2  => __('Custom field updated.', 'fem'),
    3  => __('Custom field deleted.', 'fem'),
    4  => __('Health system updated.', 'fem'),
    /* translators: %s: date and time of the revision */
    5  => isset($_GET['revision']) ? sprintf(__('Health system restored to revision from %s', 'fem'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
    /* translators: %s: post permalink */
    6  => sprintf(__('Health system published. <a href="%s">View health system</a>', 'fem'), esc_url($permalink)),
    7  => __('Health system saved.', 'fem'),
    /* translators: %s: post permalink */
    8  => sprintf(__('Health system submitted. <a target="_blank" href="%s">Preview health system</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
    /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
    9  => sprintf(
      __('Health system scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview health system</a>', 'fem'),
      date_i18n(__('M j, Y @ G:i', 'fem'), strtotime($post->post_date)),
      esc_url($permalink)
    ),
    /* translators: %s: post permalink */
    10 => sprintf(__('Health system draft updated. <a target="_blank" href="%s">Preview health system</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
  );

  return $messages;
}
add_filter('post_updated_messages', 'health_system_updated_messages');


/**
 * meta boxes
 */

function health_system_meta_box_add()
{
  global $DojoDigitalHideTitle;
  add_meta_box(
    'dojodigital_toggle_title',
    'Ocultar título',
    array($DojoDigitalHideTitle, 'build_box'),
    'health-system',
    'side'
  );
}
add_action('add_meta_boxes', 'health_system_meta_box_add');
