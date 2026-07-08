<?php

/**
 * Registers the `complaint` post type.
 */
function complaint_init()
{
  register_post_type('complaint', array(
    'labels'                => array(
      'name'                  => __('Denuncias', 'fem'),
      'singular_name'         => __('Denuncia', 'fem'),
      'all_items'             => __('Todas las denuncias', 'fem'),
      'archives'              => __('Complaint Archives', 'fem'),
      'attributes'            => __('Complaint Attributes', 'fem'),
      'insert_into_item'      => __('Insert into complaint', 'fem'),
      'uploaded_to_this_item' => __('Uploaded to this complaint', 'fem'),
      'featured_image'        => _x('Featured Image', 'complaint', 'fem'),
      'set_featured_image'    => _x('Set featured image', 'complaint', 'fem'),
      'remove_featured_image' => _x('Remove featured image', 'complaint', 'fem'),
      'use_featured_image'    => _x('Use as featured image', 'complaint', 'fem'),
      'filter_items_list'     => __('Filter complaints list', 'fem'),
      'items_list_navigation' => __('Complaints list navigation', 'fem'),
      'items_list'            => __('Complaints list', 'fem'),
      'new_item'              => __('New Complaint', 'fem'),
      'add_new'               => __('Añadir nueva', 'fem'),
      'add_new_item'          => __('Add New Complaint', 'fem'),
      'edit_item'             => __('Edit Complaint', 'fem'),
      'view_item'             => __('View Complaint', 'fem'),
      'view_items'            => __('View Complaints', 'fem'),
      'search_items'          => __('Search complaints', 'fem'),
      'not_found'             => __('No se han encontrado Denuncias', 'fem'),
      'not_found_in_trash'    => __('No se han encontrado Denuncias en la papelera', 'fem'),
      'parent_item_colon'     => __('Parent Complaint:', 'fem'),
      'menu_name'             => __('Denuncias', 'fem'),
    ),
    'public'                => true,
    'hierarchical'          => false,
    'show_ui'               => true,
    'show_in_nav_menus'     => true,
    'supports'              => array('title', 'thumbnail', 'editor', 'comments', 'revisions',
    'trackbacks', 'excerpt', 'page-attributes', 'custom-fields', 'post-formats'),
    'has_archive'           => true,
    'rewrite'               => array('slug' => 'denuncias'),
    'query_var'             => true,
    'menu_position'         => 30,
    'menu_icon'             => 'dashicons-megaphone',
    'show_in_rest'          => true,
    'rest_base'             => 'complaints',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
    'description' => 'Conoce las historias de los demás usuarios'
  ));
}
add_action('init', 'complaint_init');

/**
 * Sets the post updated messages for the `complaint` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `complaint` post type.
 */
function complaint_updated_messages($messages)
{
  global $post;

  $permalink = get_permalink($post);

  $messages['complaint'] = array(
    0  => '', // Unused. Messages start at index 1.
    /* translators: %s: post permalink */
    1  => sprintf(__('Complaint updated. <a target="_blank" href="%s">View complaint</a>', 'fem'), esc_url($permalink)),
    2  => __('Custom field updated.', 'fem'),
    3  => __('Custom field deleted.', 'fem'),
    4  => __('Complaint updated.', 'fem'),
    /* translators: %s: date and time of the revision */
    5  => isset($_GET['revision']) ? sprintf(__('Complaint restored to revision from %s', 'fem'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
    /* translators: %s: post permalink */
    6  => sprintf(__('Complaint published. <a href="%s">View complaint</a>', 'fem'), esc_url($permalink)),
    7  => __('Complaint saved.', 'fem'),
    /* translators: %s: post permalink */
    8  => sprintf(__('Complaint submitted. <a target="_blank" href="%s">Preview complaint</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
    /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
    9  => sprintf(
      __('Complaint scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview complaint</a>', 'fem'),
      date_i18n(__('M j, Y @ G:i', 'fem'), strtotime($post->post_date)),
      esc_url($permalink)
    ),
    /* translators: %s: post permalink */
    10 => sprintf(__('Complaint draft updated. <a target="_blank" href="%s">Preview complaint</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
  );

  return $messages;
}
add_filter('post_updated_messages', 'complaint_updated_messages');

/**
 * 
 */
function complaint_meta_box()
{
  add_meta_box('register-fields', __('Datos de registro'), 'complaint_meta_box_render', 'complaint');
}
add_action('add_meta_boxes', 'complaint_meta_box');

function complaint_meta_box_render($post)
{
  schema_meta_box_render($post, 'complaint_meta_box_nonce', 'denuncia-form.pug', 'denuncia.json');
}

/**
 * 
 */
function complaint_save_meta($post_id)
{
  schema_save_meta($post_id, 'complaint_meta_box_nonce', 'denuncia.json');
}
add_action('save_post', 'complaint_save_meta');
