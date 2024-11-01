<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

?>

<div id="ttlc-container" class="ttlc">
	<div class="ttlc-wrapper">
		<div class="ttlc__header">
			<div class="ttlc__header-inner">
				<div class="col-left">
					<h2 class="h4 wp-heading-inline"><?php esc_html_e( 'Ticketrilla: Client', TTLC_TEXTDOMAIN ); ?></h2><a href="<?php echo esc_url( $this->get_url( 'settings' ) ); ?>" class="btn btn-xs btn-dark"><?php esc_html_e( 'Settings', TTLC_TEXTDOMAIN ); ?></a>
				</div>
				<div class="col-right"><?php $this->render_template( 'breadcrumbs' ); ?></div>
			</div>
			<hr class="clearfix">
