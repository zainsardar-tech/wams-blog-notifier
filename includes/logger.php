<?php
/**
 * Simple Logger for WAMS Blog Notifier
 */

if (!defined('ABSPATH')) {
    exit;
}

function wams_bn_log($message, $data = array())
{
    $log_entry = "[" . date('Y-m-d H:i:s') . "] " . $message;
    if (!empty($data)) {
        $log_entry .= " | Data: " . json_encode($data);
    }
    $log_entry .= "\n";

    $upload_dir = wp_upload_dir();
    $log_file = $upload_dir['basedir'] . '/wams-blog-notifier.log';

    error_log($log_entry, 3, $log_file);
}

function wams_bn_get_logs()
{
    $upload_dir = wp_upload_dir();
    $log_file = $upload_dir['basedir'] . '/wams-blog-notifier.log';

    if (file_exists($log_file)) {
        return file_get_contents($log_file);
    }

    return 'No logs found.';
}

function wams_bn_clear_logs()
{
    $upload_dir = wp_upload_dir();
    $log_file = $upload_dir['basedir'] . '/wams-blog-notifier.log';

    if (file_exists($log_file)) {
        unlink($log_file);
    }
}
