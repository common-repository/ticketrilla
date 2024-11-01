<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$product = $data['product'];
	$server_products = empty( $data['server_products'] ) ? array($product) : $data['server_products'];
	$product_uniqid = $data['product_uniqid'];
	$common_modal_id = 'ttlc-product-common-' . $product_uniqid;
	$server_tab_id = 'ttlc-product-server-' . $product_uniqid;;
	$this->render_template( 'product-settings-header', $data );

?>

		<div id="<?php echo esc_attr( $common_modal_id ); ?>" class="modal-content modal-common collapse fade in">
			<div class="modal-header">
				<button type="button" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', TTLC_TEXTDOMAIN ); ?>" class="close"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><?php esc_html_e( 'Product Settings', TTLC_TEXTDOMAIN ); ?></h4>
			</div>

			<div class="modal-body">
				<div class="form-horizontal">
					<div class="form-group">
						<label for="<?php echo esc_attr( $common_modal_id ); ?>-server" class="col-md-3 control-label"><?php esc_html_e( 'Server', TTLC_TEXTDOMAIN ); ?></label>
						<div class="col-md-9">
							<div class="input-group">
								<input disabled name="server" id="<?php echo esc_attr( $common_modal_id ); ?>-server" type="text" placeholder="<?php esc_attr_e( 'Enter Server Address', TTLC_TEXTDOMAIN ); ?>" aria-label="..." value="<?php echo isset( $product->server ) ? esc_attr( $product->server ) : ''; ?>" class="form-control">
								<div class="input-group-btn"><a href="#<?php echo esc_attr( $server_tab_id ); ?>" class="btn btn-info ttlc-modal-nav"><?php esc_html_e( 'Change', TTLC_TEXTDOMAIN ); ?></a></div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="<?php echo esc_attr( $common_modal_id ); ?>-product" class="col-md-3 control-label"><?php esc_html_e( 'Product', TTLC_TEXTDOMAIN ); ?></label>
						<div class="col-md-9">
						<?php $selected_product = $product_uniqid . '-' . ( empty( $product->slug ) ? $server_products[0]->slug : $product->slug ); ?>
						<select <?php echo count( $server_products ) > 1 ? '' : 'disabled'; ?> name="product" id="<?php echo esc_attr( $common_modal_id ); ?>-product" class="form-control ttlc-product-select" value="<?php echo esc_attr( $selected_product ); ?>">
						<?php
							foreach( $server_products as $server_product ) {
								$server_product_uniqid =	$product_uniqid . '-' . $server_product->slug;
						?>
							<option value="<?php echo esc_attr( $server_product_uniqid ); ?>" <?php esc_html_e( $server_product_uniqid === $selected_product ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $server_product->title ); ?></option>
						<?php } ?>
						</select>
						<?php
							if ( $product->has_errors( 'id' ) ) {
								foreach ( $product->get_errors( 'id' ) as $error_message ) {
									echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
								}
							}
						?>
						</div>
					</div>
				</div>

				<div class="ttlc-products-fields">
				<?php
					foreach( $server_products as $server_product ) {
						$server_product_uniqid =	$product_uniqid . '-' . $server_product->slug;
						$login_tab_id = 'ttlc-product-login-' . $server_product_uniqid;
						$registration_tab_id = 'ttlc-product-registration-' . $server_product_uniqid;
					?>
					<div id="<?php echo esc_attr( $server_product_uniqid ); ?>" class="ttlc-product-fields<?php esc_attr_e( $server_product_uniqid !== $selected_product ? ' collapse' : ''); ?>">
					<?php if ( empty( $product->id ) ) { ?>
						<div class="modal-tabs ttlc-tabs fade in" role="tablist">
							<a href="#<?php echo esc_attr( $login_tab_id ); ?>" class="active" role="tab" aria-controls="<?php echo esc_attr( $login_tab_id ); ?>" aria-selected="true"><?php esc_html_e( 'Login', TTLC_TEXTDOMAIN ); ?></a>
							<a href="#<?php echo ! empty( $server_product->registration ) ? esc_attr( $registration_tab_id ) : ''; ?>" class="<?php echo empty( $server_product->registration ) ? 'disabled' : ''; ?>" <?php echo empty( $server_product->registration ) ? 'title="' . esc_html__( 'Registration is closed', TTLC_TEXTDOMAIN ) . '"' : ''; ?> role="tab" aria-controls="<?php echo esc_attr( $registration_tab_id ); ?>" aria-selected="false"><?php esc_html_e( 'Registration', TTLC_TEXTDOMAIN ); ?></a>
						</div>
					<?php } ?>
						<div class="tab-content">
			
							<!--Login Tab-->
			
							<div class="tab-pane fade in active" id="<?php echo esc_attr( $login_tab_id ); ?>" role="tabpanel">
							<?php
								$login_tab_data = $data;
								$login_tab_data['form'] = 'login';
								$login_tab_data['product'] = $server_product;
								$login_tab_data['server_product_uniqid'] = $server_product_uniqid;
								$this->render_template( 'product-settings-form', $login_tab_data );
							?>
							</div>
							<?php if ( isset( $server_product->registration ) ) { ?>
			
							<!--Registration Tab-->
			
							<div class="tab-pane fade" id="<?php echo esc_attr( $registration_tab_id ); ?>" role="tabpanel">
							<?php
								$registration_tab_data = $data;
								$registration_tab_data['form'] = 'registration';
								$registration_tab_data['product'] = $server_product;
								$registration_tab_data['server_product_uniqid'] = $server_product_uniqid;
								$this->render_template( 'product-settings-form', $registration_tab_data );
							?>
							</div>
							<?php } ?>
			
						</div>

					</div>
				<?php } ?>
				</div>

			</div>
		<?php $this->render_template( 'product-settings-common-footer', $data ); ?>
		</div>
		
		<?php $this->render_template( 'product-settings-server-modal', $data ); ?>

		<?php $this->render_template( 'product-settings-password-reset', $data ); ?>
	
	</div>
</div>
