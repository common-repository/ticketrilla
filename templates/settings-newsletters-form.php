<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$model = $data['newsletters'];
?>
							<form id="ttlc-newsletters-settings" class="ttlc__settings-inner ttlc-settings">
								<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'ttlc_settings' ); ?>">
								<input type="hidden" name="action" value="ttlc/settings">
								<input type="hidden" name="section" value="newsletters">
								<div class="ttlc__settings-inner-header">
									<h4><?php esc_html_e( 'Newsletters', TTLC_TEXTDOMAIN ); ?></h4>
								</div>
								<div class="ttlc__settings-inner-body">
								<?php foreach( $model->products as $product ) { ?>
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<div class="checkbox">
													<input name="<?php echo esc_attr( $product->id ); ?>" id="ttlc__newsletters_<?php echo esc_attr( $product->id ); ?>" type="checkbox" <?php esc_html_e( $product->newsletters ? 'checked' : '' ); ?> value="y">
													<label for="ttlc__newsletters_<?php echo esc_attr( $product->id ); ?>"> <?php echo esc_html( $product->title ); ?></label>
												</div><span class="help-block"><?php esc_html_e( 'Receive Newsletters for this Product', TTLC_TEXTDOMAIN ); ?></span>
											</div>
										</div>
									</div>
								<?php } ?>
								</div>
								<?php $this->render_template( 'settings-section-footer', array('model' => $model ) ); ?>
							</form>
