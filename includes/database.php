<?php
/**
 * Database setup for WAMS Blog Notifier
 */

if (!defined('ABSPATH')) {
    exit;
}

function wams_bn_create_database_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'wams_subscribers';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		number varchar(20) NOT NULL,
		created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY number (number)
	) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Add a new subscriber
 */
function wams_bn_add_subscriber($number)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'wams_subscribers';

    // Clean number
    $number = preg_replace('/[^0-9]/', '', $number);

    if (empty($number)) {
        return false;
    }

    return $wpdb->insert(
        $table_name,
        array(
            'number' => $number,
        )
    );
}

/**
 * Get all subscribers
 */
function wams_bn_get_all_subscribers()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'wams_subscribers';
    return $wpdb->get_results("SELECT number FROM $table_name", ARRAY_A);
}

/**
 * Delete a subscriber
 */
function wams_bn_delete_subscriber($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'wams_subscribers';
    return $wpdb->delete($table_name, array('id' => $id), array('%d'));
}

/**
 * Update a subscriber
 */
function wams_bn_update_subscriber($id, $number)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'wams_subscribers';

    // Clean number
    $number = preg_replace('/[^0-9]/', '', $number);

    if (empty($number)) {
        return false;
    }

    return $wpdb->update(
        $table_name,
        array('number' => $number),
        array('id' => $id),
        array('%s'),
        array('%d')
    );
}

/**
 * Get a single subscriber
 */
function wams_bn_get_subscriber($id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'wams_subscribers';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);
}

/**
 * Get subscribers with pagination
 */
function wams_bn_get_subscribers_paginated($limit = 20, $offset = 0)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'wams_subscribers';
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d", $limit, $offset), ARRAY_A);
}

/**
 * Count total subscribers
 */
function wams_bn_count_subscribers()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'wams_subscribers';
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
}
