<?php

/**
 * Registers the `normativity` post type.
 */
function normativity_init()
{
  register_post_type('normativity', array(
    'labels'                => array(
      'name'                  => __('Normatividad en México', 'fem'),
      'singular_name'         => __('Normativity', 'fem'),
      'all_items'             => __('Artículos sobre Normatividad', 'fem'),
      'archives'              => __('Normativity Archives', 'fem'),
      'attributes'            => __('Normativity Attributes', 'fem'),
      'insert_into_item'      => __('Insert into normativity', 'fem'),
      'uploaded_to_this_item' => __('Uploaded to this normativity', 'fem'),
      'featured_image'        => _x('Featured Image', 'normativity', 'fem'),
      'set_featured_image'    => _x('Set featured image', 'normativity', 'fem'),
      'remove_featured_image' => _x('Remove featured image', 'normativity', 'fem'),
      'use_featured_image'    => _x('Use as featured image', 'normativity', 'fem'),
      'filter_items_list'     => __('Filter normativities list', 'fem'),
      'items_list_navigation' => __('Normativities list navigation', 'fem'),
      'items_list'            => __('Normativities list', 'fem'),
      'new_item'              => __('New Normativity', 'fem'),
      'add_new'               => __('Añadir nueva', 'fem'),
      'add_new_item'          => __('Add New Normativity', 'fem'),
      'edit_item'             => __('Edit Normativity', 'fem'),
      'view_item'             => __('View Normativity', 'fem'),
      'view_items'            => __('View Normativities', 'fem'),
      'search_items'          => __('Search normativities', 'fem'),
      'not_found'             => __('No normativities found', 'fem'),
      'not_found_in_trash'    => __('No normativities found in trash', 'fem'),
      'parent_item_colon'     => __('Parent Normativity:', 'fem'),
      'menu_name'             => __('Normatividad en México', 'fem'),
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
    'rewrite'               => array('slug' => 'normatividad'),
    'query_var'             => true,
    'menu_position'         => 31,
    'menu_icon'             => 'dashicons-format-aside',
    'show_in_rest'          => true,
    'rest_base'             => 'normativities',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
    'description' => 'Conoce las historias de los demás usuarios'
  ));
}
add_action('init', 'normativity_init');

/**
 * Sets the post updated messages for the `normativity` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `normativity` post type.
 */
function normativity_updated_messages($messages)
{
  global $post;

  $permalink = get_permalink($post);

  $messages['normativity'] = array(
    0  => '', // Unused. Messages start at index 1.
    /* translators: %s: post permalink */
    1  => sprintf(__('Normativity updated. <a target="_blank" href="%s">View normativity</a>', 'fem'), esc_url($permalink)),
    2  => __('Custom field updated.', 'fem'),
    3  => __('Custom field deleted.', 'fem'),
    4  => __('Normativity updated.', 'fem'),
    /* translators: %s: date and time of the revision */
    5  => isset($_GET['revision']) ? sprintf(__('Normativity restored to revision from %s', 'fem'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
    /* translators: %s: post permalink */
    6  => sprintf(__('Normativity published. <a href="%s">View normativity</a>', 'fem'), esc_url($permalink)),
    7  => __('Normativity saved.', 'fem'),
    /* translators: %s: post permalink */
    8  => sprintf(__('Normativity submitted. <a target="_blank" href="%s">Preview normativity</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
    /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
    9  => sprintf(
      __('Normativity scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview normativity</a>', 'fem'),
      date_i18n(__('M j, Y @ G:i', 'fem'), strtotime($post->post_date)),
      esc_url($permalink)
    ),
    /* translators: %s: post permalink */
    10 => sprintf(__('Normativity draft updated. <a target="_blank" href="%s">Preview normativity</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
  );

  return $messages;
}
add_filter('post_updated_messages', 'normativity_updated_messages');

/**
 * meta boxes
 */

function normativity_meta_box_add()
{
  global $DojoDigitalHideTitle;
  add_meta_box(
    'dojodigital_toggle_title',
    'Ocultar título',
    array($DojoDigitalHideTitle, 'build_box'),
    'normativity',
    'side'
  );
}
add_action('add_meta_boxes', 'normativity_meta_box_add');
