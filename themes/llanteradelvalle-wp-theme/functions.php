<?php

/**
 * Llanteradelvalle Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package llanteradelvalle
 */

/**
 * Enqueue scripts and styles.
 */
function llv_assets()
{
	$theme_url = get_stylesheet_directory_uri();

	wp_enqueue_style('chromium-style', get_template_directory_uri() . '/style.css');
	wp_enqueue_style(
		'llanteradelvalle-style',
		$theme_url . '/style.css',
		array('chromium-style')
	);

	$path = get_stylesheet_directory();
	$files = json_decode(file_get_contents($path . '/components/build/rev-manifest.json'), true);
	$style = $theme_url . '/components/build/' . $files['styles/main.css'];
	$script = $theme_url . '/components/build/' . $files['scripts/main.js'];

	wp_enqueue_style('llv', $style);
	wp_enqueue_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array('jquery', 'magnific-popup'), null, true);
	wp_enqueue_script('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('popper'), null, true);
	wp_enqueue_script('llv', $script, array('bootstrap'), null, true);
	wp_localize_script('llv', 'wp', array('siteurl' => get_option('siteurl')));
}

/**
 * just catalog (not enabled)
 */

function just_catalog()
{
	remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
	remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price');
	remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price');
	remove_action('storefront_header', 'storefront_header_cart', 60);
}

/**
 * add button to summary
 */

function order_form_button_summary()
{
	echo do_shortcode('[order_form_button]');
}

/**
 *
 */
function tire_size_search_shortcode($atts)
{

	$path = get_stylesheet_directory();
	$form = file_get_contents($path . '/components/build/form-size.html');

	return $form;
}

function order_form_button_shortcode($atts)
{

	/* $a = shortcode_atts(array(
		'price' => 0
	), $atts); */
	$path = get_stylesheet_directory();
	$form = file_get_contents($path . '/components/build/order-form-button.html');

	return $form;
}

function llv_add_to_cart($cart_item, $product_id)
{
	$cart_item['delivery'] = $_POST['product_delivery'];
	$cart_item['addon'] = $_POST['addon'];
	if ($_POST['vehicle']['id']) {
		$cart_item['vehicle'] = implode(', ', $_POST['vehicle']);
	}
	if ($_POST['address']['street'] && $_POST['address']['cp']) {
		$cart_item['address'] = implode(', ', $_POST['address']);
	}
	return $cart_item;
}

function limitChars($x, $length)
{
	if (strlen($x) <= $length) {
		return $x;
	} else {
		$y = substr($x, 0, $length) . '...';
		return $y;
	}
}

function llv_get_from_cart($data, $cart_item)
{
	$delivery = get_delivery($cart_item['addon'], [
		'pricePerUnit',
		'shippingRegion',
		'installationType',
		'address',
		'price'
	]);
	$tipe = '';
	$address = $cart_item['address'];
	switch ($delivery['installationType']) {
		case 'workshop':
			$tipe = 'Ins. en taller';
			$address = $delivery['address'];
			break;
		case 'home':
			$tipe = 'Ins. a domicilio';
			break;
		default:
			$tipe = 'Envío';
			break;
	}
	$price = floatval($delivery['price']) +
		(floatval($cart_item['quantity']) * floatval($delivery['pricePerUnit']));

	$data[] = [
		'name' => $tipe,
		'value' => $delivery['title'] . ' <b>($' . number_format($price, 2) . ')</b>' .
			'<br>' . limitChars($address, 100),
		'price' => $price
	];
	return $data;
}

function llv_cart_fees()
{
	$cart_items = WC()->cart->get_cart();
	$price = 0;
	foreach ($cart_items as $cart_item) {
		$names = [
			'pricePerUnit',
			'price'
		];
		$delivery = [];
		foreach ($names as $name) {
			$delivery[$name] =  get_post_meta($cart_item['addon'], $name, true);
		}
		$price += floatval($delivery['price']) +
			(floatval($cart_item['quantity']) * floatval($delivery['pricePerUnit']));
	}
	if ($price) {
		WC()->cart->add_fee('Instalación', $price, false, '');
	}
}

function llv_add_order_item($item_id, $cart_item, $order_id)
{
	$values = $cart_item->legacy_values;
	if (!$values) {
		return;
	}

	$delivery = get_delivery($values['addon'], []);
	$names = ['Vehículo' => 'vehicle', 'Dirección' => 'address'];

	switch ($values['delivery']) {
		case 'workshop':
			$name = 'Ins. en taller';
			break;
		case 'home':
			$name = 'Ins. a domicilio';
			break;
		default:
			$name = 'Envio';
			break;
	}

	add_post_meta($order_id, $name, $delivery['title'] . ' ID=' . $values['addon']);
	foreach ($names as $n => $id) {
		add_post_meta($order_id, $n, $values[$id]);
	}
}

