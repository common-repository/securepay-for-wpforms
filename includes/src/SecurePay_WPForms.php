<?php
/**
 * SecurePay for WPForms.
 *
 * @author  SecurePay Sdn Bhd
 * @license GPL-2.0+
 *
 * @see    https://securepay.net
 */
\defined('ABSPATH') || exit;

final class SecurePay_WPForms
{
    private static function register_locale()
    {
        add_action(
            'plugins_loaded',
            function () {
                load_plugin_textdomain(
                    'securepaywpforms',
                    false,
                    SECUREPAY_WPFORMS_PATH.'languages/'
                );
            },
            0
        );
    }

    public static function register_admin_hooks()
    {
        add_action(
            'plugins_loaded',
            function () {
                if (current_user_can(apply_filters('capability', 'manage_options'))) {
                    add_action('all_admin_notices', [__CLASS__, 'callback_compatibility'], \PHP_INT_MAX);
                }
            }
        );
    }

    public static function register_addon_hooks()
    {
        add_filter('wpforms_entry_details_payment_gateway', function ($gateway, $entry_meta) {
            if (!empty($entry_meta['payment_type']) && 'securepay_payment' === $entry_meta['payment_type']) {
                $gateway = 'SecurePay';
            }

            return $gateway;
        }, 10, 2);

        add_filter('wpforms_entry_details_payment_transaction', function ($transaction, $entry_meta) {
            if (!empty($entry_meta['payment_type']) && 'securepay_payment' === $entry_meta['payment_type'] && !empty($entry_meta['payment_transaction'])) {
                $transaction = $entry_meta['payment_transaction'];
            }

            return $transaction;
        }, 10, 2);

        add_action('wpforms_loaded', function () {
            require_once SECUREPAY_WPFORMS_PATH.'/includes/src/WPForms_SecurePay_Payment.php';
            new WPForms_SecurePay_Payment();
        });
    }

    private static function is_wpforms_activated()
    {
        return class_exists('WPForms_Payment', false);
    }

    private static function register_autoupdates()
    {
        add_filter(
            'auto_update_plugin',
            function ($update, $item) {
                if (SECUREPAY_WPFORMS_SLUG === $item->slug) {
                    return !\defined('SECUREPAY_WPFORMS_AUTOUPDATE_DISABLED') || !SECUREPAY_WPFORMS_AUTOUPDATE_DISABLED ? true : false;
                }

                return $update;
            },
            \PHP_INT_MAX,
            2
        );
    }

    public static function callback_compatibility()
    {
        if (!self::is_wpforms_activated()) {
            $html = '<div id="securepay-notice" class="notice notice-error is-dismissible">';
            $html .= '<p>'.esc_html__('SecurePay require WPForms plugin. Please install and activate.', 'securepay').'</p>';
            $html .= '</div>';
            echo wp_kses_post($html);
        }
    }

    public static function activate()
    {
        return true;
    }

    public static function deactivate()
    {
        return true;
    }

    public static function uninstall()
    {
        return true;
    }

    public static function register_plugin_hooks()
    {
        register_activation_hook(SECUREPAY_WPFORMS_HOOK, [__CLASS__, 'activate']);
        register_deactivation_hook(SECUREPAY_WPFORMS_HOOK, [__CLASS__, 'deactivate']);
        register_uninstall_hook(SECUREPAY_WPFORMS_HOOK, [__CLASS__, 'uninstall']);
    }

    public static function attach()
    {
        self::register_locale();
        self::register_admin_hooks();
        self::register_addon_hooks();
        self::register_autoupdates();
    }
}
