<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

?>
        <ul class="breadcrumb">
	        <?php
		    	foreach ( TTLC_Breadcrumbs::get_links() as $index => $link ) {
			    	$title = esc_html( $link['title'] );
			    	echo '<li><a href="' . esc_url( $link['url'] ) . '">' . ( $index == 0 ? '<i class="fa fa-dashboard"></i> ' : '' ) . esc_html( $title ) . '</a></li>';
		    	}
		    
		    ?>
		    <?php echo '<li class="active">' . esc_html( stripslashes( TTLC_Breadcrumbs::get_head() ) ) . '</li>'; ?>
            
        </ul>