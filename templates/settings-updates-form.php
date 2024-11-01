<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$updates = $data['updates'];
	$labels = $updates->attributes();

?>
							<form id="ttlc-bg-updates-settings" class="ttlc__settings-inner ttlc-settings">
								<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ttlc_settings' ); ?>">
								<input type="hidden" name="action" value="ttlc/settings">
								<input type="hidden" name="section" value="updates">
								<div class="ttlc__settings-inner-header">
									<h4><?php esc_html_e( 'Background Updates', TTLC_TEXTDOMAIN ); ?></h4>
								</div>
								<div class="ttlc__settings-inner-body">
									<div class="row">
										<div class="col-md-12 col-lg-6">
											<div class="form-group">
												<div class="checkbox">
													<input name="on" id="ttlc__bg-updates" type="checkbox" <?php esc_html_e( $updates->on ? 'checked' : '' ); ?> value="y">
													<label for="ttlc__bg-updates"> <?php echo esc_html( $labels['on'] ); ?></label>
												</div><span class="help-block"><?php esc_html_e( 'Update Ticketrilla Data in Background', TTLC_TEXTDOMAIN ); ?></span>
											</div>
										</div>
										<div class="col-md-6 col-lg-6">
											<div class="form-group">
												<label><?php echo esc_html( $labels['interval'] ); ?></label>
												<div class="input-group"><span class="input-group-addon"><?php esc_html_e( 'min.', TTLC_TEXTDOMAIN ); ?></span>
													<input name="interval" id="ttlc__bg-updates-interval" type="number" min="1" value="<?php echo esc_attr( $updates->interval ); ?>" class="form-control">
												</div><span class="help-block"><?php esc_html_e( 'Minimum Updates Interval', TTLC_TEXTDOMAIN ); ?></span>
											</div>
										</div>
									</div>
								</div>
								<?php $this->render_template( 'settings-section-footer', array('model' => $updates ) ); ?>
							</form>
