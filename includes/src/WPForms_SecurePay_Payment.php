<?php
/**
 * SecurePay for WPForms.
 *
 * @author  SecurePay Sdn Bhd
 * @license GPL-2.0+
 *
 * @see    https://securepay.net
 */
class WPForms_SecurePay_Payment extends WPForms_Payment
{
    /**
     * Initialize.
     *
     * @since 1.0.0
     */
    public function init()
    {
        $this->version = SECUREPAY_WPFORMS_VERSION;
        $this->name = 'SecurePay Payment';
        $this->slug = 'securepay_payment';
        $this->priority = 10;
        $this->icon = SECUREPAY_WPFORMS_URL.'includes/admin/addon-icon-securepay.png';

        add_action('wpforms_process_complete', [$this, 'process_entry'], 20, 4);
        add_action('init', [$this, 'process_callback']);

        add_filter(
            'wpforms_frontend_form_data',
            function ($form_data) {
                $form_data['settings']['ajax_submit'] = false;

                return $form_data;
            }
        );

        add_action('wpforms_frontend_css', [$this, 'securepay_scripts'], \PHP_INT_MAX);
        add_action('wpforms_frontend_output', [$this, 'banklist_output']);
    }

    /**
     * Display content inside the panel content area.
     *
     * @since 1.0.0
     */
    public function builder_content()
    {
        wpforms_panel_field(
            'checkbox',
            $this->slug,
            'securepay_enabletestmode',
            $this->form_data,
            esc_html__('Enable SecurePay test mode', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'default' => '0',
                'tooltip' => esc_html__('Enable this option to test without credentials', 'securepaywpforms'),
            ],
            true
        );

        $live = wpforms_panel_field(
            'checkbox',
            $this->slug,
            'securepay_enable',
            $this->form_data,
            esc_html__('Enable SecurePay live mode', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'default' => '0',
                'tooltip' => esc_html__('Enable this option to use on production', 'securepaywpforms'),
            ],
            false
        );

        $live .= wpforms_panel_field(
            'text',
            $this->slug,
            'securepay_live_token',
            $this->form_data,
            esc_html__('SecurePay Live Token', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'tooltip' => esc_html__('Your SecurePay Live Token', 'securepaywpforms'),
            ],
            false
        );

        $live .= wpforms_panel_field(
            'text',
            $this->slug,
            'securepay_live_checksum',
            $this->form_data,
            esc_html__('SecurePay Live Checksum Token', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'tooltip' => esc_html__('Your SecurePay Live Checksum Token', 'securepaywpforms'),
            ],
            false
        );

        $live .= wpforms_panel_field(
            'text',
            $this->slug,
            'securepay_live_uid',
            $this->form_data,
            esc_html__('SecurePay Live UID', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'tooltip' => esc_html__('Your SecurePay Live UID', 'securepaywpforms'),
            ],
            false
        );

        $live .= wpforms_panel_field(
            'text',
            $this->slug,
            'securepay_live_partner_uid',
            $this->form_data,
            esc_html__('SecurePay Live Partner UID', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'tooltip' => esc_html__('Your SecurePay Live Partner UID', 'securepaywpforms'),
            ],
            false
        );

        $spb = '<div class="wpforms-panel-fields-group"><div class="wpforms-panel-fields-group-border-top"></div>'.$live.'</div>';
        echo $spb;

        $sandbox = wpforms_panel_field(
            'checkbox',
            $this->slug,
            'securepay_enablesandbox',
            $this->form_data,
            esc_html__('Enable SecurePay sandbox mode', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'default' => '0',
                'tooltip' => esc_html__('Enable this option to use on development', 'securepaywpforms'),
            ],
            false
        );

        $sandbox .= wpforms_panel_field(
            'text',
            $this->slug,
            'securepay_sandbox_token',
            $this->form_data,
            esc_html__('SecurePay Sandbox Token', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'tooltip' => esc_html__('Your SecurePay Sandbox Token', 'securepaywpforms'),
            ],
            false
        );

        $sandbox .= wpforms_panel_field(
            'text',
            $this->slug,
            'securepay_sandbox_checksum',
            $this->form_data,
            esc_html__('SecurePay Sandbox Checksum Token', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'tooltip' => esc_html__('Your SecurePay Sandbox Checksum Token', 'securepaywpforms'),
            ],
            false
        );

        $sandbox .= wpforms_panel_field(
            'text',
            $this->slug,
            'securepay_sandbox_uid',
            $this->form_data,
            esc_html__('SecurePay Sandbox UID', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'tooltip' => esc_html__('Your SecurePay Sandbox UID', 'securepaywpforms'),
            ],
            false
        );

        $sandbox .= wpforms_panel_field(
            'text',
            $this->slug,
            'securepay_sandbox_partner_uid',
            $this->form_data,
            esc_html__('SecurePay Sandbox Partner UID', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'tooltip' => esc_html__('Your SecurePay Sandbox Partner UID', 'securepaywpforms'),
            ],
            false
        );

        $spb = '<div class="wpforms-panel-fields-group"><div class="wpforms-panel-fields-group-border-top"></div>'.$sandbox.'</div>';
        echo $spb;

        $misc = wpforms_panel_field(
            'checkbox',
            $this->slug,
            'securepay_banklist',
            $this->form_data,
            esc_html__('Show Bank List', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'default' => '0',
            ],
            false
        );

        $misc .= wpforms_panel_field(
            'checkbox',
            $this->slug,
            'securepay_banklogo',
            $this->form_data,
            esc_html__('Use Supported Bank Logo', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'default' => '0',
            ],
            false
        );

        $misc .= wpforms_panel_field(
            'text',
            $this->slug,
            'securepay_failed_redirect',
            $this->form_data,
            esc_html__('Payment Failed Redirect URL', 'securepaywpforms'),
            [
                'parent' => 'payments',
                'tooltip' => esc_html__('SecurePay Payment Failed Redirect URL', 'securepaywpforms'),
            ],
            false
        );

        $spb = '<div class="wpforms-panel-fields-group"><div class="wpforms-panel-fields-group-border-top"></div>'.$misc.'</div>';
        echo $spb;

        echo wp_get_inline_script_tag(
            "
                function securepay_mode() {
                    var testmode = jQuery('input#wpforms-panel-field-securepay_payment-securepay_enabletestmode').prop('checked');
                    var livemode = jQuery('input#wpforms-panel-field-securepay_payment-securepay_enable').prop('checked');
                    var sandboxmode = jQuery('input#wpforms-panel-field-securepay_payment-securepay_enablesandbox').prop('checked');
                    if (testmode ) {
                        jQuery('input#wpforms-panel-field-securepay_payment-securepay_enable').prop('checked', false);
                        jQuery('input#wpforms-panel-field-securepay_payment-securepay_enablesandbox').prop('checked', false);
                    } else if (livemode) {
                        jQuery('input#wpforms-panel-field-securepay_payment-securepay_enablesandbox').prop('checked', false);
                        jQuery('input#wpforms-panel-field-securepay_payment-securepay_enabletestmode').prop('checked', false);
                    } else if (sandboxmode) {
                        jQuery('input#wpforms-panel-field-securepay_payment-securepay_enable').prop('checked', false);
                        jQuery('input#wpforms-panel-field-securepay_payment-securepay_enabletestmode').prop('checked', false);
                    }
                };

                securepay_mode();
                jQuery('input#wpforms-panel-field-securepay_payment-securepay_enabletestmode').on('click', securepay_mode);
                jQuery('input#wpforms-panel-field-securepay_payment-securepay_enable').on('click', function() {
                    jQuery('input#wpforms-panel-field-securepay_payment-securepay_enabletestmode').prop('checked', false);
                    securepay_mode();
                });
                jQuery('input#wpforms-panel-field-securepay_payment-securepay_enablesandbox').on('click', function() {
                    jQuery('input#wpforms-panel-field-securepay_payment-securepay_enabletestmode').prop('checked', false);
                    jQuery('input#wpforms-panel-field-securepay_payment-securepay_enable').prop('checked', false);
                    securepay_mode();
                });
        "
        );
    }

