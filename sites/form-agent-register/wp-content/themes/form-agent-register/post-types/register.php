<?php
$baseUrlTheme = dirname(__DIR__) . '/';
include $baseUrlTheme . 'vendor/autoload.php';

function register_init()
{
  register_post_type('register', array(
    'labels'                => array(
      'name'                  => __('Registros', 'nude'),
      'singular_name'         => __('Registros', 'nude'),
      'all_items'             => __('All Registros', 'nude'),
      'archives'              => __('Registros Archives', 'nude'),
      'attributes'            => __('Registros Attributes', 'nude'),
      'insert_into_item'      => __('Insert into Registros', 'nude'),
      'uploaded_to_this_item' => __('Uploaded to this Registros', 'nude'),
      'featured_image'        => _x('Featured Image', 'register', 'nude'),
      'set_featured_image'    => _x('Set featured image', 'register', 'nude'),
      'remove_featured_image' => _x('Remove featured image', 'register', 'nude'),
      'use_featured_image'    => _x('Use as featured image', 'register', 'nude'),
      'filter_items_list'     => __('Filter Registros list', 'nude'),
      'items_list_navigation' => __('Registros list navigation', 'nude'),
      'items_list'            => __('Registros list', 'nude'),
      'new_item'              => __('New Registros', 'nude'),
      'add_new'               => __('Add New', 'nude'),
      'add_new_item'          => __('Add New Registros', 'nude'),
      'edit_item'             => __('Edit Registros', 'nude'),
      'view_item'             => __('View Registros', 'nude'),
      'view_items'            => __('View Registros', 'nude'),
      'search_items'          => __('Search Registros', 'nude'),
      'not_found'             => __('No Registros found', 'nude'),
      'not_found_in_trash'    => __('No Registros found in trash', 'nude'),
      'parent_item_colon'     => __('Parent Registros:', 'nude'),
      'menu_name'             => __('Registros', 'nude'),
    ),
    'public'                => false,
    'hierarchical'          => false,
    'show_ui'               => true,
    'show_in_nav_menus'     => true,
    'supports'              => array(''),
    'has_archive'           => true,
    'rewrite'               => true,
    'query_var'             => true,
    'menu_position'         => null,
    'menu_icon'             => 'dashicons-admin-post',
    'show_in_rest'          => true,
    'rest_base'             => 'registers',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
  ));
}

function register_updated_messages($messages)
{
  global $post;

  $permalink = get_permalink($post);

  $messages['register'] = array(
    0  => '', // Unused. Messages start at index 1.
    /* translators: %s: post permalink */
    1  => sprintf(__('Registros updated. <a target="_blank" href="%s">View Registros</a>', 'nude'), esc_url($permalink)),
    2  => __('Custom field updated.', 'nude'),
    3  => __('Custom field deleted.', 'nude'),
    4  => __('Registros updated.', 'nude'),
    /* translators: %s: date and time of the revision */
    5  => isset($_GET['revision']) ? sprintf(__('Registros restored to revision from %s', 'nude'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
    /* translators: %s: post permalink */
    6  => sprintf(__('Registros published. <a href="%s">View Registros</a>', 'nude'), esc_url($permalink)),
    7  => __('Registros saved.', 'nude'),
    /* translators: %s: post permalink */
    8  => sprintf(__('Registros submitted. <a target="_blank" href="%s">Preview Registros</a>', 'nude'), esc_url(add_query_arg('preview', 'true', $permalink))),
    /* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
    9  => sprintf(
      __('Registros scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Registros</a>', 'nude'),
      date_i18n(__('M j, Y @ G:i', 'nude'), strtotime($post->post_date)),
      esc_url($permalink)
    ),
    /* translators: %s: post permalink */
    10 => sprintf(__('Registros draft updated. <a target="_blank" href="%s">Preview Registros</a>', 'nude'), esc_url(add_query_arg('preview', 'true', $permalink))),
  );

  return $messages;
}

function register_list_columns($columns)
{
  unset($columns['title']);
  unset($columns['date']);
  $columns['slug'] = __('Slug');
  $columns['fullname_name'] = __('Nombre');
  $columns['fullname_firstSurname'] = __('Apellido paterno');
  $columns['fullname_secondSurname'] = __('Apellido materno');
  $columns['contact_email'] = __('Correo electrónico');
  $columns['contact_phoneMobile'] = __('Teléfono celular');

  return $columns;
}

function register_list_columns_content($column, $post_id)
{
  $meta = get_post_meta($post_id, $column, true);
  if ($column === 'slug') {
    $meta = get_post_field('post_name', $post_id);
  }
  if (empty($meta))
    echo __('<em>Sin información</em>');
  else
    printf(__('%s'), $meta);
}

function register_meta_box()
{
  add_meta_box('register-fields', __('Datos de registro'), 'register_meta_box_render', 'register');
}

function register_meta_box_render($post)
{
  global $baseUrlTheme;
  wp_nonce_field('form_register_meta_box_nonce', 'meta_box_nonce');

  $json_data = readSchema('register.json');
  $data = retrieveData($json_data, $post, null);

  $pug = new Pug();
  $pug->displayFile($baseUrlTheme . 'template/pug/form-private.pug', [
    'site' => ['data' => ['schema' => $json_data, 'data' => $data]]
  ]);

  echo
    '<br><p>contrato: <a href="' . wp_get_upload_dir()['baseurl'] . '/contratos/' . get_post_meta($post->ID, 'contrato', true) . '" target="_blank">' .
      get_post_meta($post->ID, 'contrato', true) . '</a></p>';
}

function register_save_meta($post_id)
{
  if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'form_register_meta_box_nonce'))
    return;

  if (!current_user_can('edit_post', $post_id))
    return;

  $json_data = readSchema('register.json');
  $res = saveData($json_data, $post_id);
  if ($res['error']) {
    $error = new WP_Error(400, $res['error']);
  }
}

add_action('init', 'register_init');
add_filter('post_updated_messages', 'register_updated_messages');
add_filter('manage_register_posts_columns', 'register_list_columns');
add_action('manage_register_posts_custom_column', 'register_list_columns_content', 10, 2);
add_action('add_meta_boxes', 'register_meta_box');
add_action('save_post', 'register_save_meta');
