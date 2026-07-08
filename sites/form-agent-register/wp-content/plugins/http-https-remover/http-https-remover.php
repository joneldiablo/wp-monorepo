<?php
/**
 * Plugin Name: HTTP / HTTPS Remover
 * Plugin URI: https://de.wordpress.org/plugins/http-https-remover/
 * Description: This Plugin creates protocol relative urls by removing http + https from links.
 * Version: 2.2
 * Author: CONDACORE
 * Author URI: https://condacore.com/
 * License: GPLv3
 */

if (!defined('ABSPATH'))
	exit;

class HTTP_HTTPS_REMOVER
{

	public function __construct()
	{

		add_action('wp_loaded', array(
			$this,
			'letsGo'
		), 99, 1);

		/* Plugin Activation Hook */
		register_activation_hook(__FILE__,   array($this, 'jr_default_activation_hook'));
		/* Add admin notice */
		add_action('admin_notices', array($this, 'jr_activation_notice_hook'));
		/* Adding links filter */
		add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'jr_add_action_links'));

		/* Remove Trans */

		add_action('wp_ajax_nopriv_jr_remove_trans', array($this, 'jr_remove_set_transient'));
		add_action('wp_ajax_jr_remove_trans', array($this, 'jr_remove_set_transient'));
	}

	public function jr_remove_set_transient()
	{
		delete_transient('jr-wp-admin-notice');
		echo "Transient Deleted!";
		exit;
	}

	/**
	 * Create transient data
	 */

	public function jr_default_activation_hook()
	{
		set_transient('jr-wp-admin-notice', true, 0);
	}

	/**
	 * Adding Links
	 */

	function jr_add_action_links($links)
	{
		$mylinks = array(
			'<a target="_blank" style="color:green;" href="https://sellcodes.com/s/N3bDHV
">Get share count recovery</a>',
		);
		return array_merge($links, $mylinks);
	}

	/**
	 * Admin Notice on Activation.
	 */
	public function jr_activation_notice_hook()
	{

		/* Check transient */
		if (get_transient('jr-wp-admin-notice')) {
			?>
		<div class="updated notice is-dismissible http_custom_class">
			<p>
				Plugin "HTTP / HTTPS Remover: SSL Mixed Content Fix” has been successfully installed and activated – hurray!
			</p>
			<p>
				<strong>Next:</strong> if you don‘t want to lose traffic to your site we strongly recommend to get the <a style="color:green;" target="_blank" href="https://sellcodes.com/s/N3bDHV">Ultimate Social Media plugin</a> which has a share count recovery feature (so that you don‘t lose any share counts after your switch to https) besides many other cool features.
			</p>
			<div class="notice_links" style="padding: 10px 0;">
				<div style="width: 33.333%;float:left;text-align: left;">
					<!--<a style="color:green;" href="#">Get plugin now</a>-->

					<script type="text/javascript" src="https://sellcodes.com/quick_purchase/XdHlrQnc/embed.js"></script>
					<div class="sellcodes-quick-purchase"><a style="color: green;font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Oxygen-Sans,Ubuntu,Cantarell, 'Helvetica Neue',sans-serif;font-size: 14px !important;text-decoration: underline !important;" class="sc-button" data-product-id="XdHlrQnc" data-option-id="4HiwuC6Y" data-referral="N3bDHV">Get plugin now</a></div>

				</div>
				<div style="width: 33.333%;float:left;text-align: left;">
					<a style="color:green;" target="_blank" href="https://sellcodes.com/s/N3bDHV">See all plugin features </a>
				</div>
				<div style="width: 33.333%;float:left;text-align: left;">
					<a id="remove_trans_anchor" style="color:#999;" href="#">
						I‘m fine with getting less shares & traffic
					</a>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
		<style>
			.http_custom_class .notice-dismiss {
				display: none;
			}
		</style>

		<script>
			var ajax_request_url = '<?php echo admin_url('admin-ajax.php'); ?>';
			jQuery(document).ready(function($) {
				$("#remove_trans_anchor").on("click", function(e) {
					e.preventDefault();
					$.ajax({
						url: ajax_request_url,
						type: "POST",
						data: "action=jr_remove_trans",
						success: function(response) {
							jQuery('.http_custom_class .notice-dismiss').trigger('click');
						}
					});
				});
			})
		</script>
	<?php
	//delete_transient( 'jr-wp-admin-notice' );
}
}




public function letsGo()
{
	global $pagenow;
	ob_start(array(
		$this,
		'mainPath'
	));
}

public function mainPath($buffer)
{
	$content_type = NULL;
	foreach (headers_list() as $header) {
		if (strpos(strtolower($header), 'content-type:') === 0) {
			$pieces       = explode(':', strtolower($header));
			$content_type = trim($pieces[1]);
			break;
		}
	}
	if (is_null($content_type) || substr($content_type, 0, 9) === 'text/html') {

		$buffer = preg_replace('/https?:/i', '', $buffer);
	}
	return $buffer;
}
}
$http_https_remover = new HTTP_HTTPS_REMOVER();

function wp_upe_upgrade_completed($upgrader_object, $options)
{
	// The path to our plugin's main file
	$our_plugin = plugin_basename(__FILE__);
	// If an update has taken place and the updated type is plugins and the plugins element exists
	if ($options['action'] == 'update' && $options['type'] == 'plugin' && isset($options['plugins'])) {
		// Iterate through the plugins being updated and check if ours is there
		foreach ($options['plugins'] as $plugin) {
			if ($plugin == $our_plugin) {
				// Set a transient to record that our plugin has just been updated
				jr_default_activation_hook();
			}
		}
	}
}
add_action('upgrader_process_complete', 'wp_upe_upgrade_completed', 10, 2);
