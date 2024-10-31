<?php
/**
 * SecurePay for WPForms.
 *
 * @author  SecurePay Sdn Bhd
 * @license GPL-2.0+
 *
 * @see    https://securepay.net
 */

/*
 * @wordpress-plugin
 * Plugin Name:         SecurePay for WPForms
 * Plugin URI:          https://www.securepay.my/?utm_source=wp-plugins-wpforms&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Version:             1.0.6
 * Description:         SecurePay payment platform plugin for WPforms
 * Author:              SecurePay Sdn Bhd
 * Author URI:          https://www.securepay.my/?utm_source=wp-plugins-wpforms&utm_campaign=author-uri&utm_medium=wp-dash
 * Requires at least:   5.4
 * Requires PHP:        7.2
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         securepaywpforms
 * Domain Path:         /languages
 */

if (!\defined('ABSPATH') || \defined('SECUREPAY_WPFORMS_FILE')) {
    exit;
}

\define('SECUREPAY_WPFORMS_VERSION', '1.0.6');
\define('SECUREPAY_WPFORMS_SLUG', 'securepay-for-wpforms');
\define('SECUREPAY_WPFORMS_ENDPOINT_LIVE', 'https://securepay.my/api/v1/');
\define('SECUREPAY_WPFORMS_ENDPOINT_SANDBOX', 'https://sandbox.securepay.my/api/v1/');
\define('SECUREPAY_WPFORMS_ENDPOINT_PUBLIC_LIVE', 'https://securepay.my/api/public/v1/');
\define('SECUREPAY_WPFORMS_ENDPOINT_PUBLIC_SANDBOX', 'https://sandbox.securepay.my/api/public/v1/');
\define('SECUREPAY_WPFORMS_FILE', __FILE__);
\define('SECUREPAY_WPFORMS_HOOK', plugin_basename(SECUREPAY_WPFORMS_FILE));
\define('SECUREPAY_WPFORMS_PATH', realpath(plugin_dir_path(SECUREPAY_WPFORMS_FILE)).'/');
\define('SECUREPAY_WPFORMS_URL', trailingslashit(plugin_dir_url(SECUREPAY_WPFORMS_FILE)));

require __DIR__.'/includes/load.php';
SecurePay_WPForms::attach();
