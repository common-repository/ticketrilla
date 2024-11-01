<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined('WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

global $wp_filesystem, $wpdb;

// Delete Options

$options = array('ttlc_attachments_size', 'ttlc_attachments_time', 'ttlc_attachments_autoload', 'ttlc_responses_order');

foreach ( $options as $option) {
	delete_option( $option );
}

// Delete Posts

$wpdb->query("
	DELETE posts,pm
	FROM wp_posts posts
	LEFT JOIN wp_postmeta pm ON pm.post_id = posts.ID
	WHERE posts.post_type IN ('ttlc_product', 'ttlc_ticket', 'ttlc_attachment')
	"
);

// Delete Uploads

$upload_dir = wp_upload_dir('ttlc');
if ( $wp_filesystem->exists( $upload_dir['path'] ) ) {
	$wp_filesystem->rmdir( $upload_dir['path'], true );
}

