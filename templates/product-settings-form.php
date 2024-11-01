<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$form = $data['form'];
	$product = $data['product'];
	$licenses = (Array)json_decode( $product->license_fields );
	$server_product_uniqid = $data['server_product_uniqid'];
	$form_id = 'ttlc-product-' . $form . '-' . $server_product_uniqid;
	$server_tab_id = 'ttlc-product-server-' . $server_product_uniqid;
	$password_reset_modal_id = 'ttlc-product-password-reset-' . $data['product_uniqid'];

?>

<form class="form-horizontal ttlc-product-settings-form">
	<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ttlc_product_save_' . $product->slug ); ?>">
	<input type="hidden" name="_product_uniqid" value="<?php echo esc_attr( $data['product_uniqid'] ); ?>">
<?php if ( isset( $product->id) ) { ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $product->id ); ?>">
<?php } ?>
	<input type="hidden" name="external_id" value="<?php echo esc_attr( $product->external_id ); ?>">
	<input type="hidden" name="form" value="<?php echo esc_attr( $form ); ?>">
	<input type="hidden" name="title" value="<?php echo esc_attr( $product->title ); ?>">
	<input type="hidden" name="slug" value="<?php echo esc_attr( $product->slug ); ?>">
	<input type="hidden" name="server" value="<?php echo esc_attr( $product->server ); ?>">
	<input type="hidden" name="type" value="<?php echo esc_attr( $product->type ); ?>">
	<input type="hidden" name="content" value="<?php echo esc_attr( $product->content ); ?>">
	<input type="hidden" name="thumbnail" value="<?php echo esc_attr( $product->thumbnail ); ?>">
	<input type="hidden" name="author" value="<?php echo esc_attr( $product->author ); ?>">
