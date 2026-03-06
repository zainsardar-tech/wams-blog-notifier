<?php
/**
 * API Client for WAMS Blog Notifier
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Send WhatsApp text message using Aztify WAMS API
 */
function wams_bn_send_message($number, $message)
{
    $api_key = get_option('wams_bn_api_key');
    $sender = get_option('wams_bn_sender_number');

    if (empty($api_key) || empty($sender)) {
        return false;
    }

    $endpoint = 'https://wams.aztify.com/send-message';

    $footer = get_option('wams_bn_footer_text', 'Sent via Blog Notifier');

    $body = array(
        'api_key' => $api_key,
        'sender' => $sender,
        'number' => $number,
        'message' => $message,
        'footer' => $footer
    );

    $response = wp_remote_post($endpoint, array(
        'method' => 'POST',
        'timeout' => 15,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'body' => json_encode($body),
        'cookies' => array(),
    ));

    if (is_wp_error($response)) {
        $error_msg = $response->get_error_message();
        wams_bn_log("API Error: " . $error_msg, array('endpoint' => $endpoint, 'number' => $number));
        return $error_msg;
    }

    $response_body = wp_remote_retrieve_body($response);
    $result = json_decode($response_body, true);

    if (isset($result['status']) && $result['status'] === true) {
        wams_bn_log("Message sent to " . $number, array('response' => $result));
    } else {
        wams_bn_log("API Failed for " . $number, array('response' => $result, 'body' => $body));
    }

    return $result;
}

/**
 * Test API connection by sending a test message
 */
function wams_bn_test_connection()
{
    $api_key = get_option('wams_bn_api_key');
    $sender = get_option('wams_bn_sender_number');

    if (empty($api_key)) {
        return array(
            'success' => false,
            'message' => 'API Key is not configured. Please enter your API key in the settings.'
        );
    }

    if (empty($sender)) {
        return array(
            'success' => false,
            'message' => 'Sender Number is not configured. Please enter your sender number in the settings.'
        );
    }

    // Send test message to sender's own number
    $test_message = "✅ *WAMS API Test*\n\nYour API connection is working correctly!\n\nTimestamp: " . current_time('mysql');

    $result = wams_bn_send_message($sender, $test_message);

    if (is_array($result) && isset($result['status']) && $result['status'] === true) {
        wams_bn_log("API Test Successful", array('sender' => $sender));
        return array(
            'success' => true,
            'message' => 'Connection successful! A test message has been sent to ' . $sender
        );
    } else {
        $error_msg = is_string($result) ? $result : (isset($result['message']) ? $result['message'] : 'Unknown error');
        wams_bn_log("API Test Failed", array('error' => $error_msg, 'sender' => $sender));
        return array(
            'success' => false,
            'message' => 'Connection failed: ' . $error_msg
        );
    }
}