    public function get_gateway_credentials($payment_settings)
    {
        if (!empty($payment_settings['securepay_enabletestmode']) && 1 === (int) $payment_settings['securepay_enabletestmode']) {
            $securepay_payment_url = SECUREPAY_WPFORMS_ENDPOINT_SANDBOX;
            $securepay_token = 'GFVnVXHzGEyfzzPk4kY3';
            $securepay_checksum = '3faa7b27f17c3fb01d961c08da2b6816b667e568efb827544a52c62916d4771d';
            $securepay_uid = '4a73a364-6548-4e17-9130-c6e9bffa3081';
            $securepay_partner_uid = '';
        } else {
            if (!empty($payment_settings['securepay_enablesandbox']) && 1 === (int) $payment_settings['securepay_enablesandbox']) {
                $securepay_payment_url = SECUREPAY_WPFORMS_ENDPOINT_SANDBOX;
                $securepay_token = $payment_settings['securepay_sandbox_token'];
                $securepay_checksum = $payment_settings['securepay_sandbox_checksum'];
                $securepay_uid = $payment_settings['securepay_sandbox_uid'];
                $securepay_partner_uid = $payment_settings['securepay_sandbox_partner_uid'];
            } else {
                $securepay_payment_url = SECUREPAY_WPFORMS_ENDPOINT_LIVE;
                $securepay_token = $payment_settings['securepay_live_token'];
                $securepay_checksum = $payment_settings['securepay_live_checksum'];
                $securepay_uid = $payment_settings['securepay_live_uid'];
                $securepay_partner_uid = $payment_settings['securepay_live_partner_uid'];
            }
        }

        $credentials = [
            'securepay_token' => $securepay_token,
            'securepay_checksum' => $securepay_checksum,
            'securepay_uid' => $securepay_uid,
            'securepay_partner_uid' => $securepay_partner_uid,
            'securepay_payment_url' => $securepay_payment_url,
        ];

        return $credentials;
    }

