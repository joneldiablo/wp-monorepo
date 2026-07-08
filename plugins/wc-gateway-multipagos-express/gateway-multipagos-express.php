<?php

/**
 * Plugin Name: WooCommerce Multipagos Express Gateway
 * Plugin URI: https://github.com/joneldiablo/wc-gateway-multipagos
 * Description: Método de pago con Multipagos Express
 * Author: joneldiablo
 * Author URI: https://github.com/joneldiablo
 * Version: 1.1.2
 * Text Domain: wc-gateway-multipagos
 * Domain Path: /i18n/languages/
 *
 *
 * @package   WC-Gateway-Multipagos
 * @author    joneldiablo
 * @category  Admin
 *
 */

defined('ABSPATH') or exit;


// Make sure WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
  return;
}


/**
 * Add the gateway to WC Available Gateways
 * 
 * @since 1.0.0
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + multipagos gateway
 */
function wc_multipagos_add_to_gateways($gateways)
{
  $gateways[] = 'WC_Gateway_Multipagos';
  return $gateways;
}
add_filter('woocommerce_payment_gateways', 'wc_multipagos_add_to_gateways');


/**
 * Adds plugin page links
 * 
 * @since 1.0.0
 * @param array $links all plugin links
 * @return array $links all plugin links + our custom links (i.e., "Settings")
 */
