<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

?>

<li class="ttlc-attachment-loading-template hidden"><span class="ttlc__attachments-icon"><i class="fa fa-sync-alt fa-spin"></i></span>
	<div class="ttlc__attachments-info">
		<div class="ttlc__attachments-name"><?php esc_html_e( 'Loading', TTLC_TEXTDOMAIN ); ?>...</div>
		<div class="progress">
			<div role="progressbar" class="progress-bar progress-bar-info progress-bar-striped"></div>
		</div>
	</div>
</li>
<li class="ttlc__attachments-error ttlc-attachment-error-template hidden"><span class="ttlc__attachments-icon"><i class="fa fa-times"></i></span>
	<div class="ttlc__attachments-info">
		<div class="ttlc__attachments-name"><?php esc_html_e( 'Error', TTLC_TEXTDOMAIN ); ?></div>
		<div class="ttlc__attachments-size"><span></span><a href="#" title="<?php esc_attr_e( 'Reload this attachment', TTLC_TEXTDOMAIN ); ?>" class="btn btn-xs btn-danger ttlc-attachment-reload"><i class="fa fa-sync-alt"></i> <?php esc_html_e( 'Reload', TTLC_TEXTDOMAIN ); ?></a></div>
	</div>
</li>
