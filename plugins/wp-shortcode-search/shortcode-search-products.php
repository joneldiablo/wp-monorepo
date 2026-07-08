<?php

/**
 * Plugin Name: Search Products Shortcode
 * Plugin URI: https://github.com/joneldiablo/wp-shortcode-search-products
 * Description: Plugin para buscar y filtrar productos por vendedor y su ubicación. Shortcodes generados son buscador: [dsp] y filtro por zona de envío:[dfc] este último leerá las zonas de envío registradas en woocommerce y las mostrará como opciones.
 * Version: 1.3.1
 * Author: joneldiablo
 * Author URI: https://github.com/joneldiablo
 * License: GPL2
 */
$dsp_pluginRest = 'search-products/v1';

function array_flatten($array)
{
  if (!is_array($array)) {
    return false;
  }
  $result = array();
  foreach ($array as $key => $value) {
    if (is_array($value)) {
      $result = array_merge($result, array_flatten($value));
    } else {
      $result = array_merge($result, array($key => $value));
    }
  }
  return $result;
}

function shortcode_search_products()
{
  global $dsp_pluginRest;
  wp_enqueue_style('dsp-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
  wp_register_script(
    'react',
    'https://unpkg.com/react/umd/react.production.min.js',
    [],
    false,
    1
  );
  wp_register_script(
    'react-dom',
    'https://unpkg.com/react-dom/umd/react-dom.production.min.js',
    ['react'],
    false,
    1
  );
  wp_register_script(
    'dsp',
    plugin_dir_url(__FILE__) . 'assets/js/search.bundle.min.js',
    ['react-dom'],
    false,
    1
  );
  wp_localize_script('dsp', 'dspApiSettings', array(
    'root' => esc_url_raw(rest_url()),
    'versionString' => $dsp_pluginRest . '/',
    'shop' => get_permalink(wc_get_page_id('shop'))
  ));
  wp_enqueue_script('dsp');

  return '<div class="dsp-container"></div>';
}

function shortcode_filter_cities($atts = [])
{
  wp_enqueue_style('dsp-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
  wp_register_script(
    'react',
    'https://unpkg.com/react/umd/react.production.min.js',
    [],
    false,
    1
  );
  wp_register_script(
    'react-dom',
    'https://unpkg.com/react-dom/umd/react-dom.production.min.js',
    ['react'],
    false,
    1
  );
  wp_register_script(
    'dfc',
    plugin_dir_url(__FILE__) . 'assets/js/select.bundle.min.js',
    ['react-dom'],
    false,
    1
  );
  wp_localize_script('dfc', 'dfcApiSettings', array(
    'shop' => get_permalink(wc_get_page_id('shop'))
  ));
  wp_enqueue_script('dfc');

  global $wpdb;
  $select = "SELECT meta_value FROM $wpdb->usermeta where meta_key='shipping_custom'";
  $delivery_zones = $wpdb->get_results($select);
  foreach ($delivery_zones as $i => $e) {
    $delivery_zones[$i] = unserialize($e->meta_value);
  }
  $delivery_zones = ($delivery_zones);
  $delivery_zones = array_flatten($delivery_zones);
  $delivery_zones = array_unique($delivery_zones);
  $options = [];
  $municipios = file_get_contents(__DIR__ . '/municipios.json');
  $municipios = json_decode($municipios, true);
  foreach ($municipios as $obj) {
    if (in_array($obj['id'], $delivery_zones))
      $options[] = (isset($obj['municipio']) ? $obj['municipio'] . ', ' : '') . $obj['estado'] . ':' . $obj['id'];
  }
  $options = join('|', $options);
  return '<div class="dfc-container" data-options="' . $options . '"></div>';
}

function get_vendor_ids_by_shipping_custom($zone)
{
  try {
    global $wpdb;
    $vendorIds = $wpdb->get_results(
      "SELECT DISTINCT user_id,meta_value FROM $wpdb->usermeta WHERE meta_key='shipping_custom' AND (meta_value LIKE '%:\"{$zone}\"%' OR meta_value LIKE '%:\"0\"%')"
    );
    /*  echo sprintf('<pre style="background:black;color:white">
    %s
    <p>=========</p>
    %s
    </pre>', $zone, json_encode($vendorIds, JSON_PRETTY_PRINT)); */
    $toReturn = [];
    foreach ($vendorIds as $id) {
      $toReturn[] = $id->user_id;
    }
  } catch (\Throwable $th) {
    echo '<div class="error" style="background:black; color:white;">' . $th . '</div>';
    return [];
  }
  return $toReturn;
}

function get_products()
{
  $query = $_GET['s'];
  $termsQuery = new WP_Term_Query(array(
    'taxonomy' => 'dc_vendor_shop',
    'search' => $query,
    'hide_empty' => true,
    'fields' => 'ids'
  ));
  $terms = $termsQuery->get_terms();
  $vendors = [];
  $userIds = [];
  foreach ($terms as $id) {
    $vendor = get_wcmp_vendor_by_term($id);
    $vendors[] = $vendor;
    $userIds[] = $vendor->id;
  }
  $userIds = (new WP_User_Query(array(
    'exclude' => $userIds,
    'fields' => 'ids',
    'meta_query' => array(
      'relation' => 'OR',
      array(
        'key' => '_vendor_address_1',
        'value' => htmlentities($query),
        'compare' => 'LIKE'
      ),
      array(
        'key' => '_vendor_address_2',
        'value' => htmlentities($query),
        'compare' => 'LIKE'
      ),
      array(
        'key' => '_vendor_city',
        'value' => htmlentities($query),
        'compare' => 'LIKE'
      ),
      array(
        'key' => '_vendor_state',
        'value' => htmlentities($query),
        'compare' => 'LIKE'
      )
    )
  )))->get_results();
  foreach ($userIds as $id) {
    $vendor = get_wcmp_vendor($id);
    if (!$vendor) continue;
    $vendors[] = $vendor;
  }
  $stores = [];
  foreach ($vendors as $vendor) {
    $store = [
      'id' => $vendor->id,
      'title' => $vendor->get_page_title(),
      'img' => $vendor->get_image(),
      'type' => 'vendor',
      'route' => $vendor->get_permalink(),
      'subtitle' => $vendor->get_formatted_address()
    ];
    array_push($stores, $store);
  }
  $wp_query = new WP_Query(array(
    'post_type' => 'product',
    's' => htmlentities($query),
    'post_status' => 'publish',
    'fields' => 'ids'
  ));
  $products = [];
  foreach ($wp_query->posts as $id) {
    $product = wc_get_product($id);
    $image_id  = $product->get_image_id();
    $image_url = wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail');
    $item = [
      'id' => $id,
      'title' => $product->get_title(),
      'img' => $image_url,
      'type' => 'product',
      'route' =>  get_permalink($id),
      'subtitle' =>  get_woocommerce_currency_symbol() . number_format($product->is_on_sale() ?
        (float)$product->get_sale_price() :
        (float)$product->get_regular_price(), 2)
    ];
    array_push($products, $item);
  };
  $results = array_merge($stores, $products);
  $results = array_slice($results, 0, 10);
  return $results;
}

function dsp_rest_api()
{
  global $dsp_pluginRest;

  register_rest_route($dsp_pluginRest, 'autocomplete', array(
    'methods' => 'GET',
    'callback' => 'get_products'
  ));
}

function dsp_render_vendors()
{
  if ((isset($_GET['vendors']) || isset($_GET['zone'])) && is_shop()) {
    wp_enqueue_style(
      'wcmp_vendor_list',
      WP_PLUGIN_URL . '/dc-woocommerce-multi-vendor/assets/frontend/css/vendor-list.min.css'
    );
    include __DIR__ . '/vendor_list.php';
  }
}

function filter_products_by_zone($query)
{
  if (isset($_GET['zone']) && is_shop()) {
    $vendorIds = get_vendor_ids_by_shipping_custom($_GET['zone']);
    $query->set('author__in', $vendorIds);
  }
}

function dsp_shipping_custom()
{
  $usrid = get_current_user_id();
  if (!is_user_wcmp_vendor($usrid) && is_user_wcmp_vendor($_GET['ID'])) {
    $usrid = $_GET['ID'];
  }
  $shipping_own = false;
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_own = $_POST['shipping_own'] === 'on';
    $shipping_custom = $_POST['shipping_custom'];
    update_user_meta($usrid, 'shipping_own', $shipping_own);
    update_user_meta($usrid, 'shipping_custom', $shipping_custom);
  } else {
    $shipping_own = get_user_meta($usrid, 'shipping_own', true);
    $shipping_custom = get_user_meta($usrid, 'shipping_custom', true);
    if (empty($shipping_custom)) $shipping_custom = [0];
  }
  $municipios = file_get_contents(__DIR__ . '/municipios.json');
  $municipios = json_decode($municipios, true);
  $options = [
    '<option value="0" ' . (!count($shipping_custom) || in_array(0, $shipping_custom) ?
      'selected' : '') . '>Todo México</option>',
    '<option value="null" disabled>==============</option>'
  ];
  $selected = [];
  //$ids = explode(',', $atts['municipios']);
  foreach ($municipios as $obj) {
    $options[] = '<option 
      value="' . $obj['id'] . '" ' . (in_array($obj['id'], $shipping_custom) ? 'selected' : '') . '>'
      . (isset($obj['municipio']) ? $obj['municipio'] . ', ' : '') . $obj['estado'] .
      '</option>';
    if (in_array($obj['id'], $shipping_custom))
      $selected[] = '<span class="badge badge-pill badge-primary">' . (isset($obj['municipio']) ? $obj['municipio'] . ', ' : '') . $obj['estado'] . '</span>';
  }
  echo '<div class="panel panel-default pannel-outer-heading">
    <div class="panel-heading">
      <h3>Envíos</h3>
    </div>
    <div class="panel-body panel-content-padding form-horizontal">
      <div class="wcmp_media_block">
        <div class="form-group">
          <div class="col-xs-12">
            <div class="form-check" style="margin-top:20px">
              <input class="form-check-input" type="checkbox" 
              name="shipping_own" 
              id="shipping_own" ' . ($shipping_own ? '
                checked' : '') . '>
              <label class="form-check-label" for="shipping_own">
                Usar mis propios repartidores
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-3 col-md-3" for="shipping_custom">Entregas en...</label>
          <div class="col-md-6 col-sm-9">
            <select id="shipping_custom" name="shipping_custom[]" class="form-control" multiple>
              ' .  join($options) . '
            </select><br class="sr-only" />
            ' .  join('<br class="sr-only" />', $selected) . '
          </div>
        </div>
      </div>
    </div>
  </div>';
}

add_action('pre_get_posts', 'filter_products_by_zone');
add_shortcode('dsp', 'shortcode_search_products');
add_shortcode('dfc', 'shortcode_filter_cities');
add_action('rest_api_init', 'dsp_rest_api');
add_action('woocommerce_before_shop_loop', 'dsp_render_vendors');
add_action('woocommerce_no_products_found', 'dsp_render_vendors');
add_action('wcmp_after_shop_front', 'dsp_shipping_custom');
add_action('wcmp_vendor_preview_tabs_form_post', 'dsp_shipping_custom');