function wc_multipagos_gateway_plugin_links($links)
{

  $plugin_links = array(
    '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=multipagos_gateway') . '">' . __('Settings') . '</a>'
  );

  return array_merge($plugin_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wc_multipagos_gateway_plugin_links');


/**
 *
 * @class 		WC_Gateway_Multipagos
 * @extends		WC_Payment_Gateway
 * @version		1.0.0
 * @package		WooCommerce/Classes/Payment
 * @author 		joneldiablo
 */
function wc_multipagos_gateway_init()
{

  class WC_Gateway_Multipagos extends WC_Payment_Gateway
  {

    /**
     * Constructor for the gateway.
     */
    public function __construct()
    {
      $description = __('Permite el pago con Multipagos Express.', 'wc-gateway-multipagos');
      $description .= '<div>
        Coloca la siguiente dirección en el campo <i>URL retorno</i>: <b>' .
        get_site_url() .
        '/gme-callback</b><div><img src="' . plugins_url('assets/images/config.png', __FILE__) . '" style="width:100%; max-width:400px" /></div>
        </div>';
      $this->id                 = 'multipagos_gateway';
      $this->icon               = plugins_url('assets/images/mexpress.jpg', __FILE__);
      $this->has_fields         = false;
      $this->method_title       = __('Multipagos Express', 'wc-gateway-multipagos');
      $this->method_description = $description;

      // Load the settings.
      $this->init_form_fields();
      $this->init_settings();

      // Define user set variables
      $this->title = $this->get_option('title');
      $this->description = $this->get_option('description');
      $this->action = $this->get_option('action');
      $this->prefix = $this->get_option('prefix');
      $this->idexpress = $this->get_option('idexpress');
      $this->urlretorno = $this->get_option('urlretorno');

      // Actions
      // This action hook saves the settings
      add_action(
        'woocommerce_update_options_payment_gateways_' . $this->id,
        array($this, 'process_admin_options')
      );
      // We need custom JavaScript
      add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
    }

    /**
     * @overwrite
     */

    public function get_option($key, $empty_value = null)
    {
      if ($key !== 'codeBBVA')
        return parent::get_option($key, $empty_value);
      $str = htmlspecialchars_decode(parent::get_option($key, $empty_value));
      $str = preg_replace('/\\+/', '', $str);
      return $str;
    }

    /**
     * Initialize Gateway Settings Form Fields
     */
    public function init_form_fields()
    {

      $this->form_fields = apply_filters('wc_multipagos_form_fields', array(

        'enabled' => array(
          'label'   => __('Enable Multipagos Express', 'wc-gateway-multipagos'),
          'title'   => __('Enable/Disable', 'wc-gateway-multipagos'),
          'type'    => 'checkbox',
          'default' => 'yes'
        ),
        'title' => array(
          'title'       => __('Title'),
          'type'        => 'text',
          'default'     => 'Multipagos Express'
        ),
        'description' => array(
          'title'       => __('Description'),
          'type'        => 'textarea',
          'default'     => 'Paga con Tarjeta de débito o crédito, de forma fácil y segura, solo continua con tu proceso de compra haciendo click en <i>Realizar pedido</i>'
        ),
        'prefix' => array(
          'title'       => __('Prefijo'),
          'description' => __('Prefijo para la referencia del movimiento bancario, la construcción será "prefijo" + "número de orden". ej. WC1003'),
          'type'        => 'text',
          'default'     => 'WC',
          'maxlength'   => 2
        ),
        'codeBBVA' => array(
          'title'       => __('Código de botón BBVA', 'wc-gateway-multipagos'),
          'type'        => 'textarea',
          'description' => __('Copia y pega el código generado en la plataforma de Multipagos Express, los campos "importe" y "referencia" serán ignorados y se utilizará información generada por el sistema de Pedidos', 'wc-gateway-multipagos'),
          'default'     => '<form action="https://www.adquiramexico.com.mx:443/mExpress/pago/avanzado" method="post"/>
            <input type="hidden" name="importe" value="*"/>
            <input type="hidden" name="referencia" value="*"/>
            <input type="hidden" name="urlretorno" value="*"/>
            <input type="hidden" name="idexpress" value="0000"/>
            <input type="hidden" name="financiamiento" value="0"/>
            <input type="hidden" name="plazos" value=""/>
            <input type="hidden" name="mediospago" value="100000"/>
            <input type="image" src="https://dicff9jl33o1o.cloudfront.net/verticales/bexpress/resources/img/icon/paybutton_4.png" />
          </form>',
          'desc_tip'    => false,
          'sanitize_callback' => function ($value) {
            $value = str_replace('\\', '', $value);
            return htmlspecialchars($value);
          }
        ),
        'action' => array(
          'title'       => __('Servicio BBVA'),
          'type'        => 'text',
          'default'     => 'https://www.adquiramexico.com.mx:443/mExpress/pago/avanzado',
          'disabled'    => true,
          'sanitize_callback' => function ($value) {
            $code = $this->get_option('codeBBVA');
            $m = [];
            preg_match('/action="(.+)" method/', $code, $m);
            return $m[1];
          }
        ),
        'idexpress' => array(
          'title'       => __('ID Multipagos Express'),
          'type'        => 'text',
          'default'     => '',
          'disabled'    => true,
          'sanitize_callback' => function ($value) {
            $code = $this->get_option('codeBBVA');
            $m = [];
            preg_match('/name="idexpress" value="(.+)"/', $code, $m);
            return $m[1];
          }
        ),
        'urlretorno' => array(
          'title'       => __('URL de retorno'),
          'type'        => 'text',
          'default'     => '',
          'disabled'    => true,
          'sanitize_callback' => function ($value) {
            $code = $this->get_option('codeBBVA');
            $m = [];
            preg_match('/name="urlretorno" value="(.+)"/', $code, $m);
            return $m[1];
          }
        )
      ));
    }

    /**
     * Scripts
     */
    public function payment_scripts()
    {
    }

    /**
     * Process the payment and return the result
     *
     * @param int $order_id
     * @return array
     */
    public function process_payment($order_id)
    {

      $order = wc_get_order($order_id);

      // Mark as pending (we're awaiting the payment)
      $order->update_status('pending', __('Awaiting Multipagos payment', 'wc-gateway-multipagos'));

      // Remove cart
      WC()->cart->empty_cart();

      $params = 'action=' . $this->action;
      $params .= '&prefix=' . $this->prefix;
      $params .= '&idexpress=' . $this->idexpress;
      $params .= '&return=' . $this->urlretorno;
      $params .= '&total=' . $order->get_total();
      $params .= '&order_id=' . $order_id;
      $redirect = get_home_url() . '/gme-process?' . $params;
      return array(
        'result'   => 'success',
        'redirect'  => $redirect
      );
    }
  }
}
add_action('plugins_loaded', 'wc_multipagos_gateway_init', 11);

function gme_template_redirect()
{
  global $wp;
  $request_uri = str_replace(get_home_url(), '', home_url($wp->request));
  switch ($request_uri) {
    case '/gme-process': {
        include 'process-payment.php';
        break;
      }
    case '/gme-callback': {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        // Get an instance of the WC_Payment_Gateways object to get order object
        $payment_gateways   = WC_Payment_Gateways::instance();
        $payment_gateway    = $payment_gateways->payment_gateways()['multipagos_gateway'];
        $order_id = str_replace($payment_gateway->prefix, '', $_POST['referencia']);
        $order = wc_get_order($order_id);

        // process payment response
        $fields = [
          'codigo',
          'mensaje',
          'autorizacion',
          'referencia',
          'importe',
          'mediopago',
          'financiado',
          'plazos',
          's_transm',
          'hash',
          'signature',
          'tarjetahabiente',
          'cveTipoPago'
        ];
        $response = [];
        $note = "<div>\n";
        $note .= '<img src="' . plugins_url('assets/images/mexpress.jpg', __FILE__) . '" style="max-width:100%" />';
        foreach ($fields as $field) {
          $val = isset($_POST[$field]) ? $_POST[$field] : '';
          $response[$field] = $val;
          $style = '""';
          if (in_array($field, ['hash', 'signature']))
            $style = '"overflow-wrap: break-word"';
          $line = "\t<p>$field: <b style=$style>$val</b></p>\n";
          if ($field === 's_transm')
            $line .= "\t<p>$field (folio): <b style=$style>$val</b></p>\n";
          $note .= $line;
        }
        $note .= '</div>';

        $status = in_array($response['codigo'], ['0', '3'], true) ?
          'processing' : 'failed';
        $order->update_status($status, $note);
        if ($status === 'processing') wc_reduce_stock_levels($order_id);
        header('Location: ' . $payment_gateway->get_return_url($order));
        exit();
        break;
      }
    default:
      return;
  }
}
add_filter('template_redirect', 'gme_template_redirect');
