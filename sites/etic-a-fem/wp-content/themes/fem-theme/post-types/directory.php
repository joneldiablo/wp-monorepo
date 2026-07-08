<?php

/**
 * Registers the `directory` post type.
 */
function directory_init()
{
  register_post_type('directory', array(
    'labels'                => array(
      'name'                  => __('Directorio', 'fem'),
      'singular_name'         => __('Directorio', 'fem'),
      'all_items'             => __('Todo en el directorio', 'fem'),
      'archives'              => __('Directory Archives', 'fem'),
      'attributes'            => __('Directory Attributes', 'fem'),
      'insert_into_item'      => __('Insert into directory', 'fem'),
      'uploaded_to_this_item' => __('Uploaded to this directory', 'fem'),
      'featured_image'        => _x('Featured Image', 'directory', 'fem'),
      'set_featured_image'    => _x('Set featured image', 'directory', 'fem'),
      'remove_featured_image' => _x('Remove featured image', 'directory', 'fem'),
      'use_featured_image'    => _x('Use as featured image', 'directory', 'fem'),
      'filter_items_list'     => __('Filter directories list', 'fem'),
      'items_list_navigation' => __('Directories list navigation', 'fem'),
      'items_list'            => __('Directories list', 'fem'),
      'new_item'              => __('Nuevo contacto en directorio', 'fem'),
      'add_new'               => __('Añadir nuevo', 'fem'),
      'add_new_item'          => __('Agregar nuevo contacto en directorio', 'fem'),
      'edit_item'             => __('Editar Directorio', 'fem'),
      'view_item'             => __('View Directory', 'fem'),
      'view_items'            => __('View Directories', 'fem'),
      'search_items'          => __('Search directories', 'fem'),
      'not_found'             => __('No directories found', 'fem'),
      'not_found_in_trash'    => __('No directories found in trash', 'fem'),
      'parent_item_colon'     => __('Parent Directory:', 'fem'),
      'menu_name'             => __('Directorio de Servicios e Inst.', 'fem'),
    ),
    'public'                => true,
    'hierarchical'          => false,
    'show_ui'               => true,
    'show_in_nav_menus'     => true,
    'supports'              => array(
      'title', 'editor', 'page-attributes', 'custom-fields', 'post-formats'
    ),
    'has_archive'           => true,
    'rewrite'               => array('slug' => 'directorio'),
    'query_var'             => true,
    'menu_position'         => 36,
    'menu_icon'             => 'dashicons-book',
    'show_in_rest'          => true,
    'rest_base'             => 'directory',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
    'description' => 'Haz la búsqueda de los distintos hospitales del IMSS, ISSSTE, Institutos y Asociaciones que apoyan la causa.'
  ));
}
add_action('init', 'directory_init');

/**
 * Sets the post updated messages for the `directory` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `directory` post type.
 */
function directory_updated_messages($messages)
{
  global $post;

  $permalink = get_permalink($post);

  $messages['directory'] = array(
    0  => '', // Unused. Messages start at index 1.
    /* translators: %s: post permalink */
    1  => sprintf(__('Directory updated. <a target="_blank" href="%s">View directory</a>', 'fem'), esc_url($permalink)),
    2  => __('Custom field updated.', 'fem'),
    3  => __('Custom field deleted.', 'fem'),
    4  => __('Directory updated.', 'fem'),
    /* translators: %s: date and time of the revision */
    5  => isset($_GET['revision']) ? sprintf(__('Directory restored to revision from %s', 'fem'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
    /* translators: %s: post permalink */
    6  => sprintf(__('Directory published. <a href="%s">View directory</a>', 'fem'), esc_url($permalink)),
    7  => __('Directory saved.', 'fem'),
    /* translators: %s: post permalink */
    8  => sprintf(__('Directory submitted. <a target="_blank" href="%s">Preview directory</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
    /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
    9  => sprintf(
      __('Directory scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview directory</a>', 'fem'),
      date_i18n(__('M j, Y @ G:i', 'fem'), strtotime($post->post_date)),
      esc_url($permalink)
    ),
    /* translators: %s: post permalink */
    10 => sprintf(__('Directory draft updated. <a target="_blank" href="%s">Preview directory</a>', 'fem'), esc_url(add_query_arg('preview', 'true', $permalink))),
  );

  return $messages;
}
add_filter('post_updated_messages', 'directory_updated_messages');


function directory_pagesize($query)
{

  if ($query->get('post_type') === 'directory') {
    $query->set('posts_per_page', 100);
    if (($_GET['search'])) {
      $query->set('s', $_GET['search']);
    }
    if (($_GET['type'])) {
      $query->set('meta_query', [['key' => 'type', 'value' => $_GET['type']]]);
    }
    return $query;
  }
}
add_action('pre_get_posts', 'directory_pagesize', 1);

//include get_stylesheet_directory() . "/template/instituciones.php";
