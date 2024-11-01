<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$pagination = $this->data;

?>

		<ul class="pagination ttlc-pagination-ticket">
<?php 
	
	if ( $pagination->prev ) {
?>
		<li><a href="<?php echo esc_url( remove_query_arg( 'load_more', $pagination->prev ) ); ?>" aria-label="<?php esc_attr_e( 'Previous', TTLC_TEXTDOMAIN ) ?>"><span aria-hidden="true">&larr;</span></a></li>
<?php		
	}

	for( $i = 1; $i <= $pagination->pages_count; $i++ ) {
		
		if ( $pagination->active === $i ) {
?>
		<li class="active"><a href="#"><?php echo esc_html( $i ); ?></a></li>
<?php
		
		} else {
			echo '<li><a href="' . esc_url( remove_query_arg( 'load_more', add_query_arg( 'page_num', $i ) ) ) . '">' . esc_html( $i ) . '</a></li>';
		}

	}

	if ( $pagination->next ) {

?>
		<li><a href="<?php echo esc_url( remove_query_arg( 'load_more', $pagination->next ) ); ?>" aria-label="<?php esc_attr_e( 'Next', TTLC_TEXTDOMAIN ) ?>"><span aria-hidden="true">&rarr;</span></a></li>
		<li><a href="<?php echo esc_url( add_query_arg( 'load_more', $pagination->active + 1, $pagination->next) ) ?>" class="btn btn-info ttlc-load-more-ticket"><?php esc_html_e( 'Load more', TTLC_TEXTDOMAIN ) ?></a></li>

<?php } ?>
			
		</ul>
