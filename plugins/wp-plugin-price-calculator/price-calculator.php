<?php

/**
 * Plugin Name: Price Calculator Plugin
 * Plugin URI: https://github.com/joneldiablo/wp-plugin-price-calculator
 * Description: Suggest a price to the vendor with the wcmp commisions
 * Version: 1.1.2
 * Author: joneldiablo
 * Author URI: https://github.com/joneldiablo
 * License: GPL2
 */
$jdpc_pluginRest = 'price-calculator/v1';

function jdpc_admin_enqueue()
{
  global $pagenow;
  global $jdpc_pluginRest;
  $dashboard = preg_match(
    '/(dashboard|escritorio)\/(edit-product|add-product)/',
    $_SERVER['REQUEST_URI']
  );

  $admin = in_array($pagenow, ['post-new.php', 'post.php']);
  if (!($admin || $dashboard)) return;
  wp_enqueue_style('jdpc-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
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
    'jdpc',
    plugin_dir_url(__FILE__) . 'assets/js/index.bundle.min.js',
    ['react-dom', 'jquery'],
    false,
    1
  );
  wp_localize_script('jdpc', 'jdpcApiSettings', array(
    'root' => esc_url_raw(rest_url()),
    'nonce' => wp_create_nonce('wp_rest'),
    'versionString' => $jdpc_pluginRest . '/'
  ));
  wp_enqueue_script('jdpc');
}

// api
function get_fees($request)
{

  $commission_default = get_option('wcmp_payment_settings_name')['default_commission'];
  $commission_vendor = get_user_meta(get_current_vendor_id(), '_vendor_commission', true);
  $commission = is_numeric($commission_vendor) ? $commission_vendor : $commission_default;

  return [
    [
      'label' => 'Comisión Hermano.mx',
      'type' => 'percentage',
      'value' => floatval($commission)
    ]
  ];
}

function jdpc_rest_api()
{
  global $jdpc_pluginRest;

  register_rest_route($jdpc_pluginRest, 'fees', array(
    'methods' => 'GET',
    'callback' => 'get_fees',
    'permission_callback' => function () {
      return current_user_can('edit_posts');
    }
  ));
}

add_action('admin_enqueue_scripts', 'jdpc_admin_enqueue', 1000);
add_action('wcmp_frontend_enqueue_scripts', 'jdpc_admin_enqueue', 1000);
add_action('rest_api_init', 'jdpc_rest_api');