    private function get_bank_list($force = false, $is_sandbox = false)
    {
        if (is_user_logged_in()) {
            $force = true;
        }

        $bank_list = $force ? false : get_transient(SECUREPAY_WPFORMS_SLUG.'_banklist');
        $endpoint_pub = $is_sandbox ? SECUREPAY_WPFORMS_ENDPOINT_PUBLIC_SANDBOX : SECUREPAY_WPFORMS_ENDPOINT_PUBLIC_LIVE;

        if (empty($bank_list)) {
            $remote = wp_remote_get(
                $endpoint_pub.'/banks/b2c?status',
                [
                    'timeout' => 10,
                    'user-agent' => SECUREPAY_WPFORMS_SLUG.'/'.SECUREPAY_WPFORMS_VERSION,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Referer' => home_url(),
                    ],
                ]
            );

            if (!is_wp_error($remote) && isset($remote['response']['code']) && 200 === $remote['response']['code'] && !empty($remote['body'])) {
                $data = json_decode($remote['body'], true);
                if (!empty($data) && \is_array($data) && !empty($data['fpx_bankList'])) {
                    $list = $data['fpx_bankList'];
                    foreach ($list as $arr) {
                        $status = 1;
                        if (empty($arr['status_format2']) || 'offline' === $arr['status_format1']) {
                            $status = 0;
                        }

                        $bank_list[$arr['code']] = [
                            'name' => $arr['name'],
                            'status' => $status,
                        ];
                    }

                    if (!empty($bank_list) && \is_array($bank_list)) {
                        set_transient(SECUREPAY_WPFORMS_SLUG.'_banklist', $bank_list, 60);
                    }
                }
            }
        }

