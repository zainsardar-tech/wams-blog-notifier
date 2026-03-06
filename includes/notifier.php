<?php
/**
 * Notifier logic for WAMS Blog Notifier
 */

if (!defined('ABSPATH')) {
    exit;
}

// Hook into post publication
add_action('transition_post_status', 'wams_bn_trigger_notifications', 10, 3);

function wams_bn_trigger_notifications($new_status, $old_status, $post)
{
    // Only trigger when a post is published (and it wasn't already published)
    if ($new_status !== 'publish' || $old_status === 'publish' || $post->post_type !== 'post') {
        return;
    }

    $subscribers = wams_bn_get_all_subscribers();

    if (empty($subscribers)) {
        return;
    }

    $post_title = get_the_title($post->ID);
    $post_url = get_permalink($post->ID);
    $blog_name = get_bloginfo('name');

    $default_template = "📢 *New Blog Post from {blog_name}*\n\n*{post_title}*\n\nRead more here: {post_url}";
    $template = get_option('wams_bn_message_template', $default_template);

    $message = str_replace(
        array('{blog_name}', '{post_title}', '{post_url}'),
        array($blog_name, $post_title, $post_url),
        $template
    );

    foreach ($subscribers as $subscriber) {
        // Send message to each subscriber
        wams_bn_send_message($subscriber['number'], $message);
    }
}
