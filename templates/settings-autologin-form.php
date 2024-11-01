<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$autologin = $data['autologin'];
	$labels = $autologin->attributes();

?>
							<form id="ttlc-autologin-settings" class="ttlc__settings-inner ttlc-settings">
								<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ttlc_settings' ); ?>">
								<input type="hidden" name="action" value="ttlc/settings">
								<input type="hidden" name="section" value="autologin">
								<div class="ttlc__settings-inner-header">
									<h4><?php esc_html_e( 'Autologin', TTLC_TEXTDOMAIN ); ?></h4>
								</div>
								<div class="ttlc__settings-inner-body">
									<div class="row">
										<div class="col-md-6 col-lg-3">
											<div class="form-group">
												<label><?php echo esc_html( $labels['role'] ); ?></label>
												<div class="input-group">
													<select name="role" id="ttlc__autologin-role" class="form-control">
													<?php foreach ( get_editable_roles() as $role_name => $role_info ) { ?>
													<?php $selected = $role_name === $autologin->role ? 'selected="selected"' : ''; ?>
														<option value="<?php echo esc_attr( $role_name ); ?>" <?php esc_html_e( $selected ); ?>><?php echo esc_html( translate_user_role( $role_info['name'] ) ); ?></option>
													<?php } ?>
													</select>													
												</div>
												<span class="help-block"><?php esc_html_e( 'Role of autologin user', TTLC_TEXTDOMAIN ) ?></span>
											</div>
										</div>
										<div class="col-md-6 col-lg-3">
											<div class="form-group">
												<label><?php echo esc_html( $labels['days'] ); ?></label>
												<div class="input-group">
													<span class="input-group-addon"><?php esc_html_e( 'days', TTLC_TEXTDOMAIN ); ?></span><input name="days" id="ttlc__autologin-days" type="number" min="1" value="<?php echo esc_attr( $autologin->days ); ?>" class="form-control">
												</div>
												<span class="help-block"><?php esc_html_e( 'Duration of accounts', TTLC_TEXTDOMAIN ) ?></span>
											</div>
										</div>
										<div class="col-md-6 col-lg-3">
											<div class="form-group">
												<label><?php echo esc_html( $labels['logins'] ); ?></label>
												<div class="input-group">
													<span class="input-group-addon"><?php esc_html_e( 'num.', TTLC_TEXTDOMAIN ); ?></span><input name="logins" id="ttlc__autologin-number" type="number" min="1" value="<?php echo esc_attr( $autologin->logins ); ?>" class="form-control">
												</div>
												<span class="help-block"><?php esc_html_e( 'Number of logins', TTLC_TEXTDOMAIN ) ?></span>
											</div>
										</div>
										<div class="col-md-6 col-lg-3">
											<div class="form-group">
												<label><?php echo esc_html( $labels['reassign_user'] ); ?></label>
												<div class="input-group">
													<select name="reassign_user" id="ttlc__autologin-reassign_user" class="form-control">
													<?php foreach ( $autologin->get_admins() as $admin ) { $admin_data = get_userdata( $admin->ID ); ?>
													<?php $selected = $admin_data->ID == $autologin->reassign_user ? 'selected="selected"' : ''; ?>
														<option value="<?php echo esc_attr( $admin_data->ID ); ?>" <?php esc_html_e( $selected ); ?>><?php echo esc_html( sprintf('%s (%s)', $admin_data->display_name, $admin_data->user_login ) ); ?></option>
													<?php } ?>
													</select>													
												</div>
												<span class="help-block"><?php esc_html_e( 'Account to reassign data after removal', TTLC_TEXTDOMAIN ); ?></span>
											</div>
										</div>
										<?php ?>
									</div>
								</div>
								<?php $this->render_template( 'settings-section-footer', array('model' => $autologin) ); ?>
							</form>
