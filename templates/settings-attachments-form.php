<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$attachments = $data['attachments'];
	$labels = $attachments->attributes();

?>
							<form id="ttlc-attachment-settings" class="ttlc__settings-inner ttlc-settings">
								<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ttlc_settings' ); ?>">
								<input type="hidden" name="action" value="ttlc/settings">
								<input type="hidden" name="section" value="attachments">
								<div class="ttlc__settings-inner-header">
									<h4><?php esc_html_e( 'Attachments', TTLC_TEXTDOMAIN ); ?></h4>
								</div>
								<div class="ttlc__settings-inner-body">
									<div class="row">
										<div class="col-md-6 col-lg-4">
											<div class="form-group">
												<label><?php echo esc_html( $labels['size'] ); ?></label>
												<div class="input-group"><span class="input-group-addon"><?php esc_html_e( 'MB', TTLC_TEXTDOMAIN ); ?></span>
													<input name="size" id="ttlc__attachment-max-size" type="number" min="1" value="<?php echo esc_attr( $attachments->size ); ?>" class="form-control">
												</div><span class="help-block"><?php esc_html_e( 'Maximum attachments size', TTLC_TEXTDOMAIN ); ?></span>
											</div>
										</div>
										<div class="col-md-6 col-lg-4">
											<div class="form-group">
												<label><?php echo esc_html( $labels['time'] ); ?></label>
												<div class="input-group"><span class="input-group-addon"><?php esc_html_e( 'sec.', TTLC_TEXTDOMAIN ); ?></span>
													<input name="time" id="ttlc__attachment-max-time" type="number" min="1" value="<?php echo esc_attr( $attachments->time ); ?>" class="form-control">
												</div><span class="help-block"><?php esc_html_e( 'Maximum attachments loading time', TTLC_TEXTDOMAIN ); ?></span>
											</div>
										</div>
										<div class="col-md-12 col-lg-4">
											<div class="form-group">
												<div class="checkbox">
													<input name="autoload" id="ttlc__attachment-autoload" type="checkbox" <?php esc_html_e( $attachments->autoload ? 'checked' : '' ); ?> value="y">
													<label for="ttlc__attachment-autoload"> <?php echo esc_html( $labels['autoload'] ); ?></label>
												</div><span class="help-block"><?php esc_html_e( 'Autoload attachments to server on ticket response getting', TTLC_TEXTDOMAIN ); ?></span>
											</div>
										</div>
									</div>
								</div>
								<?php $this->render_template( 'settings-section-footer', array('model' => $attachments ) ); ?>
							</form>
