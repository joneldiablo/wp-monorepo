<?php

/**
 * Registers the `formal_complaints` post type.
 */
function formal_complaints_init()
{
  register_post_type('formal-complaints', array(
    'labels'                => array(
      'name'                  => __('Denuncias formales', 'fem'),
      'singular_name'         => __('Denuncia formal', 'fem'),
      'all_items'             => __('Ligas a Denuncias Formales', 'fem'),
      'archives'              => __('Formal complaints Archives', 'fem'),
      'attributes'            => __('Formal complaints Attributes', 'fem'),
      'insert_into_item'      => __('Insert into formal complaints', 'fem'),
      'uploaded_to_this_item' => __('Uploaded to this formal complaints', 'fem'),
      'featured_image'        => _x('Featured Image', 'formal-complaints', 'fem'),
      'set_featured_image'    => _x('Set featured image', 'formal-complaints', 'fem'),
      'remove_featured_image' => _x('Remove featured image', 'formal-complaints', 'fem'),
      'use_featured_image'    => _x('Use as featured image', 'formal-complaints', 'fem'),
      'filter_items_list'     => __('Filter formal complaints list', 'fem'),
      'items_list_navigation' => __('Formal complaints list navigation', 'fem'),
      'items_list'            => __('Formal complaints list', 'fem'),
      'new_item'              => __('New Formal complaints', 'fem'),
      'add_new'               => __('Añadir nueva', 'fem'),
      'add_new_item'          => __('Add New Formal complaints', 'fem'),
      'edit_item'             => __('Edit Formal complaints', 'fem'),
      'view_item'             => __('View Formal complaints', 'fem'),
      'view_items'            => __('View Formal complaints', 'fem'),
      'search_items'          => __('Search formal complaints', 'fem'),
      'not_found'             => __('No formal complaints found', 'fem'),
      'not_found_in_trash'    => __('No formal complaints found in trash', 'fem'),
      'parent_item_colon'     => __('Parent Formal complaints:', 'fem'),
      'menu_name'             => __('Denuncias formales', 'fem'),
    ),
    'public'                => true,
    'hierarchical'          => false,
    'show_ui'               => true,
    'show_in_nav_menus'     => true,
    'supports'              => array('title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields', 'post-formats'),
    'has_archive'           => true,
    'rewrite'               => array('slug' => 'denuncia-formal'),
    'query_var'             => true,
    'menu_position'         => 35,
    'menu_icon'             => 'dashicons-clipboard',
    'show_in_rest'          => true,
    'rest_base'             => 'formal-complaints',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
    'description' => 'Da click en los logos y te llevaremos directamente donde puedes levantar una queja formal'
  ));
}
add_action('init', 'formal_complaints_init');

/**
 * Sets the post updated messages for the `formal_complaints` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `formal_complaints` post type.
 */
function formal_complaints_updated_messages($messages)
{
  global $post;

  $permalink = get_permalink($post);

  $messages['formal-complaints'] = array(
    0  => '', // Unused. Messages start at index 1.
    /* translators: %s: post permalink */
    1  => sprintf(__('Formal complaints updated. <a target="_blank" href="%s">View formal complaints</a>', 'fem'), esc_url($permalink)),
    2  => __('Custom field updated.', 'fem'),
    3  => __('Custom field deleted.', 'fem'),
    4  => __('Formal complaints updated.', 'fem'),
    /* translators: %s: date and time of the revision */
    5  => isset($_GET['revision']) ? sprintf(__('Formal complaints restored to revision from %s', 'fem'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
    /* translators: %s: post permalink */
    6  => sprintf(__('Formal complaints published. <a href="%s">View formal complaints</a>', 'fem'), esc_url($permalink)),
    7  => __('Formal complaints saved.', 'fem'),
    /* translators: %s: post permalink */
    8  => sprintf(__('Formal complaints submitted. <a target="_blank" href="%s">Preview formal complaints</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
    /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
    9  => sprintf(
      __('Formal complaints scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview formal complaints</a>', 'fem'),
      date_i18n(__('M j, Y @ G:i', 'fem'), strtotime($post->post_date)),
      esc_url($permalink)
    ),
    /* translators: %s: post permalink */
    10 => sprintf(__('Formal complaints draft updated. <a target="_blank" href="%s">Preview formal complaints</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
  );

  return $messages;
}
add_filter('post_updated_messages', 'formal_complaints_updated_messages');
