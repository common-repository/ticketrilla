<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$pagination = $this->data;
?>

	<nav aria-label="<?php esc_attr_e( 'Page navigation', TTLC_TEXTDOMAIN ) ?>" class="text-center">
		<ul class="pagination ttlc-pagination">
<?php 
	
	if ( $pagination->prev ) {

		echo '<li><a href="' . esc_url( $pagination->prev ) . '" aria-label="' . esc_html__( 'Previous', TTLC_TEXTDOMAIN ) . '"><span aria-hidden="true">&larr;</span></a></li>';
		
	}

	for( $i = 1; $i <= $pagination->pages_count; $i++ ) {
		
		if ( $pagination->active === $i ) {
?>
		<li class="active"><a href="#"><?php echo esc_html( $i ); ?></a></li>
<?php
		
		} else {
			echo '<li><a href="' . esc_url( add_query_arg( 'page_num', $i ) ) . '">' . esc_html( $i ) . '</a></li>';
		}

	}

	if ( $pagination->next ) {

		echo '<li><a href="' . esc_url( $pagination->next ) . '" aria-label="' . esc_html__( 'Next', TTLC_TEXTDOMAIN ) . '"><span aria-hidden="true">&rarr;</span></a></li>';

	} ?>
		</ul>
	</nav>