<?php if ( isset( $product->author_uri ) ) { ?>
	<input type="hidden" name="author_uri" value="<?php echo esc_attr( $product->author_uri ); ?>">
<?php } ?>
	<input type="hidden" name="manual" value="<?php echo esc_attr( $product->manual ); ?>">
	<input type="hidden" name="service_terms" value="<?php echo esc_attr( $product->service_terms ); ?>">
	<input type="hidden" name="privacy_statement" value="<?php echo esc_attr( $product->privacy_statement ); ?>">
	<input type="hidden" name="license_fields" value="<?php echo esc_attr( $product->license_fields ); ?>">

	<div class="form-group <?php esc_attr_e( $product->has_errors( 'login' ) ? 'has-error' : '' ); ?>">
		<label for="<?php echo esc_attr( $form_id ); ?>-login" class="col-md-3 control-label"><?php esc_html_e( 'Login', TTLC_TEXTDOMAIN ); ?></label>
		<div class="col-md-9">
			<input autocomplete="new-password" name="login" id="<?php echo esc_attr( $form_id ); ?>-login" type="text" placeholder="<?php esc_attr_e( 'Enter Login', TTLC_TEXTDOMAIN ); ?>" class="form-control" value="<?php esc_attr_e( isset( $product->login ) ? $product->login : '' ); ?>">
			<?php
				if ( $product->has_errors( 'login' ) ) {
					foreach ( $product->get_errors( 'login' ) as $error_message ) {
						echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
					}
				}
			?>
		</div>
	</div>

	<?php if ( $form === 'registration' ) { ?>
	<div class="form-group <?php esc_attr_e( $product->has_errors( 'email' ) ? 'has-error' : '' );?>">
		<label for="<?php echo esc_attr( $form_id ); ?>-email" class="col-md-3 control-label"><?php esc_html_e( 'E-mail', TTLC_TEXTDOMAIN ); ?></label>
		<div class="col-md-9">
			<input autocomplete="new-password" name="email" id="<?php echo esc_attr( $form_id ); ?>-email" type="text" placeholder="<?php esc_attr_e( 'Enter E-mail', TTLC_TEXTDOMAIN ); ?>" class="form-control" value="<?php esc_attr_e( isset( $product->email ) ? $product->email : '' ); ?>">
			<?php
				if ( $product->has_errors( 'email' ) ) {
					foreach ( $product->get_errors( 'email' ) as $error_message ) {
						echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
					}
				}
			?>
		</div>
	</div>	
	<?php } ?>
	<?php if ( $form === 'registration' || isset( $product->id ) ) { ?>
	<div class="form-group <?php esc_attr_e( $product->has_errors( 'name' ) ? 'has-error' : '' );?>">
		<label for="<?php echo esc_attr( $form_id ); ?>-name" class="col-md-3 control-label"><?php esc_html_e( 'Name', TTLC_TEXTDOMAIN ); ?></label>
		<div class="col-md-9">
			<input autocomplete="new-password" name="name" id="<?php echo esc_attr( $form_id ); ?>-name" type="text" placeholder="<?php esc_attr_e( 'Enter Name', TTLC_TEXTDOMAIN ); ?>" class="form-control" value="<?php esc_attr_e( isset( $product->name ) ? $product->name : '' ); ?>">
			<?php
				if ( $product->has_errors( 'name' ) ) {
					foreach ( $product->get_errors( 'name' ) as $error_message ) {
						echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
					}
				}
			?>
		</div>
	</div>	
	<?php } ?>
	<div class="form-group <?php esc_attr_e( $product->has_errors( 'password' ) ? 'has-error' : '' );?>">
		<label for="<?php echo esc_attr( $form_id ); ?>-password" class="col-md-3 control-label"><?php esc_html_e( 'Password', TTLC_TEXTDOMAIN ); ?></label>
		<div class="col-md-9">
			<div class="input-group">
				<input autocomplete="new-password" name="password" id="<?php echo esc_attr( $form_id ); ?>-password" type="password" placeholder="<?php esc_attr_e( 'Enter Password', TTLC_TEXTDOMAIN ); ?>" class="form-control" value="<?php esc_attr_e( isset( $product->password ) ? $product->password : '' ); ?>">
				<div class="input-group-btn"><a href="#" class="ttlc-password-toggle btn btn-default"><i class="fa fa-eye-slash"></i></a></div>
			</div>
			<?php
				if ( $product->has_errors( 'password' ) ) {
					foreach ( $product->get_errors( 'password' ) as $error_message ) {
						echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
					}
				}
			?>
		</div>
	</div>
	<div class="form-group <?php esc_attr_e( $product->has_errors( 'license' ) ? 'has-error' : '' );?>">
		<label for="<?php echo esc_attr( $form_id ); ?>-license" class="col-md-3 control-label"><?php esc_html_e( 'License', TTLC_TEXTDOMAIN ); ?></label>
		<div class="col-md-9">
			<select name="license" id="<?php echo esc_attr( $form_id ); ?>-license" class="form-control ttlc-license-select" value="<?php esc_attr_e( isset( $product->license ) ? $product->license : '' ); ?>">
	  		<?php
		  		$selected_license = isset( $product->license ) ? $product->license : key($licenses);
		  		foreach ( $licenses as $license_type => $license_data ) {
			?>
				<option value="<?php echo esc_attr( $license_type ); ?>" <?php esc_html_e( $selected_license === $license_type ? 'selected="selected"' : '' ); ?>><?php echo esc_html( $license_data->title ); ?></option>
			<?php } ?>
			</select>
			<?php
				if ( $product->has_errors( 'license' ) ) {
					foreach ( $product->get_errors( 'license' ) as $error_message ) {
						echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
					}
				}
			?>
		</div>
	</div>
	<div class="ttlc-license-fields">
	<?php foreach( $licenses as $license_type => $license_data ) { ?>
		<div class="ttlc-license-fields-<?php echo esc_attr( $license_type ); ?> <?php esc_html_e( $selected_license === $license_type ? '' : 'collapse' ); ?>">
		<?php
			foreach( $license_data->fields as $license_field_name => $license_field_data ) {
				$license_field_mode = false;
				if ( $form === 'login' && $license_field_data->login ) {
					$license_field_mode = $license_field_data->login;
				} elseif( $form === 'registration' && $license_field_data->register ) {
					$license_field_mode = $license_field_data->register;
				}
				if ( $license_field_mode ) {
					$license_field_uniqid = uniqid();
					$license_field_id = $form_id . '-' . $license_field_name . '-' . $license_field_uniqid;
	
					$license_field_disabled = $selected_license === $license_type ? '' : 'disabled="disabled"';
					if ( $license_field_mode === 'possible' ) {
						$license_field_checkbox_id = $form_id . '-' . $license_field_name . '-checkbox-' . $license_field_uniqid;
						$license_field_checkbox_name = $license_field_name . '-checkbox';
						$license_field_checkbox_on = ( $selected_license === $license_type && ! empty( $product->license_data[$license_field_name] ) ) || ! empty( $_POST[$license_field_checkbox_name]);
						$license_field_disabled = $license_field_checkbox_on ? '' : 'disabled="disabled"';
		?>
			<div class="form-group">
				<div class="col-md-9 col-md-offset-3">
					<div class="checkbox">
						<input type="checkbox" id="<?php echo esc_attr( $license_field_checkbox_id ); ?>" name="<?php echo esc_attr( $license_field_checkbox_name ); ?>" class="form-control ttlc-license-field-checkbox" <?php esc_html_e( $license_field_checkbox_on ? 'checked="checked"' : '' ); ?> value="<?php echo esc_attr( $license_field_id ); ?>">
						<label for="<?php echo esc_attr( $license_field_checkbox_id ); ?>"><?php esc_html_e( 'I have a license', TTLC_TEXTDOMAIN ); ?></label>
					</div>
				</div>
			</div>
		<?php
					}
		?>

			<div class="form-group <?php esc_attr_e( $selected_license === $license_type && $product->has_errors( 'license_data' ) ? 'has-error' : '' );?> <?php esc_attr_e( $license_field_mode === 'possible' && $license_field_disabled ? 'collapse' : '' ); ?>">
				<label for="<?php echo esc_attr( $license_field_id ); ?>" class="col-md-3 control-label" ><?php echo esc_html( $license_field_data->title ); ?></label>
				<div class="col-md-9">
					<input <?php echo esc_attr( $license_field_disabled ); ?> name="<?php echo esc_attr( $license_field_name ); ?>" id="<?php echo esc_attr( $license_field_id ); ?>" type="<?php echo esc_attr( $license_field_data->type ); ?>" placeholder="<?php echo esc_attr( sprintf( __( 'Enter %s', TTLC_TEXTDOMAIN ), $license_field_data->title ) ); ?>" class="form-control" value="<?php esc_attr_e( $selected_license === $license_type && isset( $product->license_data[$license_field_name] ) ? $product->license_data[$license_field_name] : '' ); ?>">
				<?php
					if ( $selected_license === $license_type && $product->has_errors( 'license_data' ) ) {
						foreach ( $product->get_errors( 'license_data' ) as $error_message ) {
							echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
						}
					}
				?>
				</div>
			</div>

		<?php
			}
		}
		?>

		</div>
	<?php } ?>
	</div>

	<div class="form-group <?php esc_attr_e( $product->has_errors( 'newsletters' ) ? 'has-error' : '' );?>">						
		<div class="col-md-9 col-md-offset-3">
			<div class="checkbox">
				<input name="newsletters" type="checkbox" id="<?php echo esc_attr( $form_id ); ?>-newsletters" <?php echo isset( $product->newsletters ) && empty( $product->newsletters ) ? '' : 'checked'; ?> value="y">
				<label for="<?php echo esc_attr( $form_id ); ?>-newsletters"> 
				<?php
					echo esc_html__( 'I want to receive newsletters', TTLC_TEXTDOMAIN );
				?>
				</label>
			</div>
			<?php
				if ( $product->has_errors( 'newsletters' ) ) {
					foreach ( $product->get_errors( 'newsletters' ) as $error_message ) {
						echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
					}
				}
			?>
		</div>
	</div>


	<?php if ( $form === 'login' ) { ?>

	<div class="form-group collapse ttlc__forgot fade in">
		<div class="col-md-9 col-md-offset-3"><small class="text-muted"> <?php esc_html_e( 'I forgot my login or password', TTLC_TEXTDOMAIN ); ?>: <a class="ttlc-modal-nav" href="#<?php echo esc_attr( $password_reset_modal_id ); ?>-email_login"><?php esc_html_e( 'remember', TTLC_TEXTDOMAIN ); ?></a></small></div>
	</div>
	
	<?php } else { ?>

	<div class="form-group <?php esc_attr_e( $product->has_errors( 'terms' ) ? 'has-error' : '' );?>">						
		<div class="col-md-9 col-md-offset-3">
			<div class="checkbox">
				<input name="terms" type="checkbox" id="<?php echo esc_attr( $form_id ); ?>-terms" <?php echo empty( $product->terms ) ? '' : 'checked'; ?> value="y">
				<label for="<?php echo esc_attr( $form_id ); ?>-terms"> 
				<?php
					printf(
						esc_html__( 'I agree to your %1$sterms of service%3$s and %2$sprivacy statement.%3$s', TTLC_TEXTDOMAIN ),
						'<a target="_blank" href="' . esc_url( $product->service_terms ) . '">',
						'<a target="_blank" href="' . esc_url( $product->privacy_statement ) . '">',
						'</a>'
					);
				?>
				</label>
			</div>
			<?php
				if ( $product->has_errors( 'terms' ) ) {
					foreach ( $product->get_errors( 'terms' ) as $error_message ) {
						echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
					}
				}
			?>
		</div>
	</div>

	<?php } ?>
	<?php
		if ( $product->has_errors( '_global' ) ) {
	?>
	<div class="form-group has-error">
		<div class="col-md-9 col-md-offset-3">
	<?php
			foreach ( $product->get_errors( '_global' ) as $error_message ) {
				echo '<div class="help-block">' . esc_html( $error_message ) . '</div>';	
			}
	?>
		</div>
	</div>
	<?php
		}
	?>
</form>					
