<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$product = $data['product'];
	$save_disabled = isset( $data['save_disabled'] ) ? true : false;
	$filter_key = empty( $data['filter_key'] ) ? false : $data['filter_key'];

?>
					<div class="modal-footer">
			        <?php
				        if ( ! empty( $product->id ) ) {
					        if ( isset( $filter_key ) && $filter_key === 'archive' ) {
						    	$untrash_nonce = wp_create_nonce( 'untrash_post_' . $product->id );
					    ?>
			            	<a href="<?php echo esc_url( add_query_arg( array(
				            		'id' => $product->id,
				            		'action' => 'ttlc/product/untrash',
						            '_wpnonce' => $untrash_nonce,
					            ) ) ); ?>" class="btn btn-default ttlc-product-untrash ttlc-product-archive-btn" data-bs-dismiss="modal"><i class="fa fa-file-archive"></i> <?php esc_html_e( 'Unarchive', TTLC_TEXTDOMAIN ); ?></a>
					    <?php
					        } else {
						    	$trash_nonce = wp_create_nonce( 'trash_post_' . $product->id );
					    ?>
			            	<a href="<?php echo esc_url( add_query_arg( array(
				            		'id' => $product->id,
				            		'action' => 'ttlc/product/trash',
						            '_wpnonce' => $trash_nonce,
					            ) ) ); ?>" class="btn btn-default ttlc-product-trash ttlc-product-archive-btn" data-bs-dismiss="modal"><i class="fa fa-file-archive"></i> <?php esc_html_e( 'Archive', TTLC_TEXTDOMAIN ); ?></a>
					    <?php
						    }
						}
				    ?>

						<button type="button" data-bs-dismiss="modal" class="btn btn-default"><?php esc_html_e( 'Close', TTLC_TEXTDOMAIN ); ?></button>
						<button type="submit" class="btn btn-dark ttlc-product-save-btn <?php esc_attr_e( $save_disabled ? 'disabled' : '' ); ?>"><?php esc_html_e( 'Save Changes', TTLC_TEXTDOMAIN ); ?></button>
					</div>