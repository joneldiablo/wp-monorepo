<?php

/**
 * Registers the `consultation` post type.
 */
function consultation_init()
{
  register_post_type('consultation', array(
    'labels'                => array(
      'name'                  => __('La consulta Médica', 'fem'),
      'singular_name'         => __('Consulta médica', 'fem'),
      'all_items'             => __('Artículos sobre Consulta Médica', 'fem'),
      'archives'              => __('Consultation Archives', 'fem'),
      'attributes'            => __('Consultation Attributes', 'fem'),
      'insert_into_item'      => __('Insert into consultation', 'fem'),
      'uploaded_to_this_item' => __('Uploaded to this consultation', 'fem'),
      'featured_image'        => _x('Featured Image', 'consultation', 'fem'),
      'set_featured_image'    => _x('Set featured image', 'consultation', 'fem'),
      'remove_featured_image' => _x('Remove featured image', 'consultation', 'fem'),
      'use_featured_image'    => _x('Use as featured image', 'consultation', 'fem'),
      'filter_items_list'     => __('Filter consultations list', 'fem'),
      'items_list_navigation' => __('Consultations list navigation', 'fem'),
      'items_list'            => __('Consultations list', 'fem'),
      'new_item'              => __('New Consultation', 'fem'),
      'add_new'               => __('Añadir nueva', 'fem'),
      'add_new_item'          => __('Add New Consultation', 'fem'),
      'edit_item'             => __('Edit Consultation', 'fem'),
      'view_item'             => __('View Consultation', 'fem'),
      'view_items'            => __('View Consultations', 'fem'),
      'search_items'          => __('Search consultations', 'fem'),
      'not_found'             => __('No consultations found', 'fem'),
      'not_found_in_trash'    => __('No consultations found in trash', 'fem'),
      'parent_item_colon'     => __('Parent Consultation:', 'fem'),
      'menu_name'             => __('La consulta Médica', 'fem'),
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
    'rewrite'               => array('slug' => 'consulta-medica'),
    'query_var'             => true,
    'menu_position'         => 32,
    'menu_icon'             => 'dashicons-pressthis',
    'show_in_rest'          => true,
    'rest_base'             => 'consultations',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
    'description' => 'Conoce las historias de los demás usuarios'
  ));
}
add_action('init', 'consultation_init');

/**
 * Sets the post updated messages for the `consultation` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `consultation` post type.
 */
function consultation_updated_messages($messages)
{
  global $post;

  $permalink = get_permalink($post);

  $messages['consultation'] = array(
    0  => '', // Unused. Messages start at index 1.
    /* translators: %s: post permalink */
    1  => sprintf(__('Consultation updated. <a target="_blank" href="%s">View consultation</a>', 'fem'), esc_url($permalink)),
    2  => __('Custom field updated.', 'fem'),
    3  => __('Custom field deleted.', 'fem'),
    4  => __('Consultation updated.', 'fem'),
    /* translators: %s: date and time of the revision */
    5  => isset($_GET['revision']) ? sprintf(__('Consultation restored to revision from %s', 'fem'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
    /* translators: %s: post permalink */
    6  => sprintf(__('Consultation published. <a href="%s">View consultation</a>', 'fem'), esc_url($permalink)),
    7  => __('Consultation saved.', 'fem'),
    /* translators: %s: post permalink */
    8  => sprintf(__('Consultation submitted. <a target="_blank" href="%s">Preview consultation</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
    /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
    9  => sprintf(
      __('Consultation scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview consultation</a>', 'fem'),
      date_i18n(__('M j, Y @ G:i', 'fem'), strtotime($post->post_date)),
      esc_url($permalink)
    ),
    /* translators: %s: post permalink */
    10 => sprintf(__('Consultation draft updated. <a target="_blank" href="%s">Preview consultation</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
  );

  return $messages;
}
add_filter('post_updated_messages', 'consultation_updated_messages');

/**
 * meta boxes
 */

function consultation_meta_box_add()
{
  global $DojoDigitalHideTitle;
  add_meta_box(
    'dojodigital_toggle_title',
    'Ocultar título',
    array($DojoDigitalHideTitle, 'build_box'),
    'consultation',
    'side'
  );
}
add_action('add_meta_boxes', 'consultation_meta_box_add');
