<?php

	// Exit if accessed directly.
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	$product_post = get_post( sanitize_key( $_GET['product_id'] ) );
	if ( $product_post ) {
		$product = new TTLC_Product( $product_post );
		$ticket_list_processor = new TTLC_Ticket_List_Processor( $product );
		$ticket_list = $ticket_list_processor->result['items'];	
		$title = $product->title;
		TTLC_Breadcrumbs::add_head( $title );
		$this->render_template('header');

?>

	<div class="ttlc__header-title">
		<h1><?php echo esc_html( sprintf( __( 'Tickets: %s', TTLC_TEXTDOMAIN ), $title ) ); ?></h1><a href="<?php echo esc_url( add_query_arg( array( 'action' => 'ticket' ) ) ); ?>" class="btn btn-info"><?php esc_html_e( 'New Ticket', TTLC_TEXTDOMAIN ); ?></a>
	</div>
<?php
	
	$this->render_template( 'filter', array(
		'replied' => array(
			'label' => esc_html__( 'Needs Attention', TTLC_TEXTDOMAIN ),
		),
		'pending' => array(
			'label' => esc_html__( 'Pending', TTLC_TEXTDOMAIN ),
		),
		'closed' => array(
			'label' => esc_html__( 'Closed', TTLC_TEXTDOMAIN ),
		),
	) );

?>
</div>

<div class="ttlc__content">
	<div class="ttlc__tickets">
		<div class="ttlc__tickets-inner">
		<?php
			if ( empty( $ticket_list ) ) {

				esc_html_e( 'No Tickets Found', TTLC_TEXTDOMAIN );
				
			} else {

				$table_tickets = new TTLC_Table_Helper(
					'tickets',
					array(
						'status' => array(
							'label' => esc_html__( 'Status', TTLC_TEXTDOMAIN ),
							'value' => function( $data ) {
								switch ( $data->status ) {
									case 'pending':	$label = 'warning'; break;
									case 'replied':	$label = 'success'; break;
									case 'closed': 	$label = 'danger'; break;
									default:	    $label = 'info';
								}
								$statuses = $data->statuses();
								return '<span class="btn btn-block btn-xs btn-' . $label . '">' . esc_html( $statuses[$data->status] ) . '</span>';
							}
						),
						'title' => array(
							'label' => esc_html__( 'Title', TTLC_TEXTDOMAIN ),
							'value' => function( $data ) {
								return sprintf( '<a class="ttlc__tickets-url" href="%s">%s</a>', esc_url( add_query_arg( array('action' => 'ticket', 'ticket_id' => $data->external_id ) ) ), esc_html( sprintf( '#%s. %s', $data->external_id, stripslashes( $data->title ) ) ) );
							}
						),
						'response_date' => array(
							'label' => esc_html__( 'Last Response', TTLC_TEXTDOMAIN ),
							'value' => function( $data ) {
								return '<span class="ttlc__tickets-response">' . esc_html( $data->responses_count ) . '</span><span class="text-muted"> : </span><span>' . get_date_from_gmt( $data->last_response, 'd F Y, H:i' ) . '</span>';
								}
						),
						'attachments' => array(
							'label' => esc_html__( 'Attachments', TTLC_TEXTDOMAIN ),
							'value' => function( $data ) {
								return '<div class="ttlc__tickets-attach btn-block">' . esc_html( $data->get_attachments_count() ) . '</div>';
							}
						),
					),
					$ticket_list
				);
				$table_tickets->render( 'table table-striped' );

			}
		?>
		</div>
		<?php
			
			if ( ! empty( $ticket_list ) ) {
				
				$pagination = new TTLC_Pagination( $ticket_list_processor->result['total'] );
				$pagination->render();
				
			}
		?>
	</div>
</div>
<?php
	$this->render_template('footer');
	}
?>