function llv_product_related($wc_get_product_term_ids, $product_id)
{
	$terms = wp_get_post_terms($product_id, 'product_cat');
	/* get_terms(array(
		'include' => $wc_get_product_term_ids,
	)); */
	foreach ($terms as $term) {
		if ($term->parent == 238) {
			return [$term->term_id];
		}
	}
}

// Services

function api_llv_v1()
{
	return rest_ensure_response('Llantera del valle Api v1.0.0');
}

function get_sizes_parents()
{
	$size_cats = [
		'ancho' => [],
		'serie' => [],
		'diametro' => []
	];
	foreach ($size_cats as $key => $category) {
		$size_cats[$key] = get_term_by('slug', $key, 'tire_sizes')->term_id;
	}
	return $size_cats;
}

function get_sizes()
{
	$size_cats = [
		'ancho',
		'serie',
		'diametro'
	];
	$sizes = [];
	for ($i = 0; $i < count($size_cats); $i++) {
		$category = get_term_by('slug', $size_cats[$i], 'tire_sizes');

		$args = array(
			'hierarchical' => 1,
			'hide_empty' => 0,
			'taxonomy' => 'tire_sizes',
			'parent' => $category->term_id
		);
		$sizes[$size_cats[$i]] = get_categories($args);
	}

	return $sizes;
}

function get_delivery($id, $fields = false)
{
	$names = isset($fields) ? $fields : [
		'pricePerUnit',
		'shippingRegion',
		'installationType',
		'address',
		'vehicle',
		'price'
	];
	$delivery = [];
	$addon = get_post($id);
	$delivery['title'] = $addon->post_title;
	foreach ($names as $name) {
		$delivery[$name] =  get_post_meta($id, $name, true);
	}
	return $delivery;
}

function get_deliveries()
{
	$args = array(
		'post_type' => 'installation_addons',
		'post_status' => 'publish',
		'posts_per_page' => -1
	);
	$query = new WP_Query($args);
	$output = [];
	foreach ($query->posts as $post) {
		$fields = [
			'pricePerUnit' => '',
			'shippingRegion' => '',
			'installationType' => '',
			'address' => '',
			'price' => ''
		];
		foreach ($fields as $fieldName => $field) {
			$field = get_post_meta($post->ID, $fieldName, true);
			$fields[$fieldName] = $field;
		}
		$fields['id'] = $post->ID;
		$fields['title'] = $post->post_title;
		$fields['image'] = get_the_post_thumbnail_url($post->ID);
		array_push($output, $fields);
	}
	return $output;
}

