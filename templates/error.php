<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$message = $data['message'];
	$product = $data['product'];
?>
<div id="ttlc-container" class="ttlc ttlc__error">
	<div class="ttlc-wrapper">

		<div class="ttlc__content">
			<h1><?php esc_html_e( 'Error!', TTLC_TEXTDOMAIN ); ?></h1>
			<p><?php echo esc_html( $message ); ?></p>
			<p>
				<a class="btn btn-default" href="<?php echo esc_url( $this->get_url( 'main' ) ); ?>"><?php esc_html_e( 'Main Page', TTLC_TEXTDOMAIN ); ?></a> <a class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#ttlc-modal-product-<?php echo esc_attr( $product->slug ); ?>"><?php esc_html_e( 'Settings', TTLC_TEXTDOMAIN ); ?></a>
				<?php $this->render_template( 'product-settings', array('product' => $product, 'product_uniqid' => uniqid()) ); ?>
			</p>
		</div>

	</div>
</div>