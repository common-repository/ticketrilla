<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$data['save_disabled'] = true;
	$product = $data['product'];
	$product_uniqid = $data['product_uniqid'];
	$show_server = isset( $data['show_server'] ) ? true : false;
	$server_tab_id = 'ttlc-product-server-' . $product_uniqid;;

?>
		<div id="<?php echo esc_attr( $server_tab_id ); ?>" class="modal-content modal-server collapse fade <?php esc_attr_e( $show_server ? 'in' : '' ); ?>">
			<div class="modal-header">
				<button type="button" data-bs-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><?php esc_html_e( 'Product Settings', TTLC_TEXTDOMAIN ); ?></h4>
			</div>

			<div class="modal-body">
				<form class="form-horizontal ttlc-product-server-check-form">
					<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ttlc_server_check' ); ?>">
					<input type="hidden" name="_product_uniqid" value="<?php echo esc_attr( $product_uniqid ); ?>">
				<?php if ( isset( $product->id ) ) { ?>
						<input name="id" type="hidden" value="<?php echo esc_attr( $product->id ); ?>">
				<?php } ?>
				<?php if ( isset( $product->slug ) && $product->slug !== 'custom' ) { ?>
						<input name="slug" type="hidden" value="<?php echo esc_attr( $product->slug ); ?>">
				<?php } ?>
					<div class="form-group <?php esc_attr_e( $product->has_errors( 'server' ) ? 'has-error' : '' );?>">
						<label for="ttlc-product-<?php echo esc_attr( $product_uniqid ); ?>-server" class="col-md-3 control-label"><?php esc_html_e( 'Server', TTLC_TEXTDOMAIN ); ?></label>
						<div class="col-md-9">
							<div class="input-group">
								
								<input name="server" id="ttlc-product-<?php echo esc_attr( $product_uniqid ); ?>-server" type="text" placeholder="<?php esc_attr_e( 'Enter Server Address', TTLC_TEXTDOMAIN ); ?>" aria-label="..." value="<?php echo isset( $product->server ) ? esc_url( $product->server ) : ''; ?>" class="form-control">
								<div class="input-group-btn"><a href="#" class="btn btn-info ttlc-product-server-check"><?php esc_html_e( 'Check', TTLC_TEXTDOMAIN ); ?></a></div>
							</div>
							<?php
								if ( $product->has_errors( 'server' ) ) {
									foreach ( $product->get_errors( 'server' ) as $error_message ) {
										echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
									}
								}
							?>
						</div>
					</div>
				</form>
			</div>
		<?php $this->render_template( 'product-settings-common-footer', $data ); ?>
		</div>
