<?php
/**
 * wp-config.php template - credentials from environment
 *
 * @package WordPress
 */

define('DB_NAME', getenv('DB_NAME') ?: 'database');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

define('AUTH_KEY',         getenv('AUTH_KEY') ?: 'put your unique phrase here');
define('SECURE_AUTH_KEY',  getenv('SECURE_AUTH_KEY') ?: 'put your unique phrase here');
define('LOGGED_IN_KEY',    getenv('LOGGED_IN_KEY') ?: 'put your unique phrase here');
define('NONCE_KEY',        getenv('NONCE_KEY') ?: 'put your unique phrase here');
define('AUTH_SALT',        getenv('AUTH_SALT') ?: 'put your unique phrase here');
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT') ?: 'put your unique phrase here');
define('LOGGED_IN_SALT',   getenv('LOGGED_IN_SALT') ?: 'put your unique phrase here');
define('NONCE_SALT',       getenv('NONCE_SALT') ?: 'put your unique phrase here');

$table_prefix = 'wp_';
define('WP_DEBUG', getenv('WP_DEBUG') === 'true');

if (!defined('ABSPATH'))
    define('ABSPATH', dirname(__FILE__) . '/');

require_once(ABSPATH . 'wp-settings.php');
