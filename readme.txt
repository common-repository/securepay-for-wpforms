=== SecurePay For WPForms ===
Contributors: SecurePay
Tags: payment gateway, payment platform, Malaysia, online banking, fpx
Requires at least: 5.4
Tested up to: 6.3
Requires PHP: 7.2
Stable tag: 1.0.6
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SecurePay payment platform plugin for WPForms.

== Description ==

Install this plugin to extends the [WPForms](https://wpforms.com/) plugin to accept payments with the [SecurePay Payment Platform](https://www.securepay.my/?utm_source=wp-plugins-wpforms&utm_campaign=author-uri&utm_medium=wp-dash) for Malaysians.

If you have any questions or suggestions about this plugin, please contact us directly through email at **hello@securepay.my** . Our friendly team will gladly reply as soon as possible.

Other Integrations:

- [SecurePay For WooCommerce](https://wordpress.org/plugins/securepay/)
- [SecurePay For GravityForms](https://wordpress.org/plugins/securepay-for-gravityforms/)
- [SecurePay For WPJobster](https://wordpress.org/plugins/securepay-for-wpjobster/)
- [SecurePay For Restrict Content Pro](https://wordpress.org/plugins/securepay-for-restrictcontentpro)
- [SecurePay For Paid Memberships Pro](https://wordpress.org/plugins/securepay-for-paidmembershipspro)
- [SecurePay For GiveWP](https://wordpress.org/plugins/securepay-for-givewp)

== Installation ==

Make sure that you already have WPForms plugin installed and activated.

- Login to your *WordPress Dashboard*
- Go to **Plugins > Add New**
- Search **SecurePay For WPForms** and click **Install**
- **Activate** the plugin through the 'Plugins' screen in WordPress.

Contact us through email hello@securepay.my if you have any questions or comments about this plugin.


== Changelog ==
= 1.0.6 (27-10-2021) =
- Fixed: banklist_output(), process_entry() -> replace buyer_bank_code esc_attr with sanitize_text_field.
- Fixed: banklist_output -> js securepaybankgivewp function checking.

= 1.0.5 (25-08-2021) =
- Fixed: bank list select script.
- Fixed: handle bank image not exists.

= 1.0.4 (21-08-2021) =
- Fixed: Undefined variable: sandbox
- Fixed: get_url()

= 1.0.3 (19-08-2021) =
- Fixed: Undefined variable: html

= 1.0.2 (09-08-2021) =
- Fixed: securepaycancel, securepaytimeout hash value.
- Added: test mode.

= 1.0.1 (07-08-2021) =
- Initial release.
