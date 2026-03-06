<?php
/**
 * Shortcode and AJAX for WAMS Blog Notifier
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register Shortcode
add_shortcode('wams_subscribe', 'wams_bn_shortcode_markup');

function wams_bn_shortcode_markup()
{
    wp_enqueue_script('wams-bn-ajax', WAMS_BN_URL . 'assets/js/subscribe.js', array('jquery'), '1.0.0', true);
    wp_localize_script('wams-bn-ajax', 'wams_bn_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wams_bn_nonce')
    ));

    $title = get_option('wams_bn_form_title', 'Subscribe to Latest Posts');
    $desc = get_option('wams_bn_form_desc', 'Get instant updates on WhatsApp whenever we publish a new blog!');
    $primary_color = get_option('wams_bn_form_color', '#25D366');

    ob_start();
    ?>
    <div class="wams-subscription-container">
        <div class="wams-card">
            <h3><?php echo esc_html($title); ?></h3>
            <p><?php echo esc_html($desc); ?></p>
            <form id="wams-subscribe-form">
                <div class="wams-input-group">
                    <input type="text" id="wams-phone" name="phone" placeholder="e.g. 62888xxxx" required>
                    <button type="submit" id="wams-submit">Subscribe Now</button>
                </div>
                <div id="wams-message"></div>
            </form>
        </div>
    </div>

    <style>
        .wams-subscription-container {
            display: flex;
            justify-content: center;
            padding: 20px 0;
        }

        .wams-card {
            background: linear-gradient(135deg,
                    <?php echo esc_attr($primary_color); ?>
                    0%, #128C7E 100%);
            padding: 30px;
            border-radius: 15px;
            color: white;
            box-shadow: 0 10px 30px rgba(18, 140, 126, 0.3);
            max-width: 500px;
            width: 100%;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .wams-card:hover {
            transform: translateY(-5px);
        }

        .wams-card h3 {
            margin-top: 0;
            font-size: 24px;
            color: white;
        }

        .wams-card p {
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .wams-input-group {
            display: flex;
            background: white;
            padding: 5px;
            border-radius: 10px;
            overflow: hidden;
        }

        .wams-input-group input {
            border: none;
            padding: 12px 15px;
            flex-grow: 1;
            font-size: 16px;
            outline: none;
            color: #333;
        }

        .wams-input-group button {
            background:
                <?php echo esc_attr($primary_color); ?>
            ;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .wams-input-group button:hover {
            background: #000;
        }

        #wams-message {
            margin-top: 15px;
            font-weight: 500;
        }

        .wams-success {
            color: #fff;
            background: rgba(0, 0, 0, 0.2);
            padding: 5px;
            border-radius: 5px;
        }

        .wams-error {
            color: #ff4d4d;
            background: rgba(0, 0, 0, 0.2);
            padding: 5px;
            border-radius: 5px;
        }

        /* Mobile Responsiveness */
        @media screen and (max-width: 768px) {
            .wams-card {
                padding: 20px;
                border-radius: 12px;
                max-width: 100%;
            }

            .wams-card h3 {
                font-size: 20px;
            }

            .wams-card p {
                font-size: 14px;
            }

            .wams-input-group {
                flex-direction: column;
                padding: 0;
                background: transparent;
            }

            .wams-input-group input {
                width: 100%;
                box-sizing: border-box;
                margin-bottom: 10px;
                border-radius: 10px;
                background: white;
                font-size: 16px;
                padding: 14px 15px;
            }

            .wams-input-group button {
                width: 100%;
                box-sizing: border-box;
                padding: 14px 20px;
                font-size: 16px;
            }
        }

        @media screen and (max-width: 480px) {
            .wams-subscription-container {
                padding: 15px 10px;
            }

            .wams-card {
                padding: 15px;
            }

            .wams-card h3 {
                font-size: 18px;
            }

            .wams-card p {
                font-size: 13px;
                margin-bottom: 15px;
            }
        }
    </style>
    <?php
    return ob_get_clean();
}

// AJAX Handler
add_action('wp_ajax_wams_subscribe_action', 'wams_bn_handle_subscription');
add_action('wp_ajax_nopriv_wams_subscribe_action', 'wams_bn_handle_subscription');

function wams_bn_handle_subscription()
{
    check_ajax_referer('wams_bn_nonce', 'security');

    $number = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';

    if (empty($number)) {
        wp_send_json_error('Phone number is required.');
    }

    $result = wams_bn_add_subscriber($number);

    if ($result) {
        wp_send_json_success('Thank you! You are now subscribed.');
    } else {
        wp_send_json_error('Failed to subscribe or already subscribed.');
    }
}