function llv_get_products($request)
{
	// get product by post id
	$id = $request['id'];
	if ($id) {
		$product = wc_get_product($id);
		if (!$product) return [];
		return [
			'id' => $product->get_id(),
			'name' => $product->get_name(),
			'price' => $product->get_price()
		];
	} else {
		$args = array(
			'post_status' => 'publish',
			'post_type' => 'product',
			'fields' => ['post_title'],
			'posts_per_page' => -1
		);
		$posts = get_posts($args);
		$return = [];
		foreach ($posts as $post) {
			$metas = get_post_meta($post->ID);
			$metas['_thumbnail_id'] = isset($metas['_thumbnail_id']) ? $metas['_thumbnail_id'][0] : null;
			$metas['_product_image_gallery'] = isset($metas['_product_image_gallery']) ? $metas['_product_image_gallery'][0] : null;
			$metas['_price'] = isset($metas['_price']) ? $metas['_price'][0] : null;
			$return[] = [
				'id' => $post->ID,
				'nombre' => $post->post_title,
				'imagen' => $metas['_thumbnail_id'],
				'galeria' => $metas['_product_image_gallery'],
				'precio' => $metas['_price']
			];
		}
		return $return;
	}
}
function llv_patch_products()
{
}
function llv_post_products()
{
}
function llv_delete_products()
{
}
function llv_post_catalog($req)
{
	// FIX secure function!!! just checking if cookie exist!!!!
	$logged_in = false;
	if (count($_COOKIE)) {
		foreach ($_COOKIE as $key => $val) {
			if (preg_match("/wordpress_logged_in_/", $key)) {
				$logged_in = true;
				break;
			} else {
				$logged_in = false;
			}
		}
	} else {
		$logged_in = false;
	}
	if (!$logged_in) return '401';

	// aumentando el tiempo de ejecución
	ini_set('max_execution_time', 0);
	set_time_limit(0);

	$body = $req->get_body();
	$data = json_decode($body);
	$cats = [
		'medida' => 238,
		'marca' => 146,
		'ic' => 239,
		'rv' => 240,
		'carga' => 242
	];
	$return = [];

	foreach ($data as $product) {
		$pAttrs = array(
			'post_title' => join(' ', [
				$product->marca ? $product->marca : '',
				$product->modelo ? $product->modelo : '',
				$product->medida ? $product->medida : ''
			]),
			'post_type' => 'product',
			'post_status' => 'publish',
			'post_content' => '<p>---Características---</p><p>' . join('</p><p>', [
				$product->marca ? 'Marca: ' . $product->marca : '',
				$product->modelo ? 'Modelo: ' . $product->modelo : '',
				$product->medida ? 'Medida: ' . $product->medida : '',
				$product->carga ? 'Carga: ' . $product->carga : '',
				$product->ic ? 'IC: ' . $product->ic : '',
				$product->rv ? 'RV: ' . $product->rv : '',
			]) . '</p>'
		);
		if (isset($product->id_wp)) {
			$pAttrs['ID'] = intval($product->id_wp);
			wp_update_post($pAttrs);
			$post_id = $product->id_wp;
		} else {
			$post_id = wp_insert_post($pAttrs);
		}
		$wcproduct = wc_get_product($post_id);
		$wcproduct->set_regular_price($product->precio);
		$wcproduct->set_price($product->precio);
		$wcproduct->set_sku($post_id . '_' . $product->codigo);
		$wcproduct->set_stock_status('onbackorder');
		$wcproduct->save();

		$tags = [];
		foreach ($cats as $name => $id) {
			if (!$product->$name) continue;
			$nameCat = strtolower($product->$name);
			if ($name == 'ic' || $name == 'rv') {
				$nameCat = $name . '-' . $nameCat;
			}
			$term_ids = term_exists($nameCat, 'product_cat', $id);
			if (!$term_ids) {
				$term = wp_insert_term($product->$name, 'product_cat', array('parent' => $id, 'slug' => $nameCat));
				if (!is_wp_error($term)) {
					array_push($tags, intval($term['term_id']));
				}
			} else {
				array_push($tags, intval($term_ids['term_id']));
			}
		}
		if ($product->carga) {
			array_push($tags, 163);
		} else {
			array_push($tags, 161);
		}
		wp_set_object_terms($post_id, $tags, 'product_cat', true);

		update_post_meta($post_id, 'proveedor', $product->proveedor);
		$return[] = ['id' => $post_id, 'title' => $pAttrs['post_title']];
	}
	return $return;
}

function llv_get_category_ancho()
{
	$args = array(
		'hide_empty' => true,
		'taxonomy' => 'product_cat',
		'parent' => 238
	);
	$all = get_categories($args);
	$toReturn = [];
	foreach ($all as $cat) {
		array_push($toReturn, $cat->name);
	}
	return $toReturn;
}

function llv_update_all_price()
{
	// FIX secure function!!! just checking if cookie exist!!!!
	$logged_in = false;
	if (count($_COOKIE)) {
		foreach ($_COOKIE as $key => $val) {
			if (preg_match("/wordpress_logged_in_/", $key)) {
				$logged_in = true;
				break;
			} else {
				$logged_in = false;
			}
		}
	} else {
		$logged_in = false;
	}
	if (!$logged_in) return '401';

	// aumentando el tiempo de ejecución
	ini_set('max_execution_time', 0);
	set_time_limit(0);

	$args = array(
		'post_status' => 'publish',
		'post_type' => 'product',
		'fields' => 'ids',
		'posts_per_page' => -1
	);
	$posts = get_posts($args);
	$return = [];
	foreach ($posts as $post) {
		$product = wc_get_product($post);
		$price = $product->get_regular_price();
		$old_price = $price;
		if (isset($_GET['porcentaje'])) {
			$price *= $_GET['porcentaje'];
		}
		if (isset($_GET['extra'])) {
			$price += $_GET['extra'];
		}
		$product->set_regular_price($price);
		$product->set_price($price);
		$product->save();
		$return[] = array(
			'id' => $product->get_id(),
			'title' => $product->get_title(),
			'old_price' => $old_price,
			'price' => $price
		);
	}
	return $return;
}