        return !empty($bank_list) && \is_array($bank_list) ? $bank_list : false;
    }

    private function is_bank_list($settings, &$bank_list = '')
    {
        $is_sandbox = (!empty($settings['securepay_enablesandbox']) && 1 === (int) $settings['securepay_enablesandbox']
                    || !empty($settings['securepay_enabletestmode']) && 1 === (int) $settings['securepay_enabletestmode']) ? true : false;

        if (!empty($settings['securepay_banklist']) && 1 === (int) $settings['securepay_banklist']) {
            $bank_list = $this->get_bank_list(false, $is_sandbox);

            return !empty($bank_list) && \is_array($bank_list) ? true : false;
        }

        $bank_list = '';

        return false;
    }

    public function securepay_scripts()
    {
        if (!is_admin()) {
            $version = SECUREPAY_WPFORMS_VERSION.'x'.(\defined('WP_DEBUG') && WP_DEBUG ? time() : date('Ymdh'));
            $slug = SECUREPAY_WPFORMS_SLUG;
            $url = SECUREPAY_WPFORMS_URL;
            $selectid = 'securepayselect2';
            $selectdeps = [];
            if (wp_script_is('select2', 'enqueued')) {
                $selectdeps = ['jquery', 'select2'];
            } elseif (wp_script_is('selectWoo', 'enqueued')) {
                $selectdeps = ['jquery', 'selectWoo'];
            } elseif (wp_script_is($selectid, 'enqueued')) {
                $selectdeps = ['jquery', $selectid];
            }

            if (empty($selectdeps)) {
                wp_enqueue_style($selectid, $url.'includes/admin/min/select2.min.css', null, $version);
                wp_enqueue_script($selectid, $url.'includes/admin/min/select2.min.js', ['jquery'], $version);
                $selectdeps = ['jquery', $selectid];
            }

            wp_enqueue_script($slug, $url.'includes/admin/securepaywpforms.js', $selectdeps, $version);

            // remove jquery
            unset($selectdeps[0]);

            wp_enqueue_style($selectid.'-helper', $url.'includes/admin/securepaywpforms.css', $selectdeps, $version);
            wp_add_inline_script($slug, 'function securepaybankwpforms() { if ( "function" === typeof(securepaywpforms_bank_select) ) { securepaywpforms_bank_select(jQuery, "'.$url.'includes/admin/bnk/", '.time().', "'.$version.'"); }}');
        }
    }

    public function banklist_output($form_data)
    {
        if (empty($form_data['payments'][$this->slug])) {
            return;
        }

        $html = '';
        $bank_list = '';
        $settings = $form_data['payments'][$this->slug];
        if ($this->is_bank_list($settings, $bank_list)) {
            $bank_id = !empty($_POST['buyer_bank_code']) ? sanitize_text_field($_POST['buyer_bank_code']) : false;
            $image = false;
            if (!empty($settings['securepay_banklogo']) && 1 === (int) $settings['securepay_banklogo']) {
                $image = SECUREPAY_WPFORMS_URL.'includes/admin/securepay-bank-alt.png';
            }

            $html = '<div id="'.$this->slug.'-fpxbank" class="spwfmbody">';
            $html .= '<label class="wpforms-field-label">Pay with SecurePay</label>';

            if (!empty($image)) {
                $html .= '<img src="'.$image.'" class="spwfmlogo">';
            }

            $html .= '<select name="buyer_bank_code" id="buyer_bank_code" class="wpforms-field-medium">';
            $html .= "<option value=''>Please Select Bank</option>";
            foreach ($bank_list as $id => $arr) {
                $name = $arr['name'];
                $status = $arr['status'];

                $disabled = empty($status) ? ' disabled' : '';
                $offline = empty($status) ? ' (Offline)' : '';
                $selected = $id === $bank_id ? ' selected' : '';
                $html .= '<option value="'.$id.'"'.$selected.$disabled.'>'.$name.$offline.'</option>';
            }
            $html .= '</select>';

            $html .= '</div>';

            $html .= wp_get_inline_script_tag('if ( "function" === typeof(securepaybankwpforms) ) {securepaybankwpforms();}', ['id' => SECUREPAY_WPFORMS_SLUG.'-bankselect']);

            echo $html;
        }
    }

    private function calculate_sign($checksum, $a, $b, $c, $d, $e, $f, $g, $h, $i)
    {
        $str = $a.'|'.$b.'|'.$c.'|'.$d.'|'.$e.'|'.$f.'|'.$g.'|'.$h.'|'.$i;

        return hash_hmac('sha256', $str, $checksum);
    }

    private function get_customer($form_data, $entry)
    {
        $name = '';
        $email = '';
        $phone = '';
        if (!empty($form_data) && !empty($entry)) {
            foreach ($form_data['fields'] as $num => $arr) {
                switch ($arr['type']) {
                    case 'name':
                        if ('simple' === $arr['format']) {
                            $name = $entry['fields'][$arr['id']];
                        } elseif ('first-last' === $arr['format']) {
                            $name = '';
                            if (isset($entry['fields'][$arr['id']]['first'])) {
                                $name = $entry['fields'][$arr['id']]['first'];
                            }

                            if (isset($entry['fields'][$arr['id']]['last'])) {
                                $name .= ' '.$entry['fields'][$arr['id']]['last'];
                            }
                        } elseif ('first-middle-last' === $arr['format']) {
                            $name = '';
                            if (isset($entry['fields'][$arr['id']]['first'])) {
                                $name = $entry['fields'][$arr['id']]['first'];
                            }

                            if (isset($entry['fields'][$arr['id']]['middle'])) {
                                $name .= ' '.$entry['fields'][$arr['id']]['middle'];
                            }

                            if (isset($entry['fields'][$arr['id']]['last'])) {
                                $name .= ' '.$entry['fields'][$arr['id']]['last'];
                            }
                        }
                        break;
                    case 'email':
                        $email = $entry['fields'][$arr['id']];
                        break;
                    case 'phone':
                        $phone = $entry['fields'][$arr['id']];
                        break;
                }
            }
        }

        return [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ];
    }

    private function get_url()
    {
        // fix response from api
        $req = $_SERVER['REQUEST_URI'];
        if (false !== strpos($req, 'wpforms_return')) {
            $req = str_replace('&amp;', '&', $req);
            $req = str_replace('%26amp%3B', '&', $req);
            $req = str_replace('amp%3B', '&', $req);
            $req = str_replace('?&wpforms_return', '?wpforms_return', $req);

            parse_str($req, $dataq);
            if (!empty($dataq)) {
                foreach ($dataq as $k => $v) {
                    $_REQUEST[$k] = $v;
                }
            }
        }

        $req = preg_replace('@(\?)?(\&)?(securepaycancel|securepaytimeout)=.*@', '', $req);
        $url = get_bloginfo('url').$req;

        return $url;
    }

    public function process_entry($fields, $entry, $form_data, $entry_id)
    {
        $error = false;

        if (empty($entry_id)) {
            return;
        }

        if (empty($form_data['payments'][$this->slug])) {
            return;
        }

        $payment_settings = $form_data['payments'][$this->slug];
        $credentials = $this->get_gateway_credentials($payment_settings);
        $customer_data = $this->get_customer($form_data, $entry);

        if (empty($credentials['securepay_token'])
                || empty($credentials['securepay_checksum'])
                || empty($credentials['securepay_uid'])
                || empty($credentials['securepay_payment_url'])) {
            return;
        }

        $form_has_payments = wpforms_has_payment('form', $form_data);
        $entry_has_paymemts = wpforms_has_payment('entry', $fields);
        if (!$form_has_payments || !$entry_has_paymemts) {
            $error = 'SecurePay Payment stopped, missing payment fields';
        }

        // Check total charge amount.
        $amount = wpforms_get_total_payment($fields);
        if (empty($amount) || $amount == wpforms_sanitize_amount(0)) {
            $error = 'SecurePay Payment stopped, invalid/empty amount';
        }

        if ($error) {
            wpforms_log(
                esc_html__('SecurePay Payment Error', 'securepaywpforms'),
                $remote_post,
                [
                    'parent' => $entry_id,
                    'type' => ['error', 'payment'],
                    'form_id' => $form_data['id'],
                ]
            );

            return;
        }

        // Update entry to include payment details.
        $entry_data = [
            'status' => 'pending',
            'type' => 'payment',
            'meta' => wp_json_encode(
                [
                    'payment_type' => $this->slug,
                    'payment_total' => $amount,
                    'payment_currency' => 'MYR',
                ]
            ),
        ];
        wpforms()->entry->update($entry_id, $entry_data, '', '', ['cap' => false]);

        $query_args = 'form_id='.$form_data['id'].'&entry_id='.$entry_id.'&hash='.wp_hash($form_data['id'].','.$entry_id);
        $query_hash = base64_encode($query_args);

        $redirect_url = $this->get_url();
        $redirect_url = esc_url_raw(
            add_query_arg(
                [
                    'wpforms_return' => $query_hash,
                ],
                apply_filters('wpforms_securepay_return_url', $redirect_url, $form_data)
            )
        );

        if (empty($customer_data['email']) && empty($customer_data['phone'])) {
            $customer_email = 'noreply@securepay.my';
        } else {
            $customer_email = $customer_data['email'];
        }

        $customer_name = !empty($customer_data['name']) ? $customer_data['name'] : '';
        $customer_phone = !empty($customer_data['phone']) ? $customer_data['phone'] : '';

        $callback_url = $redirect_url;
        $cancel_url = $this->get_url();

        if (false !== strpos($cancel_url, '?')) {
            $cancel_url = $cancel_url.'&securepaycancel='.$query_hash;
        } else {
            $cancel_url = $cancel_url.'?securepaycancel='.$query_hash;
        }
        $cancel_url = str_replace('?&', '?', $cancel_url);

        $timeout_url = $this->get_url();
        if (false !== strpos($timeout_url, '?')) {
            $timeout_url = $timeout_url.'&securepaytimeout='.$query_hash;
        } else {
            $timeout_url = $timeout_url.'?securepaytimeout='.$query_hash;
        }
        $timeout_url = str_replace('?&', '?', $timeout_url);

        $securepay_token = $credentials['securepay_token'];
        $securepay_checksum = $credentials['securepay_checksum'];
        $securepay_uid = $credentials['securepay_uid'];
        $securepay_partner_uid = $credentials['securepay_partner_uid'];
        $securepay_payment_url = $credentials['securepay_payment_url'];

        $description = $form_data['settings']['form_title'].' (Order No: '.$entry_id.')';
        $securepay_sign = $this->calculate_sign($securepay_checksum, $customer_email, $customer_name, $customer_phone, $redirect_url, $entry_id, $description, $redirect_url, $amount, $securepay_uid);

        $buyer_bank_code = !empty($_POST['buyer_bank_code']) ? sanitize_text_field($_POST['buyer_bank_code']) : false;

        $securepay_args['order_number'] = esc_attr($entry_id);
        $securepay_args['buyer_name'] = esc_attr($customer_name);
        $securepay_args['buyer_email'] = esc_attr($customer_email);
        $securepay_args['buyer_phone'] = esc_attr($customer_phone);
        $securepay_args['product_description'] = esc_attr($description);
        $securepay_args['transaction_amount'] = esc_attr($amount);
        $securepay_args['redirect_url'] = esc_url_raw($redirect_url);
        $securepay_args['callback_url'] = esc_url_raw($callback_url);
        $securepay_args['cancel_url'] = esc_url_raw($cancel_url);
        $securepay_args['timeout_url'] = esc_url_raw($timeout_url);
        $securepay_args['token'] = esc_attr($securepay_token);
        $securepay_args['partner_uid'] = esc_attr($securepay_partner_uid);
        $securepay_args['checksum'] = esc_attr($securepay_sign);
        $securepay_args['payment_source'] = 'wpforms';

        if (!empty($payment_settings['securepay_banklist']) && 1 === (int) $payment_settings['securepay_banklist'] && !empty($buyer_bank_code)) {
            $securepay_args['buyer_bank_code'] = $buyer_bank_code;
        }

        $output = '<!doctype html><html><head><title>SecurePay</title>';
        $output .= '<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">';
        $output .= '<meta http-equiv="Pragma" content="no-cache"><meta http-equiv="Expires" content="0">';
        $output .= '</head><body>';
        $output .= '<form name="order" id="securepay_payment" method="post" action="'.esc_url_raw($securepay_payment_url).'payments">';
        foreach ($securepay_args as $f => $v) {
            $output .= '<input type="hidden" name="'.$f.'" value="'.$v.'">';
        }

        $output .= '</form>';
        $output .= wp_get_inline_script_tag('document.getElementById( "securepay_payment" ).submit();');
        $output .= '</body></html>';

        exit($output);
    }

    private function sanitize_response()
    {
        $params = [
             'amount',
             'bank',
             'buyer_email',
             'buyer_name',
             'buyer_phone',
             'checksum',
             'client_ip',
             'created_at',
             'created_at_unixtime',
             'currency',
             'exchange_number',
             'fpx_status',
             'fpx_status_message',
             'fpx_transaction_id',
             'fpx_transaction_time',
             'id',
             'interface_name',
             'interface_uid',
             'merchant_reference_number',
             'name',
             'order_number',
             'payment_id',
             'payment_method',
             'payment_status',
             'receipt_url',
             'retry_url',
             'source',
             'status_url',
             'transaction_amount',
             'transaction_amount_received',
             'uid',
             'securepaycancel',
             'securepaytimeout',
             'wpforms_return',
         ];

        $response_params = [];
        if (isset($_REQUEST)) {
            foreach ($params as $k) {
                if (isset($_REQUEST[$k])) {
                    $response_params[$k] = sanitize_text_field($_REQUEST[$k]);
                }
            }
        }

        return $response_params;
    }

    private function response_status($response_params)
    {
        if ((isset($response_params['payment_status']) && 'true' === $response_params['payment_status']) || (isset($response_params['fpx_status']) && 'true' === $response_params['fpx_status'])) {
            return true;
        }

        return false;
    }

    private function is_response_callback($response_params)
    {
        if (isset($response_params['fpx_status'])) {
            return true;
        }

        return false;
    }

    private function redirect($redirect)
    {
        if (!headers_sent()) {
            wp_redirect($redirect);
            exit;
        }

        $html = "<script>window.location.replace('".$redirect."');</script>";
        $html .= '<noscript><meta http-equiv="refresh" content="1; url='.$redirect.'">Redirecting..</noscript>';

        echo wp_kses(
            $html,
            [
                'script' => [],
                'noscript' => [],
                'meta' => [
                    'http-equiv' => [],
                    'content' => [],
                ],
            ]
        );
        exit;
    }

    public function process_callback()
    {
        $response_params = $this->sanitize_response();

        if (!empty($response_params) && isset($response_params['order_number'])) {
            $success = $this->response_status($response_params);

            $callback = $this->is_response_callback($response_params) ? 'Callback' : 'Redirect';
            $receipt_link = !empty($response_params['receipt_url']) ? $response_params['receipt_url'] : '';
            $status_link = !empty($response_params['status_url']) ? $response_params['status_url'] : '';
            $retry_link = !empty($response_params['retry_url']) ? $response_params['retry_url'] : '';

            $payment_id = absint($response_params['order_number']);
            $payment = wpforms()->entry->get(absint($payment_id));

            if (!isset($payment->form_id)) {
                return;
            }

            $form_data = wpforms()->form->get(
                $payment->form_id,
                [
                    'content_only' => true,
                ]
            );

            if (empty($payment) || empty($form_data)) {
                return;
            }

            $payment_meta = json_decode($payment->meta, true);

            if ($success) {
                $note = 'SecurePay payment successful<br>';
                $note .= 'Response from: '.$callback.'<br>';
                $note .= 'Transaction ID: '.$response_params['merchant_reference_number'].'<br>';

                if (!empty($receipt_link)) {
                    $note .= 'Receipt link: <a href="'.$receipt_link.'" target=new rel="noopener">'.$receipt_link.'</a><br>';
                }

                if (!empty($status_link)) {
                    $note .= 'Status link: <a href="'.$status_link.'" target=new rel="noopener">'.$status_link.'</a><br>';
                }

                wpforms()->entry_meta->add(
                    [
                        'entry_id' => $payment_id,
                        'form_id' => $payment->form_id,
                        'user_id' => 1,
                        'type' => 'note',
                        'data' => $note,
                    ],
                    'entry_meta'
                );

                $payment_meta['payment_transaction'] = $response_params['merchant_reference_number'];
                wpforms()->entry->update(
                    $payment_id,
                    [
                        'status' => 'completed',
                        'meta' => wp_json_encode($payment_meta),
                    ],
                    '',
                    '',
                    ['cap' => false]
                );
            } else {
                $note = 'SecurePay payment failed<br>';
                $note .= 'Response from: '.$callback.'<br>';
                $note .= 'Transaction ID: '.$response_params['merchant_reference_number'].'<br>';

                if (!empty($retry_link)) {
                    $note .= 'Retry link: <a href="'.$retry_link.'" target=new rel="noopener">'.$retry_link.'</a><br>';
                }

                if (!empty($status_link)) {
                    $note .= 'Status link: <a href="'.$status_link.'" target=new rel="noopener">'.$status_link.'</a><br>';
                }

                wpforms()->entry_meta->add(
                    [
                        'entry_id' => $payment_id,
                        'form_id' => $payment->form_id,
                        'user_id' => 1,
                        'type' => 'note',
                        'data' => $note,
                    ],
                    'entry_meta'
                );

                $payment_meta['payment_transaction'] = $response_params['merchant_reference_number'];
                wpforms()->entry->update(
                    $payment_id,
                    [
                        'status' => 'failed',
                        'meta' => wp_json_encode($payment_meta),
                    ],
                    '',
                    '',
                    ['cap' => false]
                );
            }

            do_action('securepay_wpforms_process_complete', wpforms_decode($payment->fields), $form_data, $payment_id, $response_params);

            if (!empty($_GET['wpforms_return'])) {
                $str = base64_decode($_GET['wpforms_return']);
                if (false !== $str) {
                    parse_str($str, $data);
                    if (!empty($data) && \is_array($data)) {
                        $payment_id = absint($data['entry_id']);
                        $payment = wpforms()->entry->get(absint($payment_id));
                        if (!empty($payment) && 'failed' === $payment->status) {
                            $form_data = wpforms()->form->get(
                                $payment->form_id,
                                [
                                    'content_only' => true,
                                ]
                            );
                            $payment_settings = $form_data['payments'][$this->slug];
                            if (!empty($payment_settings['securepay_failed_redirect'])) {
                                $this->redirect($payment_settings['securepay_failed_redirect']);
                                exit;
                            }
                        }
                    }
                }

                $this->redirect($this->get_url());
            }
            exit;
        }

        if (!empty($response_params) && (!empty($response_params['securepaycancel']) || !empty($response_params['securepaytimeout']))) {
            $status = !empty($response_params['securepaycancel']) ? 'cancelled' : 'timeout';
            $query_hash = !empty($response_params['securepaycancel']) ? $response_params['securepaycancel'] : $response_params['securepaytimeout'];

            $str = base64_decode($query_hash);
            if (false !== $str) {
                parse_str($str, $data);
                if (!empty($data) && \is_array($data)) {
                    $payment_id = absint($data['entry_id']);
                    $payment = wpforms()->entry->get(absint($payment_id));
                    if (!empty($payment) && 'pending' === $payment->status) {
                        $note = 'SecurePay payment '.$status.'<br>';
                        wpforms()->entry_meta->add(
                            [
                                'entry_id' => $payment_id,
                                'form_id' => $payment->form_id,
                                'user_id' => 1,
                                'type' => 'note',
                                'data' => $note,
                            ],
                            'entry_meta'
                        );

                        $payment_meta = json_decode($payment->meta, true);
                        wpforms()->entry->update(
                            $payment_id,
                            [
                                'status' => $status,
                                'meta' => wp_json_encode($payment_meta),
                            ],
                            '',
                            '',
                            ['cap' => false]
                        );
                    }
                }
            }

            $this->redirect($this->get_url());
            exit;
        }
    }
}
