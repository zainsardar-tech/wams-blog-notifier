<?php
/**
 * Admin settings for WAMS Blog Notifier
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add Menu
add_action('admin_menu', 'wams_bn_add_admin_menu');

function wams_bn_add_admin_menu()
{
    add_menu_page(
        'WAMS Notifier',
        'WAMS Notifier',
        'manage_options',
        'wams-blog-notifier',
        'wams_bn_settings_page',
        'dashicons-whatsapp',
        100
    );
}

// Register Settings
add_action('admin_init', 'wams_bn_register_settings');

function wams_bn_register_settings()
{
    register_setting('wams_bn_settings_group', 'wams_bn_api_key');
    register_setting('wams_bn_settings_group', 'wams_bn_sender_number');
    register_setting('wams_bn_settings_group', 'wams_bn_message_template');
    register_setting('wams_bn_settings_group', 'wams_bn_footer_text');
    register_setting('wams_bn_settings_group', 'wams_bn_form_title');
    register_setting('wams_bn_settings_group', 'wams_bn_form_desc');
    register_setting('wams_bn_settings_group', 'wams_bn_form_color');
}

function wams_bn_settings_page()
{
    // Enqueue admin assets
    wp_enqueue_style('wams-bn-admin-styles', WAMS_BN_URL . 'assets/css/admin-styles.css', array(), '1.0.0');
    wp_enqueue_script('wams-bn-admin-test', WAMS_BN_URL . 'assets/js/admin-test.js', array('jquery'), '1.0.0', true);
    wp_localize_script('wams-bn-admin-test', 'wams_admin_obj', array(
        'nonce' => wp_create_nonce('wams_bn_admin_nonce')
    ));
    ?>
    <div class="wrap">
        <h1>WAMS Blog Notifier Settings</h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=wams-blog-notifier&tab=settings"
                class="nav-tab <?php echo !isset($_GET['tab']) || $_GET['tab'] == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
            <a href="?page=wams-blog-notifier&tab=subscribers"
                class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'subscribers' ? 'nav-tab-active' : ''; ?>">Subscribers</a>
            <a href="?page=wams-blog-notifier&tab=logs"
                class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] == 'logs' ? 'nav-tab-active' : ''; ?>">Logs</a>
        </h2>

        <?php
        $tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';

        if ($tab == 'settings') {
            ?>
            <form method="post" action="options.php">
                <?php settings_fields('wams_bn_settings_group'); ?>
                <?php do_settings_sections('wams_bn_settings_group'); ?>

                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">API Key</th>
                        <td><input type="text" name="wams_bn_api_key"
                                value="<?php echo esc_attr(get_option('wams_bn_api_key')); ?>" class="regular-text"
                                placeholder="Enter your Aztify API Key" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Sender Number</th>
                        <td><input type="text" name="wams_bn_sender_number"
                                value="<?php echo esc_attr(get_option('wams_bn_sender_number')); ?>" class="regular-text"
                                placeholder="e.g. 62888xxxx" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Message Template</th>
                        <td>
                            <textarea name="wams_bn_message_template" rows="5" cols="50" class="large-text"
                                placeholder="📢 *New Blog Post from {blog_name}*&#10;&#10;*{post_title}*&#10;&#10;Read more here: {post_url}"><?php echo esc_textarea(get_option('wams_bn_message_template', "📢 *New Blog Post from {blog_name}*\n\n*{post_title}*\n\nRead more here: {post_url}")); ?></textarea>
                            <p class="description">Available placeholders: <code>{blog_name}</code>, <code>{post_title}</code>,
                                <code>{post_url}</code>
                            </p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Footer Text</th>
                        <td>
                            <input type="text" name="wams_bn_footer_text"
                                value="<?php echo esc_attr(get_option('wams_bn_footer_text', 'Sent via Blog Notifier')); ?>"
                                class="regular-text" placeholder="e.g. Sent via Blog Notifier" />
                            <p class="description">Text shown at the very bottom of the WhatsApp message.</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Form Title</th>
                        <td>
                            <input type="text" name="wams_bn_form_title"
                                value="<?php echo esc_attr(get_option('wams_bn_form_title', 'Subscribe to Latest Posts')); ?>"
                                class="regular-text" placeholder="Subscribe to Latest Posts" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Form Description</th>
                        <td>
                            <textarea name="wams_bn_form_desc" rows="2" cols="50" class="large-text"
                                placeholder="Get instant updates on WhatsApp whenever we publish a new blog!"><?php echo esc_textarea(get_option('wams_bn_form_desc', "Get instant updates on WhatsApp whenever we publish a new blog!")); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Primary Color</th>
                        <td>
                            <input type="color" name="wams_bn_form_color"
                                value="<?php echo esc_attr(get_option('wams_bn_form_color', '#25D366')); ?>" />
                            <p class="description">Main color for the subscription form and buttons.</p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

            <!-- Test Connection Section -->
            <div class="wams-test-section">
                <h3>🔍 Test API Connection</h3>
                <p>Click the button below to verify that your API credentials are working correctly. A test message will be sent
                    to your sender number.</p>
                <button type="button" id="wams-test-connection" class="button">Test Connection</button>
                <div id="wams-test-result"></div>
            </div>
            <?php
        } elseif ($tab == 'subscribers') {
            $action = isset($_GET['action']) ? $_GET['action'] : 'list';

            // Process Actions
            if (isset($_POST['wams_bn_subscriber_submit'])) {
                check_admin_referer('wams_bn_subscriber_action');
                $number = sanitize_text_field($_POST['number']);
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

                if ($id > 0) {
                    wams_bn_update_subscriber($id, $number);
                    echo '<div class="updated"><p>Subscriber updated successfully!</p></div>';
                } else {
                    wams_bn_add_subscriber($number);
                    echo '<div class="updated"><p>Subscriber added successfully!</p></div>';
                }
                $action = 'list';
            }

            if ($action == 'delete' && isset($_GET['id'])) {
                check_admin_referer('delete_subscriber_' . $_GET['id']);
                wams_bn_delete_subscriber(intval($_GET['id']));
                echo '<div class="updated"><p>Subscriber deleted.</p></div>';
                $action = 'list';
            }

            // Show UI based on action
            if ($action == 'add' || $action == 'edit') {
                $subscriber = ($action == 'edit') ? wams_bn_get_subscriber(intval($_GET['id'])) : null;
                ?>
                <h2><?php echo ($action == 'edit') ? 'Edit Subscriber' : 'Add New Subscriber'; ?></h2>
                <form method="post" action="?page=wams-blog-notifier&tab=subscribers">
                    <?php wp_nonce_field('wams_bn_subscriber_action'); ?>
                    <?php if ($subscriber): ?>
                        <input type="hidden" name="id" value="<?php echo esc_attr($subscriber['id']); ?>">
                    <?php endif; ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Phone Number</th>
                            <td>
                                <input type="text" name="number"
                                    value="<?php echo $subscriber ? esc_attr($subscriber['number']) : ''; ?>" class="regular-text"
                                    required placeholder="e.g. 62888xxxx">
                            </td>
                        </tr>
                    </table>
                    <?php submit_button($subscriber ? 'Update Subscriber' : 'Add Subscriber', 'primary', 'wams_bn_subscriber_submit'); ?>
                    <a href="?page=wams-blog-notifier&tab=subscribers" class="button">Cancel</a>
                </form>
                <?php
            } else {
                ?>
                <div style="margin: 20px 0;">
                    <a href="?page=wams-blog-notifier&tab=subscribers&action=add" class="button button-primary">Add New
                        Subscriber</a>
                </div>
                <?php
                $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
                $per_page = 20;
                $offset = ($current_page - 1) * $per_page;
                $subscribers = wams_bn_get_subscribers_paginated($per_page, $offset);
                $total_subscribers = wams_bn_count_subscribers();
                $total_pages = ceil($total_subscribers / $per_page);
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Phone Number</th>
                            <th>Subscribed At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($subscribers):
                            foreach ($subscribers as $subscriber): ?>
                                <tr>
                                    <td><?php echo esc_html($subscriber['id']); ?></td>
                                    <td><?php echo esc_html($subscriber['number']); ?></td>
                                    <td><?php echo esc_html($subscriber['created_at']); ?></td>
                                    <td>
                                        <a href="?page=wams-blog-notifier&tab=subscribers&action=edit&id=<?php echo $subscriber['id']; ?>"
                                            class="button">Edit</a>
                                        <a href="<?php echo wp_nonce_url('?page=wams-blog-notifier&tab=subscribers&action=delete&id=' . $subscriber['id'], 'delete_subscriber_' . $subscriber['id']); ?>"
                                            class="button button-link-delete" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="4">No subscribers found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="tablenav">
                    <div class="tablenav-pages">
                        <?php
                        echo paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'prev_text' => __('&laquo;'),
                            'next_text' => __('&raquo;'),
                            'total' => $total_pages,
                            'current' => $current_page
                        ));
                        ?>
                    </div>
                </div>
                <?php
            }
        } elseif ($tab == 'logs') {
            if (isset($_POST['clear_logs'])) {
                wams_bn_clear_logs();
                echo '<div class="updated"><p>Logs cleared.</p></div>';
            }
            ?>
            <form method="post">
                <textarea readonly
                    style="width:100%; height:400px; font-family:monospace;"><?php echo esc_textarea(wams_bn_get_logs()); ?></textarea>
                <p>
                    <input type="submit" name="clear_logs" class="button" value="Clear Logs"
                        onclick="return confirm('Are you sure?')">
                </p>
            </form>
            <?php
        }
        ?>

        <hr>
        <h2>Usage</h2>
        <p>Use the shortcode <code>[wams_subscribe]</code> on any page or post to display the WhatsApp subscription form.
        </p>
    </div>
    <?php
}

// AJAX Handler for Test Connection
add_action('wp_ajax_wams_test_connection', 'wams_bn_handle_test_connection');

function wams_bn_handle_test_connection()
{
    check_ajax_referer('wams_bn_admin_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access'));
    }

    $result = wams_bn_test_connection();

    if ($result['success']) {
        wp_send_json_success(array('message' => $result['message']));
    } else {
        wp_send_json_error(array('message' => $result['message']));
    }
}