function llv_update_all_images()
{
	// FIX secure function!!! just checking if cookie exist!!!!
	$logged_in = false;
	if (count($_COOKIE)) {
		foreach ($_COOKIE as $key => $val) {
			if (preg_match("/wordpress_logged_in_/", $key)) {
				$logged_in = true;
				break;
			} else {
				$logged_in = false;
			}
		}
	} else {
		$logged_in = false;
	}
	if (!$logged_in) return '401';

	// aumentando el tiempo de ejecución
	ini_set('max_execution_time', 0);
	set_time_limit(0);

	$include = (isset($_GET['ids']) ? explode(',',  $_GET['ids']) : []);
	$args = array(
		'post_status' => 'publish',
		'post_type' => 'product',
		'fields' => 'ids',
		'include' => $include,
		'posts_per_page' => -1
	);
	$posts = get_posts($args);
	$return = [];
	foreach ($posts as $post) {
		if (isset($_GET['imagen'])) {
			update_post_meta($post, '_thumbnail_id', $_GET['imagen']);
		}
		if (isset($_GET['galeria'])) {
			update_post_meta($post, '_product_image_gallery', $_GET['galeria']);
		}
		if (isset($_GET['imagen']) || isset($_GET['galeria']))
			$return[] = array(
				'id' => $post
			);
	}
	return $return;
}

function llvRoutes()
{
	register_rest_route(
		'llv',
		'v1',
		array(
			'methods' => 'GET',
			'callback' => 'api_llv_v1',
		)
	);
	register_rest_route(
		'llv/v1',
		'sizes',
		array(
			'methods' => 'GET',
			'callback' => 'get_sizes',
		)
	);
	register_rest_route(
		'llv/v1',
		'install_addons',
		array(
			'methods' => 'GET',
			'callback' => 'get_deliveries',
		)
	);
	register_rest_route(
		'llv/v1',
		'products/(?P<id>\d+)',
		array(
			'methods' => 'GET',
			'callback' => 'llv_get_products',
		)
	);
	register_rest_route(
		'llv/v1',
		'products',
		array(
			'methods' => 'GET',
			'callback' => 'llv_get_products',
		)
	);
	register_rest_route(
		'llv/v1',
		'products/(?P<id>\d+)',
		array(
			'methods' => 'PATCH',
			'callback' => 'llv_patch_products',
		)
	);
	register_rest_route(
		'llv/v1',
		'products',
		array(
			'methods' => 'POST',
			'callback' => 'llv_post_products',
		)
	);
	register_rest_route(
		'llv/v1',
		'products/(?P<id>\d+)',
		array(
			'methods' => 'DELETE',
			'callback' => 'llv_delete_products',
		)
	);
	register_rest_route(
		'llv/v1',
		'actualizar-catalogo',
		array(
			'methods' => 'POST',
			'callback' => 'llv_post_catalog',
		)
	);
	register_rest_route(
		'llv/v1',
		'ancho',
		array(
			'methods' => 'GET',
			'callback' => 'llv_get_category_ancho',
		)
	);
	register_rest_route(
		'llv/v1',
		'actualizar-precios',
		array(
			'methods' => 'GET',
			'callback' => 'llv_update_all_price',
		)
	);
	register_rest_route(
		'llv/v1',
		'actualizar-imagenes',
		array(
			'methods' => 'GET',
			'callback' => 'llv_update_all_images',
		)
	);
}

/**
 * post-types
 */
$glob = glob(get_theme_file_path() . '/post-types/*.php');
foreach ($glob as $filename) {
	include $filename;
}

//add_action('init', 'just_catalog');
add_action('wp_enqueue_scripts', 'llv_assets');
add_action('rest_api_init', 'llvRoutes');
add_action('woocommerce_before_add_to_cart_button', 'order_form_button_summary', 9);

add_filter('woocommerce_get_related_product_cat_terms', 'llv_product_related', 10, 2);
add_filter('woocommerce_add_cart_item_data', 'llv_add_to_cart', 10, 3);
add_filter('woocommerce_get_item_data', 'llv_get_from_cart', 10, 2);
add_action('woocommerce_cart_calculate_fees', 'llv_cart_fees');
add_action('woocommerce_new_order_item', 'llv_add_order_item', 10, 4);
//add_filter('woocommerce_email_order_meta_fields', 'llv_emails');
add_shortcode('tire_size_search', 'tire_size_search_shortcode');
add_shortcode('order_form_button', 'order_form_button_shortcode');
