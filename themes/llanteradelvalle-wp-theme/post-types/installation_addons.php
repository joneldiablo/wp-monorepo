<?php

/**
 * Registers the `installation_addons` post type.
 */
function installation_addons_init()
{
	register_post_type('installation_addons', array(
		'labels'                => array(
			'name'                  => __('Costos de instalación', 'llanteradelvalle'),
			'singular_name'         => __('Costo de instalación', 'llanteradelvalle'),
			'all_items'             => __('Costos de Instalación', 'llanteradelvalle'),
			'archives'              => __('Installation addons Archives', 'llanteradelvalle'),
			'attributes'            => __('Installation addons Attributes', 'llanteradelvalle'),
			'insert_into_item'      => __('Insert into installation addons', 'llanteradelvalle'),
			'uploaded_to_this_item' => __('Uploaded to this installation addons', 'llanteradelvalle'),
			'featured_image'        => _x('Featured Image', 'installation_addons', 'llanteradelvalle'),
			'set_featured_image'    => _x('Set featured image', 'installation_addons', 'llanteradelvalle'),
			'remove_featured_image' => _x('Remove featured image', 'installation_addons', 'llanteradelvalle'),
			'use_featured_image'    => _x('Use as featured image', 'installation_addons', 'llanteradelvalle'),
			'filter_items_list'     => __('Filter installation addons list', 'llanteradelvalle'),
			'items_list_navigation' => __('Installation addons list navigation', 'llanteradelvalle'),
			'items_list'            => __('Installation addons list', 'llanteradelvalle'),
			'new_item'              => __('New Installation addons', 'llanteradelvalle'),
			'add_new'               => __('Agregar nuevo', 'llanteradelvalle'),
			'add_new_item'          => __('Agregar nuevo Costo de Inst.', 'llanteradelvalle'),
			'edit_item'             => __('Editar Costo de Instalación', 'llanteradelvalle'),
			'view_item'             => __('View Installation addons', 'llanteradelvalle'),
			'view_items'            => __('View Installation addons', 'llanteradelvalle'),
			'search_items'          => __('Search installation addons', 'llanteradelvalle'),
			'not_found'             => __('No installation addons found', 'llanteradelvalle'),
			'not_found_in_trash'    => __('No installation addons found in trash', 'llanteradelvalle'),
			'parent_item_colon'     => __('Parent Installation addons:', 'llanteradelvalle'),
			'menu_name'             => __('Installation addons', 'llanteradelvalle'),
		),
		'public'                => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		'show_in_menu'          => 'edit.php?post_type=product',
		'supports'              => array('title', 'thumbnail'),
		'has_archive'           => true,
		'rewrite'               => true,
		'query_var'             => true,
		'menu_position'         => null,
		'menu_icon'             => 'dashicons-admin-post',
		'show_in_rest'          => true,
		'rest_base'             => 'installation_addons',
		'rest_controller_class' => 'WP_REST_Posts_Controller'
	));
}
add_action('init', 'installation_addons_init');

/**
 * Sets the post updated messages for the `installation_addons` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `installation_addons` post type.
 */
function installation_addons_updated_messages($messages)
{
	global $post;

	$permalink = get_permalink($post);

	$messages['installation_addons'] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf(__('Installation addons updated. <a target="_blank" href="%s">View installation addons</a>', 'llanteradelvalle'), esc_url($permalink)),
		2  => __('Custom field updated.', 'llanteradelvalle'),
		3  => __('Custom field deleted.', 'llanteradelvalle'),
		4  => __('Installation addons updated.', 'llanteradelvalle'),
		/* translators: %s: date and time of the revision */
		5  => isset($_GET['revision']) ? sprintf(__('Installation addons restored to revision from %s', 'llanteradelvalle'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
		/* translators: %s: post permalink */
		6  => sprintf(__('Installation addons published. <a href="%s">View installation addons</a>', 'llanteradelvalle'), esc_url($permalink)),
		7  => __('Installation addons saved.', 'llanteradelvalle'),
		/* translators: %s: post permalink */
		8  => sprintf(__('Installation addons submitted. <a target="_blank" href="%s">Preview installation addons</a>', 'llanteradelvalle'), esc_url(add_query_arg('preview', 'true', $permalink))),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf(
			__('Installation addons scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview installation addons</a>', 'llanteradelvalle'),
			date_i18n(__('M j, Y @ G:i', 'llanteradelvalle'), strtotime($post->post_date)),
			esc_url($permalink)
		),
		/* translators: %s: post permalink */
		10 => sprintf(__('Installation addons draft updated. <a target="_blank" href="%s">Preview installation addons</a>', 'llanteradelvalle'), esc_url(add_query_arg('preview', 'true', $permalink))),
	);

	return $messages;
}
add_filter('post_updated_messages', 'installation_addons_updated_messages');

/**
 *
 */
function installation_addons_meta_box()
{
	add_meta_box('register-fields', __('Detalles de instalación'), function ($post) {
		$fields = [
			'pricePerUnit' => '',
			'shippingRegion' => '',
			'installationType' => '',
			'address' => '',
			'price' => ''
		];
		$path = get_stylesheet_directory();
		$form = file_get_contents($path . '/components/build/installation-addons.html');
		foreach ($fields as $id => $field) {
			$fields[$id] = get_post_meta($post->ID, $id, true);
			$form = str_replace('$' . $id, $fields[$id], $form);
		}
		echo $form;
	}, 'installation_addons');
}
add_action('add_meta_boxes', 'installation_addons_meta_box');

/**
 *
 */
function installation_addons_save_meta($post_id)
{
	$fields = [
		'pricePerUnit',
		'shippingRegion',
		'installationType',
		'address',
		'price'
	];
	foreach ($fields as $id) {
		$fields[$id] = get_post_meta($post_id, $id, true);
		update_post_meta($post_id, $id, $_POST[$id]);
	}
}
add_action('save_post', 'installation_addons_save_meta');


/**
 * remove yoast
 */
function llv_rm_seo_meta_box()
{
	remove_meta_box('wpseo_meta', 'installation_addons', 'normal');
}
add_action('add_meta_boxes', 'llv_rm_seo_meta_box', 100);
