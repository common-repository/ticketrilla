<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$modal_id = $data['modal_id'];	
	$newsletter = $data['newsletter'];
?>
<div class="modal <?php if ( $newsletter->status == 'new' ) { ?>new-newsletter-modal<?php } ?> fade" id="<?php echo esc_attr( $modal_id ); ?>" tabindex="-1" role="dialog" aria-hidden="true" data-newsletter-id="<?php echo esc_attr( $newsletter->id ); ?>" data-nonce="<?php echo wp_create_nonce( 'read_newsletter_' . $newsletter->id ); ?>">
	<div class="modal-dialog" role="document">
		<div class="modal-content collapse fade in">
			<div class="modal-header">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><?php echo esc_html( $newsletter->product ); ?></h4>
			</div>

			<div class="modal-body">
				<h4><?php echo esc_html( '#' . $newsletter->external_id . '. ' . $newsletter->title ); ?></h4>
				<?php echo wp_kses_post( $newsletter->content ); ?>
			</div>
			<div class="modal-footer">
				<button type="button" data-bs-dismiss="modal" class="btn btn-default"><?php esc_html_e( 'Close', TTLC_TEXTDOMAIN ); ?></button>
			<?php
				$trash_nonce = wp_create_nonce( 'trash_newsletter_' . $newsletter->id );
			?>
			    	<a href="<?php echo esc_url( add_query_arg( array(
			        		'id' => $newsletter->id,
			        		'action' => 'ttlc/newsletter/trash',
				            '_wpnonce' => $trash_nonce,
			            ) ) ); ?>" class="btn btn-dark ttlc-newsletter-trash ttlc-newsletter-archive-btn" data-bs-dismiss="modal"><?php esc_html_e( 'Delete', TTLC_TEXTDOMAIN ); ?></a>

			</div>
		</div>
	</div>
</div>
