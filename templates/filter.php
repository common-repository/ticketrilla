<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$filter_items = $this->data;
	if ( is_array( $filter_items ) ) {
		$filter_items = array_merge( array(
			'all' => array(
				'label' => __( 'All', TTLC_TEXTDOMAIN ),
			),
		), $filter_items );
		$active_filter_key = isset( $_GET['filter'] ) && array_key_exists( $_GET['filter'], $filter_items ) ? sanitize_text_field( $_GET['filter'] ) : 'all';
?>

		<div class="ttlc__filter"><span><?php esc_html_e( 'Show', TTLC_TEXTDOMAIN ); ?>:</span>
			<ul>
			<?php
				foreach( $filter_items as $filter_key => $filter_item ) {
					$filter_url = $filter_key === 'all' ? remove_query_arg( 'filter' ) : add_query_arg( 'filter', $filter_key, remove_query_arg( 'page_num' ) );
					$item_html = '<li';
					if ( $active_filter_key === $filter_key ) {
						$item_html .= ' class="active"';
					}
					$item_html .= '><a href="' . esc_url( $filter_url ) . '">' . wp_kses( $filter_item['label'], array(
						'i' => array(
							'class' => array(),
						),
					) ) . '</a></li>';
					echo wp_kses_post( $item_html );
				}
			?>
			</ul>
		</div>

<?php } ?>