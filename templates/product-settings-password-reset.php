<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$product = $data['product'];
	$password_reset = isset( $data['password_reset'] ) ? $data['password_reset'] : new TTLC_Password_Reset;
	$product_uniqid = $data['product_uniqid'];
	$form_id = 'ttlc-product-password-reset-' . $product_uniqid;
	$password_reset_modal_id = 'ttlc-product-password-reset-' . $product_uniqid;
	$common_modal_id = 'ttlc-product-common-' . $product_uniqid;

?>

		<div id="<?php echo esc_attr( $password_reset_modal_id ); ?>-email_login" class="modal-content modal-password-reset collapse fade">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><?php esc_html_e( 'Access Recovery', TTLC_TEXTDOMAIN ); ?></h4>
			</div>

			<form class="ttlc-product-settings-password-reset form-horizontal">
				<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ttlc-product-password-reset-' . $product->slug ); ?>">
				<input type="hidden" name="_product_uniqid" value="<?php echo esc_attr( $product_uniqid ); ?>">
				<input type="hidden" name="slug" value="<?php echo esc_attr( $product->slug ); ?>">
				<input type="hidden" name="server" value="<?php echo esc_attr( $product->server ); ?>">
				<div class="modal-body">
					<div class="form-group <?php esc_attr_e( $password_reset->has_errors( 'email_login' ) ? 'has-error' : '' ); ?>">
						<label class="col-md-4 control-label" for="<?php echo esc_attr( $form_id ); ?>-email_login"><?php esc_html_e( 'Your E-mail or Login', TTLC_TEXTDOMAIN ); ?></label>
						<div class="col-md-8">
							<input id="<?php echo esc_attr( $form_id ); ?>-email_login" name="email_login" type="text" placeholder="<?php esc_attr_e( 'Enter E-mail or Login', TTLC_TEXTDOMAIN ); ?>" class="form-control" value="<?php echo esc_attr( $password_reset->email_login ); ?>">
						<?php
							if ( $password_reset->has_errors( 'email_login' ) ) {
								foreach ( $password_reset->get_errors( 'email_login' ) as $error_message ) {
									echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
								}
							}
						?>
						</div>
					</div>
					<?php
						if ( $password_reset->has_errors( '_global' ) ) {
					?>
					<div class="form-group has-error">
						<div class="col-md-8 col-md-offset-4">
					<?php
							foreach ( $password_reset->get_errors( '_global' ) as $error_message ) {
								echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
							}
					?>
						</div>
					</div>
					<?php
						}
					?>
				</div>
				<div class="modal-footer">
					<a href="#<?php echo esc_attr( $common_modal_id ); ?>" class="btn btn-default ttlc-modal-nav"><?php esc_html_e( 'Back', TTLC_TEXTDOMAIN ); ?></a>
					<button type="submit" class="btn btn-dark"><?php esc_html_e( 'Send', TTLC_TEXTDOMAIN ); ?></button>
				</div>
			</form>
		</div>

		<div id="<?php echo esc_attr( $password_reset_modal_id ); ?>-secure_key" class="modal-content modal-password-reset collapse fade">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><?php esc_html_e( 'Access Recovery', TTLC_TEXTDOMAIN ); ?></h4>
			</div>

			<form class="ttlc-product-settings-password-reset form-horizontal">
				<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ttlc-product-password-reset-' . $product->slug ); ?>">
				<input type="hidden" name="_product_uniqid" value="<?php echo esc_attr( $product_uniqid ); ?>">
				<input name="email_login" type="hidden" value="<?php echo esc_attr( $password_reset->email_login ); ?>">
				<input type="hidden" name="slug" value="<?php echo esc_attr( $product->slug ); ?>">
				<input type="hidden" name="server" value="<?php echo esc_attr( $product->server ); ?>">
				<div class="modal-body">
					<div class="form-group <?php esc_attr_e( $password_reset->has_errors( 'secure_key' ) ? 'has-error' : '' ); ?>">
						<label class="col-md-4 control-label" for="<?php echo esc_attr( $form_id ); ?>-secure_key"><?php esc_html_e( 'Enter Secure Key', TTLC_TEXTDOMAIN ); ?></label>
						<div class="col-md-8">
							<input id="<?php echo esc_attr( $form_id ); ?>-secure_key" name="secure_key" type="text" placeholder="<?php esc_attr_e( 'Enter Secure Key', TTLC_TEXTDOMAIN ); ?>" class="form-control"><small class="text-help text-muted"><?php esc_html_e( 'Secure key was send in to your e-mail', TTLC_TEXTDOMAIN ); ?></small>
						<?php
							if ( $password_reset->has_errors( 'secure_key' ) ) {
								foreach ( $password_reset->get_errors( 'secure_key' ) as $error_message ) {
									echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
								}
							}
						?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-8 col-md-offset-4">
							<div class="checkbox">
								<input name="autosubstitution" id="<?php echo esc_attr( $form_id ); ?>-autosubstitution" type="checkbox" value="y">
								<label for="<?php echo esc_attr( $form_id ); ?>-autosubstitution"><?php esc_html_e( 'Auto-Substitution', TTLC_TEXTDOMAIN ); ?></label><small class="help-block"><?php esc_html_e( 'If checked, password is automatically inserted', TTLC_TEXTDOMAIN ); ?></small>
							</div>
						</div>
					</div>
					<?php
						if ( $password_reset->has_errors( '_global' ) ) {
					?>
					<div class="form-group has-error">
						<div class="col-md-8 col-md-offset-4">
					<?php
							foreach ( $password_reset->get_errors( '_global' ) as $error_message ) {
								echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
							}
					?>
						</div>
					</div>
					<?php
						}
					?>
				</div>
				<div class="modal-footer">
					<a href="#<?php echo esc_attr( $password_reset_modal_id ); ?>-email_login" class="btn btn-default ttlc-modal-nav"><?php esc_html_e( 'Back', TTLC_TEXTDOMAIN ); ?></a>
					<button type="submit" class="btn btn-dark"><?php esc_html_e( 'Send', TTLC_TEXTDOMAIN ); ?></button>
				</div>
			</form>
		</div>

		<div id="<?php echo esc_attr( $password_reset_modal_id ); ?>-success" class="modal-content modal-password-reset collapse fade">
			<div class="modal-header">
				<button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><?php esc_html_e( 'Access Recovery', TTLC_TEXTDOMAIN ); ?></h4>
			</div>
			<form class="form-horizontal">
				<div class="modal-body">
					<div class="text-center">
						<div class="text-success"><?php esc_html_e( 'Your access has been restored', TTLC_TEXTDOMAIN ); ?></div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="text-center">
						<a href="#<?php echo esc_attr( $common_modal_id ); ?>" class="btn btn-default ttlc-modal-nav"><?php esc_html_e( 'Back', TTLC_TEXTDOMAIN ); ?></a>
					</div>
				</div>
			</form>
		</div>

