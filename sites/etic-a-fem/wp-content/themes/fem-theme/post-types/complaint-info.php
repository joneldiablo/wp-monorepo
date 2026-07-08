<?php

/**
 * Registers the `complaint_info` post type.
 */
function complaint_info_init()
{
  register_post_type('complaint-info', array(
    'labels'                => array(
      'name'                  => __('Información para Denuncias', 'fem'),
      'singular_name'         => __('Complaint info', 'fem'),
      'all_items'             => __('Artículos sobre Denuncias Formales', 'fem'),
      'archives'              => __('Complaint info Archives', 'fem'),
      'attributes'            => __('Complaint info Attributes', 'fem'),
      'insert_into_item'      => __('Insert into complaint info', 'fem'),
      'uploaded_to_this_item' => __('Uploaded to this complaint info', 'fem'),
      'featured_image'        => _x('Featured Image', 'complaint-info', 'fem'),
      'set_featured_image'    => _x('Set featured image', 'complaint-info', 'fem'),
      'remove_featured_image' => _x('Remove featured image', 'complaint-info', 'fem'),
      'use_featured_image'    => _x('Use as featured image', 'complaint-info', 'fem'),
      'filter_items_list'     => __('Filter complaint infos list', 'fem'),
      'items_list_navigation' => __('Complaint infos list navigation', 'fem'),
      'items_list'            => __('Complaint infos list', 'fem'),
      'new_item'              => __('New Complaint info', 'fem'),
      'add_new'               => __('Añadir nueva', 'fem'),
      'add_new_item'          => __('Add New Complaint info', 'fem'),
      'edit_item'             => __('Edit Complaint info', 'fem'),
      'view_item'             => __('View Complaint info', 'fem'),
      'view_items'            => __('View Complaint infos', 'fem'),
      'search_items'          => __('Search complaint infos', 'fem'),
      'not_found'             => __('No complaint infos found', 'fem'),
      'not_found_in_trash'    => __('No complaint infos found in trash', 'fem'),
      'parent_item_colon'     => __('Parent Complaint info:', 'fem'),
      'menu_name'             => __('Información para Denuncias', 'fem'),
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
    'rewrite'               => array('slug' => 'informate'),
    'query_var'             => true,
    'menu_position'         => 34,
    'menu_icon'             => 'dashicons-admin-post',
    'show_in_rest'          => true,
    'rest_base'             => 'complaint-info',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
  ));
}
add_action('init', 'complaint_info_init');

/**
 * Sets the post updated messages for the `complaint_info` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `complaint_info` post type.
 */
function complaint_info_updated_messages($messages)
{
  global $post;

  $permalink = get_permalink($post);

  $messages['complaint-info'] = array(
    0  => '', // Unused. Messages start at index 1.
    /* translators: %s: post permalink */
    1  => sprintf(__('Complaint info updated. <a target="_blank" href="%s">View complaint info</a>', 'fem'), esc_url($permalink)),
    2  => __('Custom field updated.', 'fem'),
    3  => __('Custom field deleted.', 'fem'),
    4  => __('Complaint info updated.', 'fem'),
    /* translators: %s: date and time of the revision */
    5  => isset($_GET['revision']) ? sprintf(__('Complaint info restored to revision from %s', 'fem'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
    /* translators: %s: post permalink */
    6  => sprintf(__('Complaint info published. <a href="%s">View complaint info</a>', 'fem'), esc_url($permalink)),
    7  => __('Complaint info saved.', 'fem'),
    /* translators: %s: post permalink */
    8  => sprintf(__('Complaint info submitted. <a target="_blank" href="%s">Preview complaint info</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
    /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
    9  => sprintf(
      __('Complaint info scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview complaint info</a>', 'fem'),
      date_i18n(__('M j, Y @ G:i', 'fem'), strtotime($post->post_date)),
      esc_url($permalink)
    ),
    /* translators: %s: post permalink */
    10 => sprintf(__('Complaint info draft updated. <a target="_blank" href="%s">Preview complaint info</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
  );

  return $messages;
}
add_filter('post_updated_messages', 'complaint_info_updated_messages');


/**
 * meta boxes
 */

function complaint_info_meta_box_add()
{
  global $DojoDigitalHideTitle;
  add_meta_box(
    'dojodigital_toggle_title',
    'Ocultar título',
    array($DojoDigitalHideTitle, 'build_box'),
    'complaint-info',
    'side'
  );
}
add_action('add_meta_boxes', 'complaint_info_meta_box_add');
