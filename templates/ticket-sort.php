<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	if ( isset( $_GET['order'] ) && in_array( $_GET['order'], array('ASC', 'DESC') ) ) {
		$order = sanitize_text_field( $_GET['order'] );
		update_option( 'ttlc_responses_order', $order );
	} else {
		$order = get_option( 'ttlc_responses_order', 'ASC' );
	}

?>								
								<div class="ttlc__tickets-sort"><span><?php esc_html_e( 'Sort', TTLC_TEXTDOMAIN ); ?></span> 
									<div class="btn-group">
										<a href="<?php echo esc_url( add_query_arg( 'order', 'ASC', remove_query_arg( 'load_more') ) ); ?>" title="<?php esc_attr_e( 'Recent replies at the bottom', TTLC_TEXTDOMAIN ); ?>" class="btn btn-default <?php esc_attr_e( $order === 'ASC' ? 'active disabled' : '' ); ?>"><i class="fa fa-chevron-down"></i></a>
										<a href="<?php echo esc_url( add_query_arg( 'order', 'DESC', remove_query_arg( 'load_more') ) ); ?>" title="<?php esc_attr_e( 'Recent replies at the top', TTLC_TEXTDOMAIN ); ?>" class="btn btn-default <?php esc_attr_e( $order === 'DESC' ? 'active disabled' : '' ); ?>"><i class="fa fa-chevron-up"></i></a>
									</div>

								</div>
