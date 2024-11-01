<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$attachment = $this->data;	
	$external_id = $attachment->external_id;
	$external_url = $attachment->external_url;
	$external_title = $attachment->title;
	$external_md5 = $attachment->md5;
	$local_ticket_id = $attachment->ticket_id;
?>

<li title="<?php echo esc_attr( $attachment->title ); ?>" data-attachment-external-id="<?php esc_attr_e( $external_id ? $external_id : '' ); ?>">
	<a href="<?php echo esc_url( $attachment->url ); ?>" target="_blank" download ><span class="ttlc__attachments-icon"><i class="fa fa-file"></i></span></a>
	<div class="ttlc__attachments-info">
		<div class="ttlc__attachments-name"><?php echo esc_html( $attachment->title ); ?></div>
		<div class="ttlc__attachments-size">
			<span><?php echo esc_html( TTLC_Attachment::format_size( $attachment->size ) ); ?></span>
			<?php
				if ( $external_id && $external_url && $external_title && $local_ticket_id && $external_md5 ) {
			?>
			<a href="#" target="_blank" title="<?php esc_attr_e( 'Load to server', TTLC_TEXTDOMAIN ); ?>" class="ttlc-manual-attachment-download ttlc__attachments-load btn btn-xs btn-info"><i class="fa fa-cloud-download-alt"></i><?php esc_html_e( 'To Server', TTLC_TEXTDOMAIN ); ?>
				<form>
					<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ttlc_attachment_download' ); ?>">
					<input type="hidden" name="action" value="ttlc/attachment/download">
					<input type="hidden" name="url" value="<?php echo esc_url( $external_url ); ?>">
					<input type="hidden" name="title" value="<?php echo esc_attr( $external_title ); ?>">
					<input type="hidden" name="md5" value="<?php echo esc_attr( $external_md5 ); ?>">
					<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $local_ticket_id ); ?>">
					<input type="hidden" name="external_id" value="<?php echo esc_attr( $external_id ); ?>">
				</form>
			</a>
			<?php } else { ?>
			<a href="<?php echo esc_url( $attachment->url ); ?>" download target="_blank" title="<?php esc_html_e( 'Download', TTLC_TEXTDOMAIN ); ?>" class="ttlc__attachments-load btn btn-xs btn-info"><?php esc_html_e( 'Download', TTLC_TEXTDOMAIN ); ?></a>
			<?php } ?>
		</div>
	</div>
</li>
