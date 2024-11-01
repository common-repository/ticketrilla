<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$data = $this->get_data();
	$product = $data['product'];

?>

<div id="ttlc-modal-product-<?php echo esc_attr( $product->slug ); ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div role="document" class="modal-dialog">
